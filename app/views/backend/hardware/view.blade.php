@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/hardware/general.view') {{ $asset->asset_tag }} ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <div class="btn-group pull-right">
            <button class="btn glow">@lang('button.actions')</button>
            <button class="btn glow dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                @if (in_array($asset->status_id, Statuslabel::$checkinStatus) && $asset->location_id != Location::NUMEDIA_ID)
                    <li><a href="{{ route('checkin/hardware', $asset->id) }}">@lang('admin/hardware/general.checkin')</a></li>
                @elseif(in_array($asset->status_id, Statuslabel::$checkoutStatus) && $asset->location_id == Location::NUMEDIA_ID)
                    <li><a href="{{ route('checkout/hardware', $asset->id) }}">@lang('admin/hardware/general.checkout')</a></li>
                @endif
                <li><a href="{{ route('update/hardware', $asset->id) }}">@lang('admin/hardware/general.edit')</a></li>
                <li><a href="{{ route('clone/hardware', $asset->id) }}">@lang('admin/hardware/general.clone')</a></li>
            </ul>
        </div>

        <h3 class="name">
            @lang('admin/hardware/general.view')
            {{{ $asset->asset_tag }}}
            @if ($asset->name)
                ({{{ $asset->name }}})
            @endif
        </h3>
    </div>
</div>

