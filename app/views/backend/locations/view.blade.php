@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
View Location {{{ $location->name }}} ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="location-view">
    <!-- header -->
    <div class="row header">
        <div class="col-md-8">
            <h3 class="name">{{{ $location->name }}}
        </div>
        <a href="{{ route('update/location', $location->id) }}" class="btn-flat white large pull-right edit">
            <i class="icon-pencil"></i>
            @lang('button.edit') this location
        </a>
    </div>

    <div class="row profile">
        <!-- bio, new note & orders column -->
        <div class="col-md-9 bio">
            <div class="profile-box">
                @if (count($location->getAssets) > 0)
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="col-md-3">Asset Type</th>
                            <th class="col-md-2"><span class="line"></span>Asset Tag</th>
                            <th class="col-md-2"><span class="line"></span>Name</th>
                            <th class="col-md-1"><span class="line"></span>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($location->getAssets as $asset)
                                <tr>
                                    <td>
                                        @if ($asset->physical == '1' && $asset->model)
                                            {{{ $asset->model->name }}}
                                        @endif
                                    </td>
                                    <td><a href="{{ route('view/hardware', $asset->id) }}">{{{ $asset->asset_tag }}}</a></td>
                                    <td><a href="{{ route('view/hardware', $asset->id) }}">{{{ $asset->name }}}</a></td>

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
        <!-- side address column -->
        <div class="col-md-3 address pull-right">
            <h6>Informations</h6>

            <iframe width="300" height="133" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&amp;q={{{ $location->address }}},{{{ $location->city }}},{{{ $location->state }}},{{{ $location->country }}}&amp;output=embed"></iframe>
            <ul>
                <li>{{{ $location->address }}} {{{ $location->address2 }}}</li>
                <li>{{{ $location->city }}}, {{{ $location->state }}} {{{ $location->zip }}}</li>
            </ul>
        </div>
    </div>
@stop
