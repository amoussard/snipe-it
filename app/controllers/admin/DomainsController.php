<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Domain;
use Redirect;
use Setting;
use DB;
use Sentry;
use Str;
use Validator;
use View;
use Response;

class DomainsController extends AdminController
{
    /**
     * Show a list of all the domains.
     *
     * @return View
     */
    public function getIndex()
    {
        // Show the page
        return View::make('backend/domains/index');
    }

    /**
     *
     * @return Response
     */
    public function getJsonList()
    {
        $perPage = Input::get('iDisplayLength');
        $iPage = Input::get('iDisplayStart') / $perPage;
        $domainQuery = DB::table('domains')
            ->select(
                'domains.id as id',
                'domains.name as name'
            )
            ->where('domains.deleted_at', '=', NULL)
            ->skip($iPage * $perPage)
            ->take($perPage);

        /*
         * Filters
         */
        $domainName = Input::get('domainName');
        if (!empty($domainName)) {
            $domainQuery->where('domains.name', 'LIKE', '%'.$domainName.'%');
        }

        /*
         * Orders
         */
        switch (Input::get('iSortCol_0')) {
            // Name
            case 0:
                $domainQuery->orderBy('domains.name', Input::get('sSortDir_0'));
                break;
            default:
                $domainQuery->orderBy('domains.name', 'asc');
                break;
        }

        $domains = $domainQuery->get();
        $totalDomainsCount = $domainQuery->count();

        $aResults = array();
        foreach ($domains as $domain) {
            $aResults[] = array(
                '<a href="/admin/settings/domains/'.$domain->id.'/view">'.htmlentities($domain->name).'</a>',
                '<a href="/admin/settings/domains/'.$domain->id.'/edit" class="btn btn-warning">
                    <i class="icon-pencil icon-white"></i>
                 </a>
                 <a class="btn delete btn-danger" href="/admin/settings/domains/'.$domain->id.'/delete"
                    data-content="'.Lang::get('admin/domains/message.delete.confirm').'"
                    data-title="'.Lang::get('general.delete').' '.htmlspecialchars($domain->name).' ?">
                    <i class="icon-trash icon-white"></i>
                </a>'
            );
        }

        $aResponse = array(
            'draw' => Input::get('sEcho'),
            'recordsTotal' => $totalDomainsCount,
            'recordsFiltered' => $totalDomainsCount,
            'data' => $aResults,
        );

        return Response::json($aResponse);
    }


    /**
     * Domain create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
        $domain_options = array('0' => 'Top Level') + Domain::lists('name', 'id');

        return View::make('backend/domains/edit')
            ->with('domain_options', $domain_options)
            ->with('domain', new Domain());
    }


    /**
     * Domain create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {
        // get the POST data
        $new = Input::all();

        // create a new domain instance
        $domain = new Domain();

        // attempt validation
        if ($domain->validate($new)) {

            // Save the location data
            $domain->name       = e(Input::get('name'));

            // Was the domain created?
            if($domain->save()) {
                // Redirect to the new location  page
                return Redirect::to("admin/settings/domains")
                    ->with('success', Lang::get('admin/domains/message.create.success'));
            }
        } else {
            // failure
            $errors = $domain->errors();
            return Redirect::back()
                ->withInput()
                ->withErrors($errors);
        }

        // Redirect to the location create page
        return Redirect::to('admin/settings/domains/create')
            ->with('error', Lang::get('admin/domains/message.create.error'));

    }


    /**
     * Domain update.
     *
     * @param  int  $domainId
     * @return View
     */
    public function getEdit($domainId = null)
    {
        // Check if the location exists
        if (is_null($domain = Domain::find($domainId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/domains')
                ->with('error', Lang::get('admin/domains/message.does_not_exist'));
        }

        $domainOptions = array('' => 'Top Level') + DB::table('domains')->where('id', '!=', $domainId)->lists('name', 'id');

        return View::make('backend/domains/edit', compact('domain'))->with('domainOptions',$domainOptions);
    }


    /**
     * Domain update form processing page.
     *
     * @param  int  $domainId
     * @return Redirect
     */
    public function postEdit($domainId = null)
    {
        // Check if the location exists
        if (is_null($domain = Domain::find($domainId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/domains')
                ->with('error', Lang::get('admin/domains/message.does_not_exist'));
        }

        // get the POST data
        $new = Input::all();

        // attempt validation
        if ($domain->validate($new)) {

            // Update the location data
            $domain->name   = e(Input::get('name'));

            // Was the asset created?
            if($domain->save()) {
                // Redirect to the saved location page
                return Redirect::to("admin/settings/domains/$domainId/edit")
                    ->with('success', Lang::get('admin/domains/message.update.success'));
            }
        } else {
            // failure
            $errors = $domain->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the location management page
        return Redirect::to("admin/settings/domains/$domainId/edit")
            ->with('error', Lang::get('admin/domains/message.update.error'));

    }

    /**
     * Delete the given domain.
     *
     * @param  int  $domainId
     * @return Redirect
     */
    public function getDelete($domainId)
    {
        // Check if the location exists
        if (is_null($domain = Domain::find($domainId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/domains')->with('error', Lang::get('admin/domains/message.not_found'));
        }

        $domain->delete();

        // Redirect to the locations management page
        return Redirect::to('admin/settings/domains')->with('success', Lang::get('admin/domains/message.delete.success'));
    }

    /**
     * Get domain info for domain view
     *
     * @param  int  $domainId
     * @return View
     */
    public function getView($domainId = null)
    {
        // Check if the location exists
        if (is_null($domain = Domain::find($domainId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/domains')->with('error', Lang::get('admin/domains/message.not_found'));
        }

        return View::make('backend/domains/view', compact('domain'));
    }
}