<div class="user-profile">
    <div class="row profile">
        <div class="col-md-9 bio">

            <div class="col-md-12" style="min-height: 130px;">

                @if ($asset->model && $asset->model->manufacturer)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.manufacturer'): </strong>
                        <a href="{{ route('update/manufacturer', $asset->model->manufacturer->id) }}">
                            {{{ $asset->model->manufacturer->name }}}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.model'):</strong>
                        <a href="{{ route('view/model', $asset->model->id) }}">
                            {{{ $asset->model->name }}}
                        </a>
                        / {{{ $asset->model->modelno }}}
                    </div>
                @endif

                @if ($asset->purchase_date)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.date'): </strong>
                        {{{ $asset->purchase_date }}}
                    </div>
                @endif

                @if ($asset->purchase_cost)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.cost'):</strong>
                        @lang('general.currency')
                        {{{ number_format($asset->purchase_cost,2) }}}
                    </div>
                @endif

                @if ($asset->order_number)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.order'):</strong>
                        {{{ $asset->order_number }}}
                    </div>
                @endif

                @if ($asset->warranty_months)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.warranty'):</strong>
                        {{{ $asset->warranty_months }}}
                        @lang('admin/hardware/form.months')
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.expires'):</strong>
                        {{{ $asset->warrantee_expires() }}}
                    </div>
                @endif

                @if ($asset->model && $asset->depreciation)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.depreciation'): </strong>
                        {{ $asset->depreciation->name }}
                        ({{{ $asset->depreciation->months }}} @lang('admin/hardware/form.months')
                        )
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.depreciates_on'): </strong>
                        {{{ $asset->depreciated_date() }}}
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.fully_depreciated'): </strong>
                        {{{ $asset->months_until_depreciated()->m }}}
                        @lang('admin/hardware/form.months')
                        @if ($asset->months_until_depreciated()->y > 0)
                            , {{{ $asset->months_until_depreciated()->y }}}
                            @lang('admin/hardware/form.years')
                        @endif
                    </div>
                @endif

                @if ($asset->model && $asset->model->eol)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.eol_rate'): </strong>
                        {{{ $asset->model->eol }}}
                        @lang('admin/hardware/form.months')
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.eol_date'): </strong>
                        {{{ $asset->eol_date() }}}
                        @if ($asset->months_until_eol())
                            (
                            @if ($asset->months_until_eol()->y > 0) {{{ $asset->months_until_eol()->y }}}
                                @lang('general.years'),
                            @endif

                            {{{ $asset->months_until_eol()->m }}}
                            @lang('general.months')
                            )
                        @endif
                    </div>
                @endif

                @if ($asset->supplier_id)
                    <div class="col-md-6">
                        <strong>@lang('admin/hardware/form.supplier'): </strong>
                        <a href="{{ route('view/supplier', $asset->supplier_id) }}">
                            {{{ $asset->supplier->name }}}
                        </a>
                    </div>
                @endif
            </div>

		<!-- Licenses assets table -->
        <h6>Software Assigned to {{{ $asset->name }}}</h6>

		<!-- checked out assets table -->
		@if (count($asset->licenses) > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="col-md-4"><span class="line"></span>@lang('general.name')</th>
                        <th class="col-md-1"><span class="line"></span>@lang('table.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asset->licenseseats as $seat)
                        <tr>
                            <td><a href="{{ route('view/license', $seat->license->id) }}">{{{ $seat->license->name }}}</a></td>
                            <td><a href="{{ route('checkin/license', $seat->id) }}" class="btn-flat info">@lang('general.checkin')</a>
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

        <!-- checked out assets table -->

        <h6>Logs</h6>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="col-md-3"><span class="line"></span>@lang('general.date')</th>
                    <th class="col-md-2"><span class="line"></span>@lang('general.admin')</th>
                    <th class="col-md-2"><span class="line"></span>@lang('table.actions')</th>
                    <th class="col-md-2"><span class="line"></span>@lang('general.location')</th>
                    <th class="col-md-3"><span class="line"></span>@lang('general.notes')</th>
                </tr>
            </thead>
            <tbody>
                @if (count($asset->assetlog) > 0)
                    @foreach ($asset->assetlog as $log)
                        <tr>
                            <td>{{{ $log->added_on }}}</td>
                            <td>
                                @if (isset($log->user_id))
                                    {{{ $log->adminlog->fullName() }}}
                                @endif
                            </td>
                            <td>{{ $log->action_type }}</td>
                            <td>
                                @if (isset($log->checkedout_to))
                                    <a href="{{ route('view/user', $log->checkedout_to) }}">
                                        {{{ $log->locationlog->name }}}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($log->note)
                                    {{{ $log->note }}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td>{{ $asset->created_at }}</td>
                    <td>
                        @if ($asset->adminuser->id)
                            {{{ $asset->adminuser->fullName() }}}
                        @else
                            @lang('general.unknown_admin')
                        @endif
                    </td>
                    <td>@lang('general.created_asset')</td>
                    <td></td>
                    <td>
                        @if ($asset->notes)
                            {{{ $asset->notes }}}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>


    </div>

    <!-- side address column -->
    <div class="col-md-3 col-xs-12 address pull-right">

        @if ($qr_code->display)
            <h6>@lang('admin/hardware/form.qr')</h6>
            <p>
                <img src="{{{ $qr_code->url }}}" />
            </p>
        @endif

        <h6>@lang('admin/hardware/form.location')</h6>

        <iframe width="300" height="133" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&amp;q={{{ $asset->loc->address }}},{{{ $asset->loc->city }}},{{{ $asset->loc->state }}},{{{ $asset->loc->country }}}&amp;output=embed"></iframe>
        <ul>
            <li>{{{ $asset->loc->name }}}</li>
            <li>{{{ $asset->loc->address }}} {{{ $asset->loc->address2 }}}</li>
            <li>{{{ $asset->loc->city }}}, {{{ $asset->loc->state }}} {{{ $asset->loc->zip }}}</li>

            @if (in_array($asset->status_id, Statuslabel::$checkinStatus) && $asset->location_id != Location::NUMEDIA_ID)
                <li><a href="{{ route('checkin/hardware', $asset->id) }}" class="btn-flat large info ">@lang('admin/hardware/general.checkin')</a></li>
            @elseif(in_array($asset->status_id, Statuslabel::$checkoutStatus) && $asset->location_id == Location::NUMEDIA_ID)
                <li><a href="{{ route('checkout/hardware', $asset->id) }}" class="btn-flat large success">@lang('admin/hardware/general.checkout')</a></li>
            @endif
        </ul>
    </div>
</div>
@stop
