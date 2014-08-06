@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Domains ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/domain') }}" class="btn btn-success pull-right"><i class="icon-plus-sign icon-white"></i>  @lang('general.create')</a>
        <h3>@lang('admin/domains/table.domains')</h3>
    </div>
</div>

<div class="row form-wrapper">
    <div class="row">
        <form class="form-inline filters-form" role="form">
            <h4>Filters</h4>
            <div class="form-group col-md-3">
                <label class="sr-only" for="domainName">Name</label>
                <input type="text" id="domainName" name="domainName" class="form-control" placeholder="Enter name" />
            </div>
        </form>
    </div>

    <table id="domains-table">
        <thead>
            <tr role="row">
                <th class="col-md-3" data-title="name">@lang('admin/domains/table.name')</th>
                <th class="col-md-2 actions" data-title="action">@lang('table.actions')</th>
            </tr>
        </thead>
        <tbody>
        {{--
            @foreach ($domains as $domain)
                <tr>
                    <td>
                        <a href="{{ route('view/domain', $domain->id) }}">
                            {{{ $domain->name }}}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('update/domain', $domain->id) }}" class="btn btn-warning"><i class="icon-pencil icon-white"></i></a>
                        <a data-html="false" class="btn delete-asset btn-danger" data-toggle="modal"
                           href="{{ route('delete/domain', $domain->id) }}" data-content="@lang('admin/domains/message.delete.confirm')"
                            data-title="@lang('general.delete')
                         {{ htmlspecialchars($domain->name) }}?" onClick="return false;"><i class="icon-trash icon-white"></i></a>

                    </td>
                </tr>
            @endforeach
        --}}
        </tbody>
    </table>
</div>


@stop
