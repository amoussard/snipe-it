<?php

class Domain extends Elegant
{
    protected $softDelete = true;
    protected $table = 'domains';
    protected $rules = array(
        'name'  => 'required|alpha_space|min:3',
    );

    public function getLocations()
    {
        return $this->hasMany('Location', 'domain_id')->withTrashed();
    }

    public function getAssets()
    {
        return $this->hasManyThrough('Asset', 'Location')->withTrashed();
    }
}
