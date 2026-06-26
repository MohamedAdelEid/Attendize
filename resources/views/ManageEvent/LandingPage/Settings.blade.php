@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Landing Page Settings
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class="ico-home2 mr5"></i>
    Landing Page
@stop

@section('head')
    <script>
        $(function () {
            var hash = document.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            }
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>Landing Page CMS</strong> — Configure themes, sections, location, and footer content.
                Registration fees are pulled from your <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}">Registration Forms</a> and categories.
                <a href="{{ route('showEventSymposium', ['event_id' => $event->id]) }}" target="_blank" class="btn btn-sm btn-primary pull-right">Preview Landing Page</a>
            </div>

            <ul class="nav nav-tabs">
                <li class="{{ ($tab == 'general' || !$tab) ? 'active' : '' }}"><a href="#general" data-toggle="tab">General & Theme</a></li>
                <li class="{{ $tab == 'hero' ? 'active' : '' }}"><a href="#hero" data-toggle="tab">Hero</a></li>
                <li class="{{ $tab == 'pricing' ? 'active' : '' }}"><a href="#pricing" data-toggle="tab">Registration Fees</a></li>
                <li class="{{ $tab == 'registration' ? 'active' : '' }}"><a href="#registration" data-toggle="tab">Registration Section</a></li>
                <li class="{{ $tab == 'location' ? 'active' : '' }}"><a href="#location" data-toggle="tab">Location</a></li>
                <li class="{{ $tab == 'footer' ? 'active' : '' }}"><a href="#footer" data-toggle="tab">Footer</a></li>
                <li><a href="{{ route('showEventThemes', ['event_id' => $event->id]) }}">Manage Themes</a></li>
            </ul>

            <div class="tab-content panel panel-default" style="padding: 20px;">
                @include('ManageEvent.LandingPage.Partials.GeneralTab')
                @include('ManageEvent.LandingPage.Partials.HeroTab')
                @include('ManageEvent.LandingPage.Partials.PricingTab')
                @include('ManageEvent.LandingPage.Partials.RegistrationTab')
                @include('ManageEvent.LandingPage.Partials.LocationTab')
                @include('ManageEvent.LandingPage.Partials.FooterTab')
            </div>
        </div>
    </div>
@stop
