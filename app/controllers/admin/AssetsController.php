<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Asset;
use Supplier;
use Statuslabel;
use User;
use Setting;
use Redirect;
use DB;
use Actionlog;
use Model;
use Depreciation;
use Sentry;
use Str;
use Validator;
use View;
use Response;
use Config;
use Location;
use Log;

use BaconQrCode\Renderer\Image as QrImage;

class AssetsController extends AdminController
{
    protected $qrCodeDimensions = array( 'height' => 170, 'width' => 170);

    /**
     * Show a list of all the assets.
     *
     * @return View
     */

    public function getIndex()
    {
        $model_list = array('-1' => 'Choose a model') + Model::orderBy('name', 'asc')->lists('name', 'id');
        $status_list = array('-1' => 'Choose a status') + Statuslabel::orderBy('name', 'asc')->lists('name', 'id');

        return View::make('backend/hardware/index')
            ->with('model_list', $model_list)
            ->with('status_list', $status_list);
    }

    /**
     *
     * @return Response
     */
    public function getJsonList()
    {
        $perPage = Input::get('iDisplayLength');
        $iPage = Input::get('iDisplayStart') / $perPage;
        $assetQuery = DB::table('assets')
            ->select(
                'assets.id as id',
                'assets.asset_tag as mac_address',
                'assets.name as name',
                'models.id as model_id',
                'models.name as model_name',
                'status_labels.id as status_id',
                'status_labels.name as status_name',
                'locations.name as location_name',
                'locations.id as location_id'
            )
            ->leftJoin('models', 'models.id', '=', 'assets.model_id')
            ->leftJoin('locations', 'locations.id', '=', 'assets.location_id')
            ->leftJoin('status_labels', 'status_labels.id', '=', 'assets.status_id')
            ->where('assets.deleted_at', '=', NULL)
            ->skip($iPage * $perPage)
            ->take($perPage);

        /*
         * Filters
         */
        $assetMac = Input::get('assetMac');
        if (!empty($assetMac)) {
            $assetQuery->where('assets.asset_tag', 'LIKE', '%'.$assetMac.'%');
        }
        $assetName = Input::get('assetName');
        if (!empty($assetName)) {
            $assetQuery->where('assets.name', 'LIKE', '%'.$assetName.'%');
        }
        $assetModel = Input::get('assetModel');
        if (!empty($assetModel) && $assetModel != -1) {
            $assetQuery->where('models.id', '=', $assetModel);
        }
        $assetStatus = Input::get('assetStatus');
        if (!empty($assetStatus) && $assetStatus != -1) {
            $assetQuery->where('assets.status_id', '=', $assetStatus);
        }
        $assetLocation = Input::get('assetLocation');
        if (!empty($assetLocation)) {
            $assetQuery->where('locations.name', 'LIKE', '%'.$assetLocation.'%');
        }
        $assetNumedia = Input::get('assetNumedia');
        if ($assetNumedia == 1) {
            $assetQuery->where('locations.id', '=', Location::NUMEDIA_ID);
        }

        /*
         * Orders
         */
        switch (Input::get('iSortCol_0')) {
            // MAC address
            case 0:
                $assetQuery->orderBy('assets.asset_tag', Input::get('sSortDir_0'));
                break;
            // Name
            case 1:
                $assetQuery->orderBy('assets.name', Input::get('sSortDir_0'));
                break;
            // Model
            case 2:
                $assetQuery->orderBy('models.name', Input::get('sSortDir_0'));
                break;
            // Status
            case 3:
                $assetQuery->orderBy('assets.status_name', Input::get('sSortDir_0'));
                break;
            // Location
            case 4:
                $assetQuery->orderBy('locations.name', Input::get('sSortDir_0'));
                break;
            default:
                $assetQuery->orderBy('assets.name', 'asc');
                break;
        }

        $assets = $assetQuery->get();
        $totalAssetsCount = $assetQuery->count();

        $aResults = array();
        foreach ($assets as $asset) {
            $aTmp = array(
                '<a href="/hardware/'.$asset->id.'/view">'.htmlentities($asset->mac_address).'</a>',
                '<a href="/hardware/'.$asset->id.'/view">'.htmlentities($asset->name).'</a>',
                '<a href="/hardware/models/'.$asset->model_id.'/view">'.htmlentities($asset->model_name).'</a>',
                $asset->status_id
                    ? '<span class="btn '.Statuslabel::$statusClass[$asset->status_id].'">'.$asset->status_name.'</span>'
                    : null,
                '<a href="/admin/settings/locations/'.$asset->location_id.'/view">'.htmlentities($asset->location_name).'</a>',
            );

            if (in_array($asset->status_id, Statuslabel::$checkinStatus) && $asset->location_id != Location::NUMEDIA_ID) {
                $aTmp[] = '<a href="/hardware/'.$asset->id.'/checkin" class="btn btn-primary">checkin</a>';
            } elseif(in_array($asset->status_id, Statuslabel::$checkoutStatus) && $asset->location_id == Location::NUMEDIA_ID) {
                $aTmp[] = '<a href="/hardware/'.$asset->id.'/checkout" class="btn btn-info">checkout</a>';
            } elseif(in_array($asset->status_id, Statuslabel::$repareStatus) && $asset->location_id == Location::NUMEDIA_ID) {
                $aTmp[] = '<a href="/hardware/'.$asset->id.'/repare" class="btn btn-warning">repare</a>';
            } else {
                $aTmp[] = null;
            }

            $aTmp[] = '
                <a href="/hardware/'.$asset->id.'/edit" class="btn btn-warning">
                    <i class="icon-pencil icon-white"></i>
                </a>
                <a class="btn delete btn-danger" href="/hardware/'.$asset->id.'/delete"
                    data-title="'.Lang::get('general.delete').' '.htmlspecialchars($asset->mac_address).' ?"
                    data-content="'.Lang::get('admin/hardware/message.delete.confirm').'">
                    <i class="icon-trash icon-white"></i>
                </a>';

            $aResults[] = $aTmp;
        }

        $aResponse = array(
            'draw' => Input::get('sEcho'),
            'recordsTotal' => $totalAssetsCount,
            'recordsFiltered' => $totalAssetsCount,
            'data' => $aResults,
        );

        return Response::json($aResponse);
    }

