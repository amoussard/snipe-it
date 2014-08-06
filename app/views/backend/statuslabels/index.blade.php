@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/statuslabels/table.title') ::
@parent
@stop

{{-- Page content --}}
@section('content')


<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/statuslabel') }}" class="btn btn-success pull-right"><i class="icon-plus-sign icon-white"></i>  @lang('general.create')</a>
        <h3>@lang('admin/statuslabels/table.title')</h3>
    </div>
</div>

<div class="user-profile">
    <div class="row profile">
        <div class="col-md-9 bio">
            <div class="row">
                <form class="form-inline filters-form" role="form">
                    <h4>Filters</h4>
                    <div class="form-group col-md-3">
                        <label class="sr-only" for="statusLabelName">Name</label>
                        <input type="text" id="statusLabelName" name="statusLabelName" class="form-control" placeholder="Enter name" />
                    </div>
                </form>
            </div>

            <table id="statuslabels-table">
                <thead>
                    <tr role="row">
                        <th class="col-md-4">@lang('admin/statuslabels/table.name')</th>
                        <th class="col-md-2 actions">@lang('table.actions')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>

        <!-- side address column -->
        <div class="col-md-3 col-xs-12 address pull-right">
            <br /><br />
            <h6>@lang('admin/statuslabels/table.about')</h6>
            <p>@lang('admin/statuslabels/table.info')</p>
        </div>
    </div>
</div>

@stop
