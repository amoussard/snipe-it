@extends('backend/layouts/default')

@section('title0')
    @if (Input::get('Pending') || Input::get('Undeployable') || Input::get('RTD')  || Input::get('Deployed'))
        @if (Input::get('Pending'))
            @lang('general.pending')
        @elseif (Input::get('RTD'))
            @lang('general.ready_to_deploy')
        @elseif (Input::get('Undeployable'))
            @lang('general.undeployable')
        @elseif (Input::get('Deployed'))
            @lang('general.deployed')
        @endif
    @else
            @lang('general.all')
    @endif

    @lang('general.assets')
@stop

{{-- Page title --}}
@section('title')
    @yield('title0') :: @parent
@stop

{{-- Page content --}}
@section('content')


<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/hardware') }}" class="btn btn-success pull-right"><i class="icon-plus-sign icon-white"></i> @lang('general.create')</a>
        <h3>@yield('title0')</h3>
    </div>
</div>

<div class="row form-wrapper">

    <table id="assets-table">
        <thead>
            <tr role="row">
                <th class="col-md-1" bSortable="true">@lang('admin/hardware/table.asset_tag')</th>
                <th class="col-md-2" bSortable="true">@lang('admin/hardware/table.title')</th>
                <th class="col-md-2" bSortable="true">@lang('general.model')</th>
                <th class="col-md-2" bSortable="true">@lang('general.status')</th>
                {{--<th class="col-md-2" bSortable="true">@lang('admin/hardware/table.serial')</th>--}}
                <th class="col-md-2" bSortable="true">@lang('admin/hardware/table.location')</th>
                {{--<th class="col-md-2">@lang('admin/hardware/table.eol')</th>--}}
                <th class="col-md-1">@lang('admin/hardware/table.change')</th>
                <th class="col-md-2 actions" bSortable="false">@lang('table.actions')</th>
            </tr>
        </thead>
        <tbody>
        {{--
            @foreach ($assets as $asset)
            <tr>
                <td><a href="{{ route('view/hardware', $asset->id) }}">{{ $asset->asset_tag }}</a></td>
                <td>
                    @if ($asset->model)
                        <a href="{{ route('view/hardware', $asset->id) }}">{{{ $asset->model->name }}}</a>
                    @else
                        @lang('general.na')
                    @endif
                </td>
                @if (Setting::getSettings()->display_asset_name)
                    <td><a href="{{ route('view/hardware', $asset->id) }}">{{ $asset->name }}</a></td>
                @endif
                <td>
                    @if ($asset->model)
                        <a href="{{ route('view/hardware', $asset->id) }}">{{ $asset->serial }}</a>
                    @else
                        @lang('general.na')
                    @endif
                </td>
                <td>
                    @if ($asset->loc)
                        <a href="{{ route('view/location', $asset->location_id) }}">
                            {{{ $asset->loc->name }}}
                        </a>
                    @else
                        @lang('general.na')
                    @endif
                </td>
                <td>
                @if ($asset->model && $asset->model->eol)
                    {{{ $asset->eol_date() }}}
                @endif
                </td>
                <td>
                    @if ($asset->status_id < 1 )
                        @if ($asset->location_id != Location::NUMEDIA_ID)
                            <a href="{{ route('checkin/hardware', $asset->id) }}" class="btn btn-primary">@lang('general.checkin')</a>
                        @else
                            <a href="{{ route('checkout/hardware', $asset->id) }}" class="btn btn-info">@lang('general.checkout')</a>
                        @endif
                    @endif
                </td>
                <td nowrap="nowrap">
                    <a href="{{ route('update/hardware', $asset->id) }}" class="btn btn-warning"><i class="icon-pencil icon-white"></i></a>
                    <a data-html="false" class="btn delete-asset btn-danger" data-toggle="modal" href="{{ route('delete/hardware', $asset->id) }}" data-content="@lang('admin/hardware/message.delete.confirm')"
                    data-title="@lang('general.delete')
                     {{ htmlspecialchars($asset->asset_tag) }}?" onClick="return false;"><i class="icon-trash icon-white"></i></a>
                </td>
            </tr>
            @endforeach
        --}}
        </tbody>
    </table>

</div>


@stop