    public function getReports()
    {
        // Grab all the assets
        $assets = Asset::orderBy('created_at', 'DESC')->get();
        return View::make('backend/reports/index', compact('assets'));
    }

    public function exportReports()
    {
        // @todo - It may be worthwhile creating a separate controller for reporting

        // Grab all the assets
        $assets = Asset::orderBy('created_at', 'DESC')->get();

        $rows = array();

        // Create the header row
        $header = array(
            Lang::get('admin/hardware/table.asset_tag'),
            Lang::get('admin/hardware/table.title'),
            Lang::get('admin/hardware/table.serial'),
            Lang::get('admin/hardware/table.checkoutto'),
            Lang::get('admin/hardware/table.location'),
            Lang::get('admin/hardware/table.purchase_date'),
            Lang::get('admin/hardware/table.purchase_cost'),
            Lang::get('admin/hardware/table.book_value'),
            Lang::get('admin/hardware/table.diff')
        );
        $header = array_map('trim', $header);
        $rows[] = implode($header, ',');

        // Create a row per asset
        foreach ($assets as $asset) {
            $row = array();
            $row[] = $asset->asset_tag;
            $row[] = $asset->name;
            $row[] = $asset->serial;


            if ($asset->assigned_to > 0) {
              $user = User::find($asset->assigned_to);
              $row[] = $user->fullName();
              } else {
                $row[] = ''; // Empty string if unassigned
            }

            if (($asset->assigned_to > 0) && ($asset->assigneduser->location_id > 0)) {
                $location = Location::find($asset->assigneduser->location_id);
                if ($location->city) {
                    $row[] = '"'.$location->city . ', ' . $location->state.'"';
                } elseif ($location->name) {
                    $row[] = $location->name;
                } else {
                    $row[] = '';
                }
            } else {
                $row[] = '';  // Empty string if location is not set
            }

            $depreciation = $asset->depreciate();

            $row[] = $asset->purchase_date;
            $row[] = '"'.number_format($asset->purchase_cost).'"';
            $row[] = '"'.number_format($depreciation).'"';
            $row[] = '"'.number_format($asset->purchase_cost - $depreciation).'"';
            $rows[] = implode($row, ',');
        }

        // spit out a csv
        $csv = implode($rows, "\n");
        $response = Response::make($csv, 200);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-disposition', 'attachment;filename=report.csv');

        return $response;
    }

