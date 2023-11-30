@extends('templates.initial.initial')
@section('title', 'Inicio de Sessión')

@section('content')
<div class="login-content">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-3"></div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6">
            <form id="login-form" action="./login" method="post" class="mdc-card">
                @csrf
                <!--TITLE-->
                <div class="title-content">
                    <h1 class="mdc-typography--headline3">Iniciar Sesión</h1>
                    <span class="mdc-typography--subtitle1">Tu contraseña será el código que se envió a tu correo electrónico.</span>
                </div>

                <!-- INPUT DNI -->
                <div class="text-form">
                    <label id="form-username"
                        class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="dni-label">DNI</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <input type="text" id="username" name="dni" autofocus required class="mdc-text-field__input"
                            aria-labelledby="dni-label"
                            aria-controls="username-helper"
                            aria-describedby="username-helper"
                            maxlength="8">
                        <i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing">person</i>
                    </label>
                    <div class="mdc-text-field-helper-line">
                        <div class="mdc-text-field-helper-text" id="username-helper" aria-hidden="true">8 Dígitos numéricos.</div>
                    </div>
                </div>

                <!--INPUT PASSWORD-->
                <div class="text-form">
                    <label id="form-password"
                        class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="password-label">Contraseña</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <input type="password" id="password" name="password" required class="mdc-text-field__input" aria-labelledby="password-label">
                        <i id="password-visibility"
                            class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing"
                            role="button">visibility</i>
                    </label>
                </div>

                <div class="text-form button-form">
                    <button id="btn-submit" class="mdc-button mdc-button--raised">
                        <div class="mdc-button__ripple"></div>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">login</i>
                        <span class="mdc-button__label">Acceder</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="mdc-layout-grid__cell--span-3"></div>
    </div>
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/login.css">
@endpush

@push('body_scripts')

<script src="static/js/login.js"></script>
@if (session('status') == 'error')
<script type="text/javascript">Notifier.error("{{ session('status_message') }}");</script>
@elseif (session('status') == 'success')
<script type="text/javascript">Notifier.success("{{ session('status_message') }}");</script>
@endif

@endpush
