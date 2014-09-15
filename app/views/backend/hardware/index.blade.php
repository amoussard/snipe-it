@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
    @lang('general.assets') :: @parent
@stop

{{-- Page content --}}
@section('content')


<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/hardware') }}" class="btn btn-success pull-right">
            <i class="icon-plus-sign icon-white"></i>
            @lang('general.create')
        </a>
        <h2>@lang('general.assets')</h2>
    </div>
</div>

<div class="row form-wrapper">

    <div class="row">
        <form class="form-inline filters-form" role="form">
            <h4>Filters</h4>
            <div class="form-group col-md-2">
                <label class="sr-only" for="assetMac">MAC address</label>
                <input type="text" id="assetMac" name="assetMac" class="form-control" placeholder="Enter MAC address" />
            </div>
            <div class="form-group col-md-2">
                <label class="sr-only" for="assetName">Name</label>
                <input type="text" id="assetName" name="assetName" class="form-control" placeholder="Enter name" />
            </div>
            <div class="form-group col-md-2">
                <label class="sr-only"  for="assetModel">Model</label>
                {{ Form::select('assetModel', $model_list , 0, array(
                    'class' => 'form-control',
                    'id' => 'assetModel'
                )) }}
            </div>
            <div class="form-group col-md-2">
                <label class="sr-only"  for="assetStatus">Status</label>
                {{ Form::select('assetStatus', $status_list , 0, array(
                    'class' => 'form-control',
                    'id' => 'assetStatus'
                )) }}
            </div>
            <div class="form-group col-md-2">
                <label class="sr-only" for="assetLocation">Location</label>
                <input type="text" id="assetLocation" name="assetLocation" class="form-control" placeholder="Enter a location name" />
            </div>
            <div class="checkbox col-md-2">
                <label>
                    <input type="checkbox" id="assetNumedia"> At Nümedia
                </label>
            </div>
            <div class="form-group col-md-2">
                <label class="sr-only" for="assetOrderNumber">Name</label>
                <input type="text" id="assetOrderNumber" name="assetOrderNumber" class="form-control" placeholder="Order number" />
            </div>
        </form>
    </div>

    <table id="assets-table">
        <thead>
            <tr role="row">
                <th class="col-md-1" bSortable="true">@lang('admin/hardware/table.mac_address')</th>
                <th class="col-md-2" bSortable="true">@lang('admin/hardware/table.name')</th>
                <th class="col-md-2" bSortable="true">@lang('admin/hardware/table.model')</th>
                <th class="col-md-2" bSortable="true">@lang('admin/hardware/table.status')</th>
                <th class="col-md-3" bSortable="true">@lang('admin/hardware/table.location')</th>
                <th class="col-md-1">@lang('admin/hardware/table.change')</th>
                <th class="col-md-2 actions" bSortable="false">@lang('table.actions')</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>


@stop
