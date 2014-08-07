<?php

class Statuslabel extends Elegant
{
    const OUT_FOR_DIAGNOSTICS = 1;
    const OUT_FOR_REPAIR = 2;
    const BROKEN_NOT_FIXABLE = 3;
    const LOST_STOLEN = 4;
    const READY_TO_DEPLOY = 5;
    const TESTING = 6;
    const DEPLOYED = 7;

    public static $checkoutStatus = array(
        self::READY_TO_DEPLOY,
    );

    public static $checkinStatus = array(
        self::DEPLOYED,
        self::OUT_FOR_REPAIR,
    );

    public static $repareStatus = array(
        self::TESTING,
    );

    public static $statusClass = array(
        self::OUT_FOR_DIAGNOSTICS => 'btn-warning',
        self::OUT_FOR_REPAIR => 'btn-warning',
        self::BROKEN_NOT_FIXABLE => 'btn-danger',
        self::LOST_STOLEN => 'btn-danger',
        self::READY_TO_DEPLOY => 'btn-primary',
        self::TESTING => 'btn-info',
        self::DEPLOYED => 'btn-success',
    );

    protected $table = 'status_labels';
    protected $softDelete = true;

    protected $rules = array(
        'name'  		=> 'required|alpha_space|min:2',
    );

    public function has_assets()
    {
        return $this->hasMany('Asset', 'status_id')->count();
    }

}
