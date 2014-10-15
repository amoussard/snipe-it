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
    const INTERNAL_USAGE = 8;
    const READY_TO_STAGE = 9;
    const WAITING_FOR_RETURN = 10;

    public static $checkoutStatus = array(
        self::READY_TO_DEPLOY,
    );

    public static $checkinStatus = array(
        self::DEPLOYED,
        self::OUT_FOR_REPAIR,
        self::WAITING_FOR_RETURN,
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
        self::INTERNAL_USAGE => 'btn-success',
        self::READY_TO_STAGE => 'btn-info',
        self::WAITING_FOR_RETURN => 'btn-info',
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
