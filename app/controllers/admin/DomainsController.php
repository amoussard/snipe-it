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

class DomainsController extends AdminController
{
    /**
     * Show a list of all the domains.
     *
     * @return View
     */

    public function getIndex()
    {
        // Grab all the locations
        $domains = Domain::orderBy('created_at', 'DESC')->paginate(Setting::getSettings()->per_page);

        // Show the page
        return View::make('backend/domains/index', compact('domains'));
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