<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Statuslabel;
use Redirect;
use DB;
use Sentry;
use Setting;
use Str;
use Validator;
use View;
use Response;

class StatuslabelsController extends AdminController
{
    /**
     * Show a list of all the statuslabels.
     *
     * @return View
     */

    public function getIndex()
    {
        // Show the page
        return View::make('backend/statuslabels/index', compact('statuslabels'));
    }

    /**
     *
     * @return Response
     */
    public function getJsonList()
    {
        $perPage = Input::get('iDisplayLength');
        $iPage = Input::get('iDisplayStart') / $perPage;
        $statusQuery = DB::table('status_labels')
            ->select(
                'status_labels.id as id',
                'status_labels.name as name'
            )
            ->where('status_labels.deleted_at', '=', NULL)
            ->skip($iPage * $perPage)
            ->take($perPage);

        /*
         * Filters
         */
        $statusName = Input::get('statusLabelName');
        if (!empty($statusName)) {
            $statusQuery->where('status_labels.name', 'LIKE', '%'.$statusName.'%');
        }

        /*
         * Orders
         */
        switch (Input::get('iSortCol_0')) {
            // Name
            case 0:
                $statusQuery->orderBy('status_labels.name', Input::get('sSortDir_0'));
                break;
            default:
                $statusQuery->orderBy('status_labels.name', 'asc');
                break;
        }

        $statusLabels = $statusQuery->get();
        $totalStatusLabelsCount = $statusQuery->count();

        $aResults = array();
        foreach ($statusLabels as $statusLabel) {
            $aResults[] = array(
                '<a href="/admin/settings/statuslabels/'.$statusLabel->id.'/view">'.htmlentities($statusLabel->name).'</a>',
                '<a href="/admin/settings/statuslabels/'.$statusLabel->id.'/edit" class="btn btn-warning">
                    <i class="icon-pencil icon-white"></i>
                 </a>
                 <a class="btn delete btn-danger" href="/admin/settings/statuslabels/'.$statusLabel->id.'/delete"
                    data-content="'.Lang::get('admin/statuslabels/message.delete.confirm').'"
                    data-title="'.Lang::get('general.delete').' '.htmlspecialchars($statusLabel->name).' ?">
                    <i class="icon-trash icon-white"></i>
                 </a>'
            );
        }

        $aResponse = array(
            'draw' => Input::get('sEcho'),
            'recordsTotal' => $totalStatusLabelsCount,
            'recordsFiltered' => $totalStatusLabelsCount,
            'data' => $aResults,
        );

        return Response::json($aResponse);
    }

    /**
     * Statuslabel create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
        return View::make('backend/statuslabels/edit')->with('statuslabel',new Statuslabel);
    }


    /**
     * Statuslabel create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // get the POST data
        $new = Input::all();

        // create a new model instance
        $statuslabel = new Statuslabel();

        // attempt validation
        if ($statuslabel->validate($new)) {

            // Save the Statuslabel data
            $statuslabel->name            	= e(Input::get('name'));
            $statuslabel->user_id          = Sentry::getId();

            // Was the asset created?
            if($statuslabel->save()) {
                // Redirect to the new Statuslabel  page
                return Redirect::to("admin/settings/statuslabels")->with('success', Lang::get('admin/statuslabels/message.create.success'));
            }
        } else {
            // failure
            $errors = $statuslabel->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the Statuslabel create page
        return Redirect::to('admin/settings/statuslabels/create')->with('error', Lang::get('admin/statuslabels/message.create.error'));

    }


    /**
     * Statuslabel update.
     *
     * @param  int  $statuslabelId
     * @return View
     */
    public function getEdit($statuslabelId = null)
    {
        // Check if the Statuslabel exists
        if (is_null($statuslabel = Statuslabel::find($statuslabelId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/statuslabels')->with('error', Lang::get('admin/statuslabels/message.does_not_exist'));
        }


        return View::make('backend/statuslabels/edit', compact('statuslabel'));
    }


    /**
     * Statuslabel update form processing page.
     *
     * @param  int  $statuslabelId
     * @return Redirect
     */
    public function postEdit($statuslabelId = null)
    {
        // Check if the Statuslabel exists
        if (is_null($statuslabel = Statuslabel::find($statuslabelId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/statuslabels')->with('error', Lang::get('admin/statuslabels/message.does_not_exist'));
        }



        // get the POST data
        $new = Input::all();


        // attempt validation
        if ($statuslabel->validate($new)) {

            // Update the Statuslabel data
            $statuslabel->name            	= e(Input::get('name'));

            // Was the asset created?
            if($statuslabel->save()) {
                // Redirect to the saved Statuslabel page
                return Redirect::to("admin/settings/statuslabels/$statuslabelId/edit")->with('success', Lang::get('admin/statuslabels/message.update.success'));
            }
        } else {
            // failure
            $errors = $statuslabel->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the Statuslabel management page
        return Redirect::to("admin/settings/statuslabels/$statuslabelId/edit")->with('error', Lang::get('admin/statuslabels/message.update.error'));

    }

    /**
     * Delete the given Statuslabel.
     *
     * @param  int  $statuslabelId
     * @return Redirect
     */
    public function getDelete($statuslabelId)
    {
        // Check if the Statuslabel exists
        if (is_null($statuslabel = Statuslabel::find($statuslabelId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/statuslabels')->with('error', Lang::get('admin/statuslabels/message.not_found'));
        }


        if ($statuslabel->has_assets() > 0) {

            // Redirect to the asset management page
            return Redirect::to('admin/settings/statuslabels')->with('error', Lang::get('admin/statuslabels/message.assoc_users'));
        } else {

            $statuslabel->delete();

            // Redirect to the statuslabels management page
            return Redirect::to('admin/settings/statuslabels')->with('success', Lang::get('admin/statuslabels/message.delete.success'));
        }



    }



}
