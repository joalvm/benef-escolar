<?php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Storage;

    $storage = array_merge(
        Arr::except($children, 'parent'),
        [
            'request' => array_merge(
                Arr::except($request, ['child', 'parent', 'period', 'plant', 'education_level', 'responsable']),
                [
                    'education_levels_id' => Arr::get($request, 'education_level.id'),
                    'get_loan' => Arr::get($request, 'get_loan', true),
                    'get_pack' => Arr::get($request, 'get_pack', true),
                    'delivery_type' => Arr::get($request, 'delivery_type'),
                    'plants_id' => Arr::get($request, 'plant.id'),
                    'responsable_name' => Arr::get($request, 'responsable.name'),
                    'responsable_dni' => Arr::get($request, 'responsable.dni'),
                    'responsable_phone' => Arr::get($request, 'responsable.phone'),
                    'districts_id' => Arr::get($request, 'district.id'),
                    'address' => Arr::get($request, 'address'),
                    'address_reference' => Arr::get($request, 'address_reference'),
                    'documents' => array_map(function ($item) {
                        return Arr::only($item, ['id', 'type', 'file', 'status']);
                    }, $documents)
                ]
            )
        ]
    );
?>
@extends('templates.admin.admin')

@section('title', 'Registro de hijo')

@section('content')

