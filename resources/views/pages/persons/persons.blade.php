@extends('templates.admin.admin')

@section('title', 'Información Personal')

@section('content')
<div class="mdc-layout-grid__inner">
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Información Personal.</h3>
    </div>

    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text information mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-name">Nombres</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txnames" name="names" value="{{$person['names']}}" required
                class="mdc-text-field__input" aria-labelledby="lbl-name">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text information mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-dni">DNI</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtdni" name="dni" value="{{$person['dni']}}" disabled required class="mdc-text-field__input"
                aria-labelledby="lbl-dni">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text information mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-email">Email</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtemail" name="email" value="{{$person['email']}}" required
                class="mdc-text-field__input" aria-labelledby="lbl-email">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <div aria-required="true" data-name="gender" class="input-select information mdc-select mdc-select--outlined">
            <div class="mdc-select__anchor" aria-labelledby="outlined-select-label" aria-controls="helper-cbo-gender"
                aria-describedby="helper-cbo-gender">
                <span class="mdc-notched-outline">
                    <span class="mdc-notched-outline__leading"></span>
                    <span class="mdc-notched-outline__notch">
                        <span id="outlined-select-label" class="mdc-floating-label">Sexo</span>
                    </span>
                    <span class="mdc-notched-outline__trailing"></span>
                </span>
                <span class="mdc-select__selected-text-container">
                    <span id="demo-selected-text" class="mdc-select__selected-text"></span>
                </span>
                <span class="mdc-select__dropdown-icon">
                    <svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5" focusable="false">
                        <polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd"
                            points="7 10 12 15 17 10">
                        </polygon>
                        <polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd"
                            points="7 15 12 10 17 15">
                        </polygon>
                    </svg>
                </span>
            </div>
            <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
                <ul class="mdc-list" role="listbox" aria-label="Food picker listbox">
                    <li class="mdc-list-item {{$person['gender'] == 'femenino' ? 'mdc-list-item--selected': ''}}"
                        data-value="femenino" role="option">
                        <span class="mdc-list-item__ripple"></span>
                        <span class="mdc-list-item__text">FEMENINO</span>
                    </li>
                    <li class="mdc-list-item {{$person['gender'] == 'masculino' ? 'mdc-list-item--selected': ''}}"
                        data-value="masculino" role="option">
                        <span class="mdc-list-item__ripple"></span>
                        <span class="mdc-list-item__text">MASCULINO</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text information mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-bith_date">Fecha de nacimiento.</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="date" id="txtbirth_date" name="birth_date" value="{{ $person['birth_date'] }}" required class="mdc-text-field__input" aria-labelledby="lbl-bith_date">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text information mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-phone">Celular.</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="text" id="txtphone" name="phone" value="{{ $person['phone'] }}" required class="mdc-text-field__input" aria-labelledby="lbl-phone">
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12" style="text-align: right">
        <button id="btn-save_info" class="mdc-button mdc-button--raised">
            <span class="mdc-button__ripple"></span>
            <i class="material-icons mdc-button__icon" aria-hidden="true">save</i>
            <span class="mdc-button__label">Guardar Información</span>
        </button>
    </div>

    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Otros correos electrónicos. <small style="color: #555">(En desarrollo)</small></h3>
        <span class="mdc-typography--body2">Si desea ser notificado en otras cuentas de correos electronicos.</span>
    </div>

    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <div class="mdc-chip-set mdc-chip-set--input" role="grid" style="margin-bottom: 8px">
            <div class="mdc-chip" role="row">
                <div class="mdc-chip__ripple"></div>
                <span role="gridcell">
                    <span role="button" tabindex="0" class="mdc-chip__primary-action">
                        <span class="mdc-chip__text">joalvm@gmail.com</span>
                    </span>
                </span>
                <span role="gridcell">
                    <i class="material-icons mdc-chip__icon mdc-chip__icon--trailing" tabindex="-1"
                        role="button">cancel</i>
                </span>
            </div>
            <div class="mdc-chip" role="row">
                <div class="mdc-chip__ripple"></div>
                <span role="gridcell">
                    <span role="button" tabindex="-1" class="mdc-chip__primary-action">
                        <span class="mdc-chip__text">otrocorreo@ejemplo.com</span>
                    </span>
                </span>
                <span role="gridcell">
                    <i class="material-icons mdc-chip__icon mdc-chip__icon--trailing" tabindex="-1"
                        role="button">cancel</i>
                </span>
            </div>
        </div>
        <div>
            <label class="input_text mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
                <span class="mdc-notched-outline">
                    <span class="mdc-notched-outline__leading"></span>
                    <span class="mdc-notched-outline__notch">
                        <span class="mdc-floating-label" id="lbl-bith_date">Correo Electrónico.</span>
                    </span>
                    <span class="mdc-notched-outline__trailing"></span>
                </span>
                <input type="email" id="txtorder_email" name="other_email" value=""
                    class="mdc-text-field__input" aria-labelledby="lbl-bith_date">
                <button type="button" id="btn-search"
                    class="mdc-button mdc-button--outline mdc-text-field__icon mdc-text-field__icon--trailing"
                    tabindex="0" role="button" style="height: 100%">
                    <span class="mdc-button__ripple"></span>
                    <i id="dni-visibility" class="material-icons">add</i>
                </button>
            </label>
        </div>
    </div>

    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
        <h3 class="mdc-typography--headline6">Cambiar contraseña.</h3>
        <span class="mdc-typography--body2">Recuerde usar una combinación que sea fácil de recordar para usted.</span>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text change_password mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-current_password">Contraseña Actual</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="password" id="txtcurrent_password" name="current_password" class="mdc-text-field__input"
                aria-labelledby="lbl-current_password" required>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text change_password mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-password">Nueva Contraseña</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="password" id="txtpassword" name="password" class="mdc-text-field__input"
                aria-labelledby="lbl-password" required>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-12-mobile">
        <label class="input_text change_password mdc-text-field mdc-text-field--outlined">
            <span class="mdc-notched-outline">
                <span class="mdc-notched-outline__leading"></span>
                <span class="mdc-notched-outline__notch">
                    <span class="mdc-floating-label" id="lbl-confirm_password">Confirmar Contraseña</span>
                </span>
                <span class="mdc-notched-outline__trailing"></span>
            </span>
            <input type="password" id="txtconfirm_password" name="confirm_password" class="mdc-text-field__input"
                aria-labelledby="lbl-confirm_password" required>
        </label>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12" style="text-align: right">
        <button id="btn-change_password" class="mdc-button mdc-button--raised">
            <span class="mdc-button__ripple"></span>
            <i class="material-icons mdc-button__icon" aria-hidden="true">save</i>
            <span class="mdc-button__label">Cambiar Contraseña</span>
        </button>
    </div>
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/persons.css">
@endpush

@push('body_scripts')
<script src="static/js/persons.js"></script>
@endpush
