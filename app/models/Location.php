<?php

class Location extends Elegant
{
    const NUMEDIA_ID = 8;
    const EXABIT_ID = 1621;

    protected $softDelete = true;
    protected $table = 'locations';
    protected $rules = array(
        'name'  		=> 'required|alpha_space|min:3',
        'address'		=> 'required|alpha_space|min:5',
        'address2'		=> 'alpha_space|min:5',
        'city'   		=> 'required|alpha_space|min:3',
        'state'   		=> 'required|alpha|min:2',
        'country'   	=> 'required|alpha|min:2|max:2',
        'zip'   		=> 'alpha_dash|min:3',
        'domain_id'     => 'required|integer',
    );

    public function has_users()
    {
        return $this->hasMany('User', 'location_id')->count();
    }

    public function getAssets()
    {
        return $this->hasMany('Asset', 'location_id')->withTrashed();
    }

}
