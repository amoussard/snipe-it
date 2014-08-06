<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Location;
use Domain;
use Redirect;
use Setting;
use DB;
use Sentry;
use Str;
use Validator;
use View;
use Response;

class LocationsController extends AdminController
{
    /**
     * Show a list of all the locations.
     *
     * @return View
     */

    public function getIndex()
    {
        // Show the page
        return View::make('backend/locations/index');
    }

    /**
     *
     * @return Response
     */
    public function getJsonList()
    {
        $perPage = Input::get('iDisplayLength');
        $iPage = Input::get('iDisplayStart') / $perPage;
        $locationQuery = DB::table('locations')
            ->select(
                'locations.id as id',
                'locations.name as name',
                DB::raw('CONCAT(locations.address, ", ", locations.address2) AS address'),
                DB::raw('CONCAT(locations.city, ", ", locations.state, " ", locations.country) AS city')
            )
            ->skip($iPage * $perPage)
            ->take($perPage);

        /*
         * Filters
         */
        $locationName = Input::get('locationName');
        if (!empty($locationName)) {
            $locationQuery->where('locations.name', 'LIKE', '%'.$locationName.'%');
        }

        /*
         * Orders
         */
        switch (Input::get('iSortCol_0')) {
            // Name
            case 0:
                $locationQuery->orderBy('locations.name', Input::get('sSortDir_0'));
                break;
            default:
                $locationQuery->orderBy('locations.name', 'asc');
                break;
        }

        $locations = $locationQuery->get();
        $totalLocationsCount = $locationQuery->count();

        $aResults = array();
        foreach ($locations as $location) {
            $aResults[] = array(
                '<a href="/admin/settings/locations/'.$location->id.'/view">'.htmlentities($location->name).'</a>',
                htmlentities($location->address),
                htmlentities($location->city),
                '<a href="/admin/settings/locations/'.$location->id.'/edit" class="btn btn-warning">
                    <i class="icon-pencil icon-white"></i>
                 </a>
                 <a class="btn delete btn-danger" href="/admin/settings/locations/'.$location->id.'/delete"
                    data-content="'.Lang::get('admin/locations/message.delete.confirm').'"
                    data-title="'.Lang::get('general.delete').' '.htmlspecialchars($location->name).' ?">
                    <i class="icon-trash icon-white"></i>
                 </a>'
            );
        }

        $aResponse = array(
            'draw' => Input::get('sEcho'),
            'recordsTotal' => $totalLocationsCount,
            'recordsFiltered' => $totalLocationsCount,
            'data' => $aResults,
        );

        return Response::json($aResponse);
    }


    /**
     * Location create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
        $location_options = array('0' => 'Top Level') + Location::lists('name', 'id');
        $domain_list = array('' => '') + Domain::lists('name', 'id');

        return View::make('backend/locations/edit')
            ->with('location_options',$location_options)
            ->with('location',new Location)
            ->with('domain_list', $domain_list);
    }


    /**
     * Location create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // get the POST data
        $new = Input::all();

        // create a new location instance
        $location = new Location();

        // attempt validation
        if ($location->validate($new)) {

            // Save the location data
            $location->name            	= e(Input::get('name'));
            $location->address			= e(Input::get('address'));
            $location->address2			= e(Input::get('address2'));
            $location->city    			= e(Input::get('city'));
            $location->state    		= e(Input::get('state'));
            $location->country    		= e(Input::get('country'));
            $location->zip    		= e(Input::get('zip'));
            $location->user_id          = Sentry::getId();

            // Was the asset created?
            if($location->save()) {
                // Redirect to the new location  page
                return Redirect::to("admin/settings/locations")->with('success', Lang::get('admin/locations/message.create.success'));
            }
        } else {
            // failure
            $errors = $location->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the location create page
        return Redirect::to('admin/settings/locations/create')->with('error', Lang::get('admin/locations/message.create.error'));
    }


    /**
     * Location update.
     *
     * @param  int  $locationId
     * @return View
     */
    public function getEdit($locationId = null)
    {
        // Check if the location exists
        if (is_null($location = Location::find($locationId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/locations')->with('error', Lang::get('admin/locations/message.does_not_exist'));
        }

        // Show the page
        $domain_list = array('' => '') + Domain::lists('name', 'id');

        $location_options = array('' => 'Top Level') + DB::table('locations')->where('id', '!=', $locationId)->lists('name', 'id');
        return View::make('backend/locations/edit', compact('location'))
            ->with('location_options', $location_options)
            ->with('domain_list', $domain_list);
    }


    /**
     * Location update form processing page.
     *
     * @param  int  $locationId
     * @return Redirect
     */
    public function postEdit($locationId = null)
    {
        // Check if the location exists
        if (is_null($location = Location::find($locationId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/locations')->with('error', Lang::get('admin/locations/message.does_not_exist'));
        }

        // get the POST data
        $new = Input::all();

        // attempt validation
        if ($location->validate($new)) {

            // Update the location data
            $location->name            	= e(Input::get('name'));
            $location->address			= e(Input::get('address'));
            $location->address2			= e(Input::get('address2'));
            $location->city    			= e(Input::get('city'));
            $location->state    		= e(Input::get('state'));
            $location->country    		= e(Input::get('country'));
            $location->zip    		    = e(Input::get('zip'));
            $location->domain_id 		= e(Input::get('domain_id'));

            // Was the asset created?
            if($location->save()) {
                // Redirect to the saved location page
                return Redirect::to("admin/settings/locations/$locationId/edit")
                    ->with('success', Lang::get('admin/locations/message.update.success'));
            }
        } else {
            // failure
            $errors = $location->errors();
            return Redirect::back()
                ->withInput()
                ->withErrors($errors);
        }

        // Redirect to the location management page
        return Redirect::to("admin/settings/locations/$locationId/edit")->with('error', Lang::get('admin/locations/message.update.error'));

    }

    /**
     * Delete the given location.
     *
     * @param  int  $locationId
     * @return Redirect
     */
    public function getDelete($locationId)
    {
        // Check if the location exists
        if (is_null($location = Location::find($locationId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/locations')->with('error', Lang::get('admin/locations/message.not_found'));
        }


        if ($location->has_users() > 0) {

            // Redirect to the asset management page
            return Redirect::to('admin/settings/locations')->with('error', Lang::get('admin/locations/message.assoc_users'));
        } else {

            $location->delete();

            // Redirect to the locations management page
            return Redirect::to('admin/settings/locations')->with('success', Lang::get('admin/locations/message.delete.success'));
        }



    }

    /**
     * Get location info for location view
     *
     * @param  int  $locationId
     * @return View
     */
    public function getView($locationId = null)
    {
        // Check if the location exists
        if (is_null($location = Location::find($locationId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/locations')->with('error', Lang::get('admin/locations/message.not_found'));
        }

        return View::make('backend/locations/view', compact('location'));
    }



}