<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Información Personal.</h3>
        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada campo.</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-name" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-name">Nombres Hijo</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txname" name="name" required value="{{$children['name'] ?? ''}}"
                class="input-text mdc-text-field__input" aria-labelledby="lbl-name">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-paternal_surname" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-paternal_surname">Apellido Paterno Hijo</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtpaternal_surname" name="paternal_surname"
                value="{{$children['paternal_surname'] ?? ''}}" required class="input-text mdc-text-field__input"
                aria-labelledby="lbl-paternal_surname">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-maternal_surname" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-maternal_surname">Apellido Materno Hijo</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtmaternal_surname" name="maternal_surname"
                value="{{$children['maternal_surname'] ?? ''}}" required class="input-text mdc-text-field__input"
                aria-labelledby="lbl-maternal_surname">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-gender" name="gender" class="input-select select-text"
                value="{{$children['gender'] ?? 'femenino'}}" required>
                <option value="femenino" {{($children['gender'] ?? 'femenino') == 'femenino' ? 'selected' : ''}}>
                    FEMENINO</option>
                <option value="masculino" {{($children['gender'] ?? 'femenino') == 'masculino' ? 'selected' : ''}}>
                    MASCULINO</option>
            </select>
            <label class="select-label">Sexo Hijo</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-birth_date" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-birth_date">F. Nacimiento Hijo</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="date" id="txtbirth_date" name="birth_date" value="{{$children['birth_date'] ?? ''}}" required
                class="input-text mdc-text-field__input" aria-labelledby="lbl-birth_date">
        </label>
    </div>

    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Datos para acceder al bono.</h3>
        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada campo.</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-education-levels" name="education_levels_id" class="input-select select-text" data-value="{{Arr::get($request, 'education_level.id')}}" required>
                <option value="" selected disabled></option>
            </select>
            <label class="select-label">Nivel Educativo</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <label>¿Desea solicitar un prestamo? <strong>Monto: S/. {{$period['max_amount_loan']}}</strong></label>
        <br>
        <div id="form-loan-yes" class="mdc-form-field">
            <div class="mdc-radio">
                <input class="mdc-radio__native-control" type="radio" id="input-loan-yes" value="1" name="get_loan"
                    {{Arr::get($request, 'get_loan', true) ? 'checked' : ''}}>
                <div class="mdc-radio__background">
                    <div class="mdc-radio__outer-circle"></div>
                    <div class="mdc-radio__inner-circle"></div>
                </div>
                <div class="mdc-radio__ripple"></div>
            </div>
            <label for="input-loan-yes">SI</label>
        </div>
        <div id="form-loan-no" class="mdc-form-field">
            <div class="mdc-radio">
                <input class="mdc-radio__native-control" type="radio" id="input-loan-no" value="0" name="get_loan"
                    {{!Arr::get($request, 'get_loan', true) ? 'checked' : ''}}>
                <div class="mdc-radio__background">
                    <div class="mdc-radio__outer-circle"></div>
                    <div class="mdc-radio__inner-circle"></div>
                </div>
                <div class="mdc-radio__ripple"></div>
            </div>
            <label for="input-loan-no">NO</label>
        </div>
        <br>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <label>¿Deseo recibir Pack Educativo?</label>
        <br>
        <div id="form-pack-yes" class="mdc-form-field">
            <div class="mdc-radio">
                <input class="mdc-radio__native-control" type="radio" id="input-pack-yes" value="1" name="get_pack"
                    {{Arr::get($request, 'get_pack', true) ? 'checked' : ''}}>
                <div class="mdc-radio__background">
                    <div class="mdc-radio__outer-circle"></div>
                    <div class="mdc-radio__inner-circle"></div>
                </div>
                <div class="mdc-radio__ripple"></div>
            </div>
            <label for="input-pack-yes">SI</label>
        </div>
        <div id="form-pack-no" class="mdc-form-field">
            <div class="mdc-radio">
                <input class="mdc-radio__native-control" type="radio" id="input-pack-no" value="0" name="get_pack"
                    {{!Arr::get($request, 'get_pack', true) ? 'checked' : ''}}>
                <div class="mdc-radio__background">
                    <div class="mdc-radio__outer-circle"></div>
                    <div class="mdc-radio__inner-circle"></div>
                </div>
                <div class="mdc-radio__ripple"></div>
            </div>
            <label for="input-pack-no">NO</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-delivery_type" name="delivery_type"
                class="input-select handle-pack select-text"
                value="{{ Arr::get($request, 'delivery_type') }}"
                aria-labelledby="lbl-delivery_type"
                data-value="{{ Arr::get($request, 'delivery_type') }}"
                {{ Arr::get($request, 'get_pack', true) ? '' : 'disabled' }}
                required>
                <option selected disabled></option>
                <option value="pick_in_plant" {{ Arr::get($request, 'delivery_type') == 'pick_in_plant' ? 'selected' : '' }}>Recoger en planta (Solo el trabajador)</option>
                <option value="delivery" {{ Arr::get($request, 'delivery_type') == 'delivery' ? 'selected' : '' }}>Enviar a mi domicilio</option>
            </select>
            <label id="lbl-delivery_type" class="select-label">Tipo de recepción</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-plants" name="plants_id"
                class="input-select handle-pack type-pick_in_plant select-text"
                aria-labelledby="lbl-plants"
                data-value="{{ Arr::get($request, 'plant.id') }}"
                {{ Arr::get($request, 'delivery_type') != 'pick_in_plant' ? 'disabled' : '' }}
                required>
                <option value="" selected disabled></option>
            </select>
            <label id="lbl-plants" class="select-label">Planta de recojo</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Persona encargada de la recepción.</h3>
        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada campo.</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-responsable_name" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-responsable_name">Nombre del responsable</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtresponsable_name"
                name="responsable_name"
                value="{{ Arr::get($request, 'responsable.name') }}"
                class="mdc-text-field__input handle-pack type-delivery"
                aria-labelledby="lbl-responsable_name"
                required
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-responsable_dni" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-responsable_dni">DNI del responsable</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="number" id="txt-responsable_dni"
                name="responsable_dni"
                value="{{ Arr::get($request, 'responsable.dni') }}"
                class="mdc-text-field__input handle-pack type-delivery"
                aria-labelledby="lbl-responsable_dni"
                required
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-responsable_phone" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-responsable_phone">Celular del responsable</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="number" id="txt-responsable_phone"
                name="responsable_phone"
                value="{{ Arr::get($request, 'responsable.phone') }}"
                class="mdc-text-field__input handle-pack type-delivery"
                aria-labelledby="lbl-responsable_phone"
                required
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-department" name="departments"
                class="input-select handle-pack type-delivery select-text"
                aria-labelledby="lbl-department"
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}
                data-value="{{ Arr::get($request, 'department.id') }}"
                required>
                <option value="" disabled selected></option>
            </select>
            <label id="lbl-department" class="select-label">Departamento</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-provinces" name="provinces"
                class="input-select handle-pack type-delivery select-text"
                aria-labelledby="lbl-provinces"
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}
                data-value="{{ Arr::get($request, 'province.id') }}"
                required>
                <option value="" disabled selected></option>
            </select>
            <label id="lbl-provinces" class="select-label">Provincia</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-phone">
        <div class="select">
            <select id="cbo-districts" name="districts_id"
                class="input-select handle-pack type-delivery select-text"
                aria-labelledby="lbl-districts"
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}
                data-value="{{ Arr::get($request, 'district.id') }}"
                required>
                <option value="" disabled selected></option>
            </select>
            <label id="lbl-districts" class="select-label">Distrito</label>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-address" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-address">Dirección de entrega</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txt-address"
                name="address"
                value="{{ Arr::get($request, 'address') }}"
                class="mdc-text-field__input handle-pack type-delivery"
                aria-labelledby="lbl-address"
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <label id="input-address_reference" class="mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-address_reference">Referencia</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txt-address_reference"
                name="address_reference"
                value="{{ Arr::get($request, 'address_reference') }}"
                class="mdc-text-field__input handle-pack type-delivery"
                aria-labelledby="lbl-address_reference"
                {{ Arr::get($request, 'delivery_type') != 'delivery' ? 'disabled' : '' }}>
        </label>
    </div>
