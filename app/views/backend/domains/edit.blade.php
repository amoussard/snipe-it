@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')

    @if ($domain->id)
        @lang('admin/domains/table.update') ::
    @else
        @lang('admin/domains/table.create') ::
    @endif

@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ URL::previous() }}" class="btn-flat gray pull-right">
            <i class="icon-circle-arrow-left icon-white"></i> @lang('general.back')
        </a>
        <h3>
            @if ($domain->id)
                @lang('admin/domains/table.update')
            @else
                @lang('admin/domains/table.create')
            @endif
        </h3>
    </div>
</div>

<div class="row form-wrapper">

<form class="form-horizontal" method="post" action="" autocomplete="off">
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

    <!-- Location Name -->
    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
        <label for="name" class="col-md-2 control-label">
            @lang('admin/domains/table.name')
            <i class='icon-asterisk'></i>
        </label>
        <div class="col-md-12">
            <div class="col-xs-8">
                <input class="form-control" type="text" name="name" id="name" value="{{{ Input::old('name', $domain->name) }}}" />
            </div>
            {{ $errors->first('name', '<span class="alert-msg"><i class="icon-remove-sign"></i> :message</span>') }}
        </div>
    </div>

    <!-- Form actions -->
    <div class="form-group">
    <label class="col-md-2 control-label"></label>
        <div class="col-md-7">
            <a class="btn btn-link" href="{{ URL::previous() }}">@lang('general.cancel')</a>
            <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> @lang('general.save')</button>
        </div>
    </div>
</form>


@stop
