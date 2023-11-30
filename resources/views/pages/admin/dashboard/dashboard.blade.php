@extends('templates.admin.admin')
@section('title', 'Dashboard')

@section('empty_content')
<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <div class="mdc-card">
            <div class="mdc-card__content">
                <canvas id="chart-daily_requests" width="100%" height="400"></canvas>
            </div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                <div class="mdc-card">
                    <div class="mdc-card__content">
                        <canvas id="chart-status_requests" width="100%" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12"></div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4">
        <div class="mdc-card">
            <div class="mdc-card__content">
            </div>
        </div>
    </div>
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/admin.dashboard.css">
@endpush

@push('body_scripts')
<script type="text/javascript" src="static/js/admin.dashboard.js"></script>
@endpush
