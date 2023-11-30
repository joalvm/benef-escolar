<?php
    use Illuminate\Support\Arr;

    $total = 0;
    $bonoExtra = 0;
    $status = $requests['status'] ?? 'no_requested';

    foreach ($children as $key) {
        if (Arr::get($key, 'request.get_loan', false)) {
            $total += $period['max_amount_loan'];
        }

        if (Arr::get($key, 'request.education_level.amount', 0)) {
            $bonoExtra = (int) Arr::get($key, 'request.education_level.amount', 0);
        }
    }

    $class = empty($children) ? 'mdc-layout-grid__cell--span-12' : 'mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-12-phone';
?>

@extends('templates.admin.admin')

@section('title', 'Campaña Escolar ' . $period['name'])

@section('content')
<div class="mdc-layout-grid__inner">
    <!--CARDS-->
    <div
        class="mdc-layout-grid__cell {{$class}}">
        <div class="mdc-card card-amount mdc-card--outlined">
            <div class="mdc-card__content">
                <h2 class="mdc-typography--headline4 status-{{$status}}">{{ status_message($status, true) }}</h2>
                <h2 class="mdc-typography--body2">{{ status_message($status) }}</h2>
            </div>
        </div>
    </div>
    @if (!empty($children))
    <div
        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-6-phone">
        <div class="mdc-card card-amount mdc-card--outlined">
            <div class="mdc-card__content">
                <h2 class="mdc-typography--headline4">
                    <span>S/. {{$period['amount_bonds'] + $bonoExtra}}</span>
                    <!--<span class="bono-extra">+ s/.</span> -->
                </h2>
                <h2 class="mdc-typography--body2">Asig. Escolar {{$period['name']}}</h2>
            </div>
        </div>
    </div>
    <div
        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-6-phone">
        <div class="mdc-card card-amount mdc-card--outlined">
            <div class="mdc-card__content">
                <h2 class="mdc-typography--headline4">S/. {{$total}}</h2>
                <h2 class="mdc-typography--body2">Total de prestamo</h2>
            </div>
        </div>
    </div>
    @endif
    <!--TABLE-->
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6">
        @if(empty($requests))
        <span class="mdc-typography--headline6" style="color: #0065e9;">PASO 1:</span><br/>
        @endif
        <span class="mdc-typography--headline6" style="font-size: 1rem">INFORMACIÓN DE HIJOS</span>
    </div>
    @if (count($children) < Arr::get($period, 'max_children'))
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6" style="text-align: right">
        <a href="user/bonds/children" class="mdc-button mdc-button--raised">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">AGREGAR HIJO</div>
        </a>
    </div>
    @endif
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        @include('pages.users.bonds.table_children')
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        @include('pages.users.bonds.request')
    </div>
</div>

@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/user.bonds.css">
@endpush

@push('body_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.0.7/compressor.min.js"></script>
<script id="data-children">
    window.localStorage.setItem('children', JSON.stringify(@json($children)));
</script>
<script src="static/js/user.bonds.js"></script>
@endpush
