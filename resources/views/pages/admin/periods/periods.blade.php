@extends('templates.admin.admin')
@section('title', 'Periodos')

@section('content')
<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
        <h2 class="mdc-typography--headline6">Periodos</h2>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-2 self-middle">
        <button id="btn-add_child" class="mdc-button mdc-button--raised">
            <span class="mdc-button__ripple"></span>
            <i class="material-icons mdc-button__icon" aria-hidden="true">add</i>
            <span class="mdc-button__label">Agregar</span>
        </button>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <div id="periods-list" class="mdc-data-table">
            <div class="mdc-data-table__table-container">
                <table id="dt-periods" class="mdc-data-table__table" aria-label="Dessert calories">
                    <thead>
                        <tr class="mdc-data-table__header-row">
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nombre</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Fecha de Inicio</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Fecha de Fin</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Cantidad de bonos</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Importe máximo de préstamo</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Status</th>
                            <th class="mdc-data-table__header-cell mdc-data-table__header-cell--checkbox" role="columnheader" scope="col" style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="mdc-data-table__content"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('pages.admin.periods.dialog')
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/admin.periods.css">
@endpush

@push('body_scripts')
<script type="text/javascript" src="static/js/admin.periods.js"></script>
@endpush