</div>

<br>

<!-- DOCUMENTOS -->
<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6" style="margin-bottom: 0;">Documentos</h3>
        <span class="mdc-typography--body2" style="color: #666">Solo imagenes y/o archivos PDF</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <div id="card-dni" class="mdc-card card-file mdc-card--outlined">
            <div class="mdc-card__content">
                <div class="mdc-typography--Subtitle1">DNI hijo(a)</div>
                <ul id="dni-list" class="mdc-list mdc-list--image-list mdc-list--two-line list-files">
                    @foreach ($documents as $doc)
                        @if (Arr::get($doc, 'type') == 'dni' and Arr::get($doc, 'status') != 'closed')
                            <li id="list-{{ Arr::get($doc, 'id') }}" class="mdc-list-item doc-status-{{ Arr::get($doc, 'status') }}">
                                @if(preg_match("/\.pdf$/im", Arr::get($doc, 'file')))
                                <a class="mdc-list-item__graphic material-icons"
                                    href="{{ file_url(Arr::get($doc, 'file')) }}"
                                    aria-hidden="true"
                                    target="_blank">picture_as_pdf</a>
                                @else
                                <a class="mdc-list-item__graphic"
                                    href="{{ file_url(Arr::get($doc, 'file')) }}"
                                    target="_blank"
                                    style="background-image: url('{{ file_url(Arr::get($doc, 'file')) }}')">
                                </a>
                                @endif
                                <span class="mdc-list-item__text">
                                    <span class="mdc-list-item__primary-text">{{ status_message(Arr::get($doc, 'status')) }}</span>
                                    <span class="mdc-list-item__secondary-text">{{ Arr::get($doc, 'observation') }}</span>
                                </span>
                                <span class="mdc-list-item__meta" aria-hidden="true">
                                    <!-- MUESTRA UN BOTON DEPENDIENDO DEL ESTADO DEL DOCUMENTO -->
                                    @if (Arr::get($doc, 'status') !== 'approved')
                                        <button
                                            data-id="{{ Arr::get($doc, 'id') }}"
                                            data-status="{{ Arr::get($doc, 'status') }}"
                                            class="mdc-icon-button material-icons upgradeable"
                                            title="{{Arr::get($doc, 'status') === 'observed' ? 'Cambiar documento' : 'Eliminar documento'}}">
                                            <span>{{Arr::get($doc, 'status') === 'observed' ? 'cached' : 'delete'}}</span>
                                        </button>
                                    @else
                                        <span class="material-icons">check</span>
                                    @endif
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
                @if (Arr::get($request, 'status') !== 'approved')
                    <button type="button" class="btn-upload request-empty-list upload mdc-button">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">upload</i>
                        <div class="mdc-button__label">AGREGAR IMAGENES</div>
                    </button>
                    <input type="file" data-type="dni" multiple data-list="dni-list" style="display: none" />
                @endif
            </div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
        <div id="card-academic" class="mdc-card card-file mdc-card--outlined">
            <div class="mdc-card__content">
                <div class="mdc-typography--Subtitle1">Sustentos Escolares</div>
                <ul id="academico-list" class="mdc-list mdc-list--image-list mdc-list--two-line list-files">
                    @foreach ($documents as $doc)
                        @if (Arr::get($doc, 'type') == 'studies' and Arr::get($doc, 'status') != 'closed')
                            <li id="list-{{Arr::get($doc, 'id')}}" class="mdc-list-item doc-status-{{ Arr::get($doc, 'status') }}">
                                @if(preg_match("/\.pdf$/im", Arr::get($doc, 'file')))
                                <a class="mdc-list-item__graphic material-icons"
                                    href="{{ file_url(Arr::get($doc, 'file')) }}"
                                    aria-hidden="true"
                                    target="_blank">picture_as_pdf</a>
                                @else
                                <a class="mdc-list-item__graphic"
                                    href="{{ file_url(Arr::get($doc, 'file')) }}"
                                    target="_blank"
                                    style="background-image: url('{{ file_url(Arr::get($doc, 'file')) }}')">
                                </a>
                                @endif

                                <span class="mdc-list-item__text">
                                    <span class="mdc-list-item__primary-text">{{ status_message(Arr::get($doc, 'status')) }}</span>
                                    <span class="mdc-list-item__secondary-text" title="{{ Arr::get($doc, 'observation') }}">
                                        {{ Arr::get($doc, 'observation') }}{{ Arr::get($doc, 'file') }}
                                    </span>
                                </span>
                                <span class="mdc-list-item__meta" aria-hidden="true">
                                    <!-- MUESTRA UN BOTON DEPENDIENDO DEL ESTADO DEL DOCUMENTO -->
                                    @if (Arr::get($doc, 'status') !== 'approved')
                                        <button
                                            data-id="{{ Arr::get($doc, 'id') }}"
                                            data-status="{{ Arr::get($doc, 'status') }}"
                                            class="mdc-icon-button material-icons upgradeable"
                                            title="{{Arr::get($doc, 'status') === 'observed' ? 'Cambiar documento' : 'Eliminar documento'}}">
                                            <span>{{Arr::get($doc, 'status') === 'observed' ? 'cached' : 'delete'}}</span>
                                        </button>
                                    @else
                                        <span class="mdc-icon-button material-icons">check</span>
                                    @endif
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
                @if (Arr::get($request, 'status') !== 'approved')
                <button type="button" class="btn-upload request-empty-list upload mdc-button">
                    <div class="mdc-button__ripple"></div>
                    <i class="material-icons mdc-button__icon" aria-hidden="true">upload</i>
                    <div class="mdc-button__label">AGREGAR IMAGENES</div>
                </button>
                <input type="file" data-type="studies" multiple data-list="academico-list" style="display: none">
                @endif
            </div>
        </div>
    </div>
    <input type="file" id="input-reload-file" style="display: none">
</div>
<!--/DOCUMENTOS-->

<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 form-button-actions">
        <a id="btn-cancel" href="{{ previous_page($previewPage ?? 'user/bonds') }}" class="mdc-button mdc-button--outlined" style="margin-right: 24px">
            <span class="mdc-button__ripple"></span>
            <span class="mdc-button__label">cancelar</span>
        </a>
        <button id="btn-save" data-action="{{empty($children) ? 'create' : 'update'}}" class="mdc-button mdc-button--raised">
            <span class="mdc-button__ripple"></span>
            <span class="mdc-button__label">GUARDAR</span>
        </button>
    </div>
</div>

@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/user.bonds.children.css">
@endpush

@push('body_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.0.7/compressor.min.js"></script>
@if (!empty($children))
<script>
    window.localStorage.setItem(
        'user.bonds.children',
        JSON.stringify(@json($storage))
    );
</script>
@else
<script>
    window.localStorage.removeItem('user.bonds.children');
</script>
@endif
<script src="static/js/user.bonds.children.js"></script>
@endpush
