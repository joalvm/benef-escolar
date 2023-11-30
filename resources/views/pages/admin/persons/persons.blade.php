@extends('templates.admin.admin')
@section('title', 'Trabajadores')

@section('content')
<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8-desktop">
            <label class="mdc-text-field mdc-text-field--outlined mdc-text-field--no-label mdc-text-field--with-trailing-icon">
                <span class="mdc-notched-outline">
                    <span class="mdc-notched-outline__leading"></span>
                    <span class="mdc-notched-outline__trailing"></span>
                </span>
                <input id="dt-persons-search" class="mdc-text-field__input" type="text" placeholder="Buscar"
                    aria-label="Label">
                <button type="button" id="dt-persons-search-button"
                    class="mdc-button mdc-text-field__icon mdc-text-field__icon--trailing"
                    tabindex="0" role="button" style="height: 100%">
                    <span class="mdc-button__ripple"></span>
                    <i id="dni-visibility" class="material-icons">search</i>
                </button>
            </label>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop"></div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12-desktop">
            <div id="dt-persons" class="mdc-data-table">
                <div class="mdc-data-table__table-container">
                    <table class="mdc-data-table__table" aria-label="Personal">
                        <thead>
                            <tr class="mdc-data-table__header-row">
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Id</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nombres</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">DNI</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Planta</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Embarcación</th>
                                <th class="mdc-data-table__header-cell mdc-data-table__header-cell--checkbox" role="columnheader" scope="col">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="mdc-data-table__content"></tbody>
                    </table>
                </div>

                <div id="dt-persons-pagination" class="mdc-data-table__pagination">
                    <div class="mdc-data-table__pagination-trailing">
                        <div class="mdc-data-table__pagination-rows-per-page">
                            <div class="mdc-data-table__pagination-rows-per-page-label">
                                Filas por página
                            </div>

                            <div class="select mdc-data-table__pagination-rows-per-page-select">
                                <select class="select-text" required>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div class="mdc-data-table__pagination-navigation">
                            <div class="mdc-data-table__pagination-total">1‑10 of 100</div>
                            <button class="mdc-icon-button material-icons mdc-data-table__pagination-button"
                                data-action="first" data-first-page="true" disabled>
                                <div class="mdc-button__icon">first_page</div>
                            </button>
                            <button class="mdc-icon-button material-icons mdc-data-table__pagination-button"
                                data-action="preview" data-prev-page="true" disabled>
                                <div class="mdc-button__icon">chevron_left</div>
                            </button>
                            <button class="mdc-icon-button material-icons mdc-data-table__pagination-button"
                                data-action="next" data-next-page="true" disabled>
                                <div class="mdc-button__icon">chevron_right</div>
                            </button>
                            <button class="mdc-icon-button material-icons mdc-data-table__pagination-button"
                                data-action="last" data-last-page="true" disabled>
                                <div class="mdc-button__icon">last_page</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/admin.persons.css">
@endpush

@push('body_scripts')
<script type="text/javascript" src="static/js/admin.persons.js"></script>
@endpush
