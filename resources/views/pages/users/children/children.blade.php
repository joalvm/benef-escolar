@extends('templates.admin.admin')
@section('title', 'Dashboard')

@section('content')
<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-10">
        <h2 class="mdc-typography--headline6">Hijos</h2>
        <span class="mdc-typography--body2">Registre a sus hijos para que puedan acceder al bono.</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-2 self-middle">
        <!--<button id="btn-add_child" class="mdc-button mdc-button--raised">
            <span class="mdc-button__ripple"></span>
            <i class="material-icons mdc-button__icon" aria-hidden="true">add</i>
            <span class="mdc-button__label">agregar hijo</span>
        </button>-->
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <div id="children-list" class="mdc-data-table">
            <div class="mdc-data-table__table-container">
                <table id="dt-children" class="mdc-data-table__table" aria-label="Dessert calories">
                    <thead>
                        <tr class="mdc-data-table__header-row">
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nombres</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Sexo</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">F. Nacimiento</th>
                            <th class="mdc-data-table__header-cell mdc-data-table__header-cell--checkbox" role="columnheader" scope="col" style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="mdc-data-table__content"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('pages.users.children.dialog')

@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/user.children.css">
@endpush

@push('body_scripts')
<script src="static/js/user.children.js"></script>
@endpush
