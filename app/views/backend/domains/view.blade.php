@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
View Domain {{{ $domain->name }}} ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="location-view">
    <!-- header -->
    <div class="row header">
        <div class="col-md-8">
            <h3 class="name">{{{ $domain->name }}}
        </div>
        <a href="{{ route('update/domain', $domain->id) }}" class="btn-flat white large pull-right edit">
            <i class="icon-pencil"></i>
            @lang('button.edit') this domain
        </a>
    </div>

    <div class="row profile">
        <!-- bio, new note & orders column -->
        <div class="col-md-9 bio">
            <div class="profile-box">
                @if (count($domain->getLocations) > 0)
                    <table class="table table-hover">
                        <thead>
                        <tr role="row">
                            <th class="col-md-3">@lang('admin/locations/table.name')</th>
                            <th class="col-md-3">@lang('admin/locations/table.address')</th>
                            <th class="col-md-2">@lang('admin/locations/table.city'),
                                @lang('admin/locations/table.state')
                                @lang('admin/locations/table.country')</th>
                            <th class="col-md-2 actions">@lang('table.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($domain->getLocations as $location)
                                <tr>
                                    <td>
                                        <a href="{{ route('view/location', $location->id) }}">
                                            {{{ $location->name }}}
                                        </a>
                                    </td>
                                    <td>{{{ $location->address }}}, {{{ $location->address2 }}}  </td>
                                    <td>{{{ $location->city }}}, {{{ $location->state }}}  {{{ $location->country }}}  </td>
                                    <td>
                                        <a href="{{ route('update/location', $location->id) }}" class="btn btn-warning"><i class="icon-pencil icon-white"></i></a>
                                        <a data-html="false" class="btn delete-asset btn-danger" data-toggle="modal" href="{{ route('delete/location', $location->id) }}" data-content="@lang('admin/locations/message.delete.confirm')"
                                           data-title="@lang('general.delete')
                     {{ htmlspecialchars($location->name) }}?" onClick="return false;"><i class="icon-trash icon-white"></i></a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="col-md-12">
                        <div class="alert alert-info alert-block">
                            <i class="icon-info-sign"></i>
                            @lang('general.no_results')
                        </div>
                    </div>
                @endif

                @if (count($domain->getAssets) > 0)
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="col-md-2"><span class="line"></span>Name</th>
                            <th class="col-md-2"><span class="line"></span>Asset Tag</th>
                            <th class="col-md-3">Asset Type</th>
                            <th class="col-md-1"><span class="line"></span>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($domain->getAssets as $asset)
                        <tr>
                            <td><a href="{{ route('view/hardware', $asset->id) }}">{{{ $asset->name }}}</a></td>
                            <td><a href="{{ route('view/hardware', $asset->id) }}">{{{ $asset->asset_tag }}}</a></td>
                            <td>
                                @if ($asset->model)
                                    {{{ $asset->model->name }}}
                                @else
                                    @lang('general.na')
                                @endif
                            </td>
                            <td> <a href="{{ route('checkin/hardware', $asset->id) }}" class="btn-flat info">Checkin</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="col-md-12">
                        <div class="alert alert-info alert-block">
                            <i class="icon-info-sign"></i>
                            @lang('general.no_results')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
