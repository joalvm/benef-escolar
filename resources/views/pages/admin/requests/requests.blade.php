@extends('templates.admin.admin')
@section('title', 'Solicitudes')

@section('content')
<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div
            class="mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-tablet mdc-layout-grid__cell--span-12-phone">
            <label
                class="mdc-text-field mdc-text-field--outlined mdc-text-field--no-label mdc-text-field--with-trailing-icon">
                <span class="mdc-notched-outline">
                    <span class="mdc-notched-outline__leading"></span>
                    <span class="mdc-notched-outline__trailing"></span>
                </span>
                <input id="dt-request-search" class="mdc-text-field__input" type="text" placeholder="Buscar"
                    aria-label="Label">
                <button type="button" id="dt-request-search-button"
                    class="mdc-button mdc-text-field__icon mdc-text-field__icon--trailing" tabindex="0" role="button"
                    style="height: 100%">
                    <span class="mdc-button__ripple"></span>
                    <i id="dni-visibility" class="material-icons">search</i>
                </button>
            </label>
        </div>
        <div
            class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-12-phone">
            <div class="select">
                <select id="cbo-boats" name="boats" class="input-select handle-pack type-delivery select-text"
                    aria-labelledby="lbl-boats">
                    <option value="all" selected>Todos</option>
                    @foreach ($boats as $boat)
                    <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                    @endforeach
                </select>
                <label id="lbl-boats" class="select-label">Embarcaciones</label>
            </div>
        </div>
        <div
            class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-12-phone">
            <div class="select">
                <select id="cbo-units" name="units" class="input-select handle-pack type-delivery select-text"
                    aria-labelledby="lbl-units">
                    <option value="all" selected>Todos</option>
                    @foreach ($units as $unit)
                    <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                    @endforeach
                </select>
                <label id="lbl-units" class="select-label">Unidades</label>
            </div>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8">
            <div id="form-status" class="mdc-form-field">
                <div class="mdc-checkbox">
                    <input type="checkbox" class="input_checkbox mdc-checkbox__native-control" value="pending"
                        id="chk-pending" />
                    <div class="mdc-checkbox__background">
                        <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                            <path class="mdc-checkbox__checkmark-path" fill="none"
                                d="M1.73,12.91 8.1,19.28 22.79,4.59" />
                        </svg>
                        <div class="mdc-checkbox__mixedmark"></div>
                    </div>
                    <div class="mdc-checkbox__ripple"></div>
                </div>
                <label for="chk-pending" class="badge-status pending"
                    style="padding-left: 12px; padding-right: 12px">Pendientes (<span data-status="pending"
                        class="counter">-</span>)</label>
                <div class="mdc-checkbox">
                    <input type="checkbox" class="input_checkbox mdc-checkbox__native-control" value="observed"
                        id="chk-observed" />
                    <div class="mdc-checkbox__background">
                        <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                            <path class="mdc-checkbox__checkmark-path" fill="none"
                                d="M1.73,12.91 8.1,19.28 22.79,4.59" />
                        </svg>
                        <div class="mdc-checkbox__mixedmark"></div>
                    </div>
                    <div class="mdc-checkbox__ripple"></div>
                </div>
                <label for="chk-observed" class="input_checkbox badge-status observed"
                    style="padding-left: 12px; padding-right: 12px">Observados (<span data-status="observed"
                        class="counter">-</span>)</label>
                <div class="mdc-checkbox">
                    <input type="checkbox" class="mdc-checkbox__native-control" value="approved" id="chk-approved" />
                    <div class="mdc-checkbox__background">
                        <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
                            <path class="mdc-checkbox__checkmark-path" fill="none"
                                d="M1.73,12.91 8.1,19.28 22.79,4.59" />
                        </svg>
                        <div class="mdc-checkbox__mixedmark"></div>
                    </div>
                    <div class="mdc-checkbox__ripple"></div>
                </div>
                <label for="chk-approved" class="badge-status approved"
                    style="padding-left: 12px; padding-right: 12px">Aprobados (<span data-status="approved"
                        class="counter">-</span>)</label>
            </div>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4" style="text-align: right">
            <a id="btn-export-zip" href="admin/persons/requests/zip" target="_blank"
                class="mdc-button mdc-button--outlined">
                <span class="mdc-button__ripple"></span>
                <span class="mdc-button__icon material-icons" aria-hidden="true">archive</span>
                <span class="mdc-button__label">Zip</span>
            </a>
            <a id="btn-export-excel" href="admin/persons/requests/excel" target="_blank"
                class="mdc-button mdc-button--outlined">
                <span class="mdc-button__ripple"></span>
                <svg class="mdc-button__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 548.291 548.291">
                    <path d="M486.206,196.121H473.04v-63.525c0-0.396-0.062-0.795-0.109-1.2c-0.021-2.52-0.829-4.997-2.556-6.96L364.657,3.677
                        c-0.033-0.031-0.064-0.042-0.085-0.075c-0.63-0.704-1.364-1.29-2.143-1.796c-0.229-0.154-0.461-0.283-0.702-0.419
                        c-0.672-0.365-1.387-0.672-2.121-0.893c-0.2-0.052-0.379-0.134-0.577-0.186C358.23,0.118,357.401,0,356.562,0H96.757
                        C84.894,0,75.256,9.649,75.256,21.502v174.613H62.092c-16.971,0-30.732,13.756-30.732,30.733v159.812
                        c0,16.961,13.761,30.731,30.732,30.731h13.164V526.79c0,11.854,9.638,21.501,21.501,21.501h354.776
                        c11.853,0,21.501-9.647,21.501-21.501V417.392h13.166c16.966,0,30.729-13.764,30.729-30.731V226.854
                        C516.93,209.872,503.176,196.121,486.206,196.121z M96.757,21.502h249.054v110.006c0,5.94,4.817,10.751,10.751,10.751h94.972
                        v53.861H96.757V21.502z M314.576,314.661c-21.124-7.359-34.908-19.045-34.908-37.544c0-21.698,18.11-38.297,48.116-38.297
                        c14.331,0,24.903,3.014,32.442,6.413l-6.411,23.2c-5.091-2.446-14.146-6.037-26.598-6.037s-18.488,5.662-18.488,12.266
                        c0,8.115,7.171,11.696,23.58,17.921c22.446,8.305,33.013,20,33.013,37.921c0,21.323-16.415,39.435-51.318,39.435
                        c-14.524,0-28.861-3.769-36.031-7.737l5.843-23.77c7.738,3.958,19.627,7.927,31.885,7.927c13.218,0,20.188-5.47,20.188-13.774
                        C335.894,324.667,329.858,320.13,314.576,314.661z M265.917,343.9v24.157h-79.439V240.882h28.877V343.9H265.917z M94.237,368.057
                        H61.411l36.788-64.353l-35.473-62.827h33.021l11.125,23.21c3.774,7.736,6.606,13.954,9.628,21.135h0.367
                        c3.027-8.115,5.477-13.775,8.675-21.135l10.756-23.21h32.827l-35.848,62.066l37.74,65.103h-33.202l-11.515-23.022
                        c-4.709-8.855-7.73-15.465-11.316-22.824h-0.375c-2.645,7.359-5.845,13.969-9.811,22.824L94.237,368.057z M451.534,520.968H96.757
                        V417.392h354.776V520.968z M451.728,368.057l-11.512-23.022c-4.715-8.863-7.733-15.465-11.319-22.825h-0.366
                        c-2.646,7.36-5.858,13.962-9.827,22.825l-10.551,23.022h-32.836l36.788-64.353l-35.471-62.827h33.02l11.139,23.21
                        c3.77,7.736,6.593,13.954,9.618,21.135h0.377c3.013-8.115,5.459-13.775,8.671-21.135l10.752-23.21h32.835l-35.849,62.066
                        l37.733,65.103h-33.202V368.057z" />
                </svg>
                <span class="mdc-button__label">Excel</span>
            </a>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12-desktop">
            <div id="dt-persons" class="mdc-data-table">
                <div class="mdc-data-table__table-container">
                    <table class="mdc-data-table__table" aria-label="Personal">
                        <thead>
                            <tr class="mdc-data-table__header-row">
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Id</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nombres</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">DNI</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Celular</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Unidad</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Embarcación</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Fecha</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Estado</th>
                                <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="mdc-data-table__content"></tbody>
                    </table>
                </div>

                <div id="dt-request-pagination" class="mdc-data-table__pagination">
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
<link rel="stylesheet" href="static/css/admin.requests.css">
@endpush

@push('body_scripts')
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script type="text/javascript" src="static/js/admin.requests.js"></script>
@endpush