    /**
     * Asset create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Grab the dropdown list of models
        $model_list = array('' => '') + Model::orderBy('name', 'asc')->lists('name', 'id');
        $supplier_list = array('' => '') + Supplier::orderBy('name', 'asc')->lists('name', 'id');
        $location_list = array('' => '') + Location::lists('name', 'id');
        $assigned_to = array('' => 'Select a User') + DB::table('users')->select(DB::raw('concat (first_name," ",last_name) as full_name, id'))->whereNull('deleted_at')->lists('full_name', 'id');
        $statuslabel_list = Statuslabel::orderBy('name', 'asc')->lists('name', 'id');

        return View::make('backend/hardware/edit')
            ->with('supplier_list',$supplier_list)
            ->with('model_list',$model_list)
            ->with('statuslabel_list',$statuslabel_list)
            ->with('assigned_to',$assigned_to)
            ->with('asset',new Asset)
            ->with('location_list', $location_list)
            ;
    }


    /**
     * Asset create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {
        // get the POST data
        $new = Input::all();

        // create a new model instance
        $asset = new Asset();

        // attempt validation
        if ($asset->validate($new)) {

            if (e(Input::get('warranty_months')) == '') {
                $asset->warranty_months =  NULL;
            } else {
                $asset->warranty_months        = e(Input::get('warranty_months'));
            }

            if (e(Input::get('purchase_cost')) == '') {
                $asset->purchase_cost =  NULL;
            } else {
                $asset->purchase_cost        = e(Input::get('purchase_cost'));
            }

            if (e(Input::get('purchase_date')) == '') {
                $asset->purchase_date =  NULL;
            } else {
                $asset->purchase_date        = e(Input::get('purchase_date'));
            }

            if (e(Input::get('assigned_to')) == '') {
                $asset->assigned_to =  0;
            } else {
                $asset->assigned_to        = e(Input::get('assigned_to'));
            }

            // Save the asset data
            $asset->name            		= e(Input::get('name'));
            $asset->serial            		= e(Input::get('serial'));
            $asset->model_id           		= e(Input::get('model_id'));
            $asset->order_number            = e(Input::get('order_number'));
            $asset->notes            		= e(Input::get('notes'));
            $asset->asset_tag            	= e(Input::get('asset_tag'));
            $asset->supplier_id            	= e(Input::get('supplier_id'));
            $asset->status_id               = e(Input::get('status_id'));
            $asset->user_id          		= Sentry::getId();
            $asset->archived          			= '0';
            $asset->physical            		= '1';
            $asset->depreciate          		= '0';
            $asset->requestable            	= e(Input::get('requestable'));

            // Was the asset created?
            if($asset->save()) {
                // Redirect to the asset listing page
                return Redirect::to("hardware")->with('success', Lang::get('admin/hardware/message.create.success'));
            }
        } else {
            // failure
            $errors = $asset->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the asset create page with an error
        return Redirect::to('assets/create')->with('error', Lang::get('admin/hardware/message.create.error'));
    }

    /**
     * Asset update.
     *
     * @param  int  $assetId
     * @return View
     */
    public function getEdit($assetId = null)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.does_not_exist'));
        }

        // Grab the dropdown list of models
        $model_list = array('' => '') + Model::orderBy('name', 'asc')->lists('name', 'id');
        $supplier_list = array('' => '') + Supplier::orderBy('name', 'asc')->lists('name', 'id');
        $location_list = array('' => '') + Location::lists('name', 'id');

        // Grab the dropdown list of status
        $statuslabel_list = Statuslabel::orderBy('name', 'asc')->lists('name', 'id');

        return View::make('backend/hardware/edit', compact('asset'))
            ->with('model_list', $model_list)
            ->with('supplier_list', $supplier_list)
            ->with('statuslabel_list', $statuslabel_list)
            ->with('location_list', $location_list)
        ;
    }


    /**
     * Asset update form processing page.
     *
     * @param  int  $assetId
     * @return Redirect
     */
    public function postEdit($assetId = null)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.does_not_exist'));
        }


        // Declare the rules for the form validation
        $rules = array(
            'name'   => 'alpha_space|min:3',
            'asset_tag'   => 'required|alpha_space|min:3',
            'model_id'   => 'required',
            'serial'   => 'alpha_space|min:3',
            'warranty_months'   => 'integer',
            'notes'   => 'alpha_space',
        );

        // Create a new validator instance from our validation rules
        $validator = Validator::make(Input::all(), $rules);

        // If validation fails, we'll exit the operation now.
        if ($validator->fails()) {
            // Ooops.. something went wrong
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if (e(Input::get('location_id')) == '') {
            $asset->location_id =  NULL;
        } else {
            $asset->location_id =  e(Input::get('location_id'));
        }

        if (e(Input::get('warranty_months')) == '') {
            $asset->warranty_months =  NULL;
        } else {
            $asset->warranty_months        = e(Input::get('warranty_months'));
        }

        if (e(Input::get('purchase_cost')) == '') {
            $asset->purchase_cost =  NULL;
        } else {
            $asset->purchase_cost        = e(Input::get('purchase_cost'));
        }

        if (e(Input::get('purchase_date')) == '') {
            $asset->purchase_date =  NULL;
        } else {
            $asset->purchase_date        = e(Input::get('purchase_date'));
        }

        if (e(Input::get('supplier_id')) == '') {
            $asset->supplier_id =  NULL;
        } else {
            $asset->supplier_id        = e(Input::get('supplier_id'));
        }


        // Update the asset data
        $asset->name            		= e(Input::get('name'));
        $asset->serial            		= e(Input::get('serial'));
        $asset->model_id           		= e(Input::get('model_id'));
        $asset->order_number            = e(Input::get('order_number'));
        $asset->asset_tag           	= e(Input::get('asset_tag'));
        $asset->notes            		= e(Input::get('notes'));
        $asset->requestable            	= e(Input::get('requestable'));
        $asset->status_id               = e(Input::get('status_id'));
        $asset->physical            	= '1';

        // Was the asset updated?
        if($asset->save()) {
            // Redirect to the new asset page
            return Redirect::to("hardware")->with('success', Lang::get('admin/hardware/message.update.success'));
        }

        // Redirect to the asset management page with error
        return Redirect::to("hardware/$assetId/edit")->with('error', Lang::get('admin/hardware/message.update.error'));
    }

    /**
     * Delete the given asset.
     *
     * @param  int  $assetId
     * @return Redirect
     */
    public function getDelete($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        if (isset($asset->assigneduser->id) && ($asset->assigneduser->id!=0)) {
            // Redirect to the asset management page
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.assoc_users'));
        } else {
            // Delete the asset
            $asset->delete();

            // Redirect to the asset management page
            return Redirect::to('hardware')->with('success', Lang::get('admin/hardware/message.delete.success'));
        }
    }

    /**
    * Check out the asset to a person
    **/
    public function getCheckout($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        // Get the dropdown of users and then pass it to the checkout view
        $location_list = array('' => '') + Location::lists('name', 'id');

        //print_r($users);
        return View::make('backend/hardware/checkout', compact('asset'))
            ->with('location_list', $location_list);
    }

    /**
     * Check out the asset to a person
     */
    public function postCheckout($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        $location_id = e(Input::get('location_id'));

        // Declare the rules for the form validation
        $rules = array(
            'location_id'   => 'required|integer',
            'note'          => 'alpha_space',
        );

        // Create a new validator instance from our validation rules
        $validator = Validator::make(Input::all(), $rules);

        // If validation fails, we'll exit the operation now.
        if ($validator->fails()) {
            // Ooops.. something went wrong
            return Redirect::back()->withInput()->withErrors($validator);
        }


        // Check if the user exists
        if (is_null($location = Location::find($location_id))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.location_does_not_exist'));
        }

        // Update the asset data
        $asset->location_id = e(Input::get('location_id'));
        $asset->status_id = Statuslabel::DEPLOYED;

        // Was the asset updated?
        if($asset->save()) {
            $logaction = new Actionlog();
            $logaction->asset_id = $asset->id;
            $logaction->checkedout_to = $asset->location_id;
            $logaction->asset_type = ASSET::TYPE_HARDWARE;
            $logaction->location_id = $location_id;
            $logaction->user_id = Sentry::getUser()->id;
            $logaction->note = e(Input::get('note'));
            $log = $logaction->logaction('checkout');

            // Redirect to the new asset page
            return Redirect::to("hardware")->with('success', Lang::get('admin/hardware/message.checkout.success'));
        }

        // Redirect to the asset management page with error
        return Redirect::to("hardware/$assetId/checkout")->with('error', Lang::get('admin/hardware/message.checkout.error'));
    }


    /**
    * Check the asset back into inventory
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getCheckin($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        return View::make('backend/hardware/checkin', compact('asset'));
    }


    /**
    * Check in the item so that it can be checked out again to someone else
    *
    * @param  int  $assetId
    * @return View
    **/
    public function postCheckin($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        // Check the asset back to numedia
        $asset->location_id = Location::NUMEDIA_ID;
        $asset->status_id = Statuslabel::TESTING;

        // Was the asset updated?
        if($asset->save()) {
            $logaction = new Actionlog();
            $logaction->checkedout_to = Location::NUMEDIA_ID;
            $logaction->asset_id = $asset->id;
            $logaction->location_id = Location::NUMEDIA_ID;
            $logaction->asset_type = ASSET::TYPE_HARDWARE;
            $logaction->note = e(Input::get('note'));
            $logaction->user_id = Sentry::getUser()->id;
            $log = $logaction->logaction('checkin from');

            // Redirect to the new asset page
            return Redirect::to("hardware")->with('success', Lang::get('admin/hardware/message.checkin.success'));
        }

        // Redirect to the asset management page with error
        return Redirect::to("hardware")->with('error', Lang::get('admin/hardware/message.checkin.error'));
    }

    /**
     * Send the asset to repare
     *
     * @param  int  $assetId
     * @return View
     **/
    public function getRepare($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        return View::make('backend/hardware/repare', compact('asset'));
    }


    /**
     * Check in the item so that it can be checked out again to someone else
     *
     * @param  int  $assetId
     * @return View
     **/
    public function postRepare($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page with error
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.not_found'));
        }

        // Check the asset back to numedia
        $asset->location_id = Location::EXABIT_ID;
        $asset->status_id = Statuslabel::OUT_FOR_REPAIR;

        // Was the asset updated?
        if($asset->save()) {
            $logaction = new Actionlog();
            $logaction->checkedout_to = Location::EXABIT_ID;
            $logaction->asset_id = $asset->id;
            $logaction->location_id = Location::EXABIT_ID;
            $logaction->asset_type = ASSET::TYPE_HARDWARE;
            $logaction->note = e(Input::get('note'));
            $logaction->user_id = Sentry::getUser()->id;
            $log = $logaction->logaction('Repare');

            // Redirect to the new asset page
            return Redirect::to("hardware")->with('success', Lang::get('admin/hardware/message.repare.success'));
        }

        // Redirect to the asset management page with error
        return Redirect::to("hardware")->with('error', Lang::get('admin/hardware/message.repare.error'));
    }


    /**
    *  Get the asset information to present to the asset view page
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getView($assetId = null)
    {
        $asset = Asset::find($assetId);

        if (isset($asset->id)) {

            $settings = Setting::getSettings();

            $qr_code = (object) array(
                'display' => $settings->qr_code == '1',
                'height' => $this->qrCodeDimensions['height'],
                'width' => $this->qrCodeDimensions['width'],
                'url' => route('qr_code/hardware', $asset->id)
            );

            return View::make('backend/hardware/view', compact('asset', 'qr_code'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/hardware/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('assets')->with('error', $error);
        }

    }

    /**
    *  Get the QR code representing the asset
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getQrCode($assetId = null)
    {
        $settings = Setting::getSettings();

        if ($settings->qr_code == '1') {
            $asset = Asset::find($assetId);
            if (isset($asset->id)) {
                $renderer = new \BaconQrCode\Renderer\Image\Png;
                $renderer->setWidth($this->qrCodeDimensions['height'])
                ->setHeight($this->qrCodeDimensions['height']);

                $writer = new \BaconQrCode\Writer($renderer);
                $content = $writer->writeString(route('view/hardware', $asset->id));

                $content_disposition = sprintf('attachment;filename=qr_code_%s.png', preg_replace('/\W/', '', $asset->asset_tag));
                $response = Response::make($content, 200);
                $response->header('Content-Type', 'image/png');
                $response->header('Content-Disposition', $content_disposition);
                return $response;
            }
        }

        $response = Response::make('', 404);
        return $response;
    }

    /**
     * Asset update.
     *
     * @param  int  $assetId
     * @return View
     */
    public function getClone($assetId = null)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find($assetId))) {
            // Redirect to the asset management page
            return Redirect::to('hardware')->with('error', Lang::get('admin/hardware/message.does_not_exist'));
        }

        // Grab the dropdown list of models
        $model_list = array('' => '') + Model::lists('name', 'id');

        // Grab the dropdown list of status
        $statuslabel_list = array('' => 'Pending') + array('0' => 'Ready to Deploy') + Statuslabel::lists('name', 'id');

        // get depreciation list
        $depreciation_list = array('' => '') + Depreciation::lists('name', 'id');

        return View::make('backend/hardware/clone', compact('asset'))->with('model_list',$model_list)->with('depreciation_list',$depreciation_list)->with('statuslabel_list',$statuslabel_list);
    }


}
