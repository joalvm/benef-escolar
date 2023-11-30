@extends('templates.initial.initial')
@section('title', 'Registro')

@section('content')
<div class="info-text">
    <h1 class="mdc-typography--headline4">CAMPAÑA ESCOLAR 2021</h1>
    <span class="mdc-typography--subtitle1">Por favor ingresa tu DNI. Una vez que se te envíe tu contraseña a tu correo electrónico da click en "Iniciar Sessión" que se encuentra en el lado superior derecha.</span>
    <form action="api/register" method="GET" id="form-document" class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-3"></div>
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6">
                <label id="input-dni"
                    class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
                    <span class="mdc-notched-outline">
                        <span class="mdc-notched-outline__leading"></span>
                        <span class="mdc-notched-outline__notch">
                            <span class="mdc-floating-label" id="dni-label">Documento de Identidad</span>
                        </span>
                        <span class="mdc-notched-outline__trailing"></span>
                    </span>
                    <input type="number" id="dni" name="dni" class="mdc-text-field__input" autofocus maxlength="8" aria-labelledby="dni-label">
                    <button type="button" id="btn-search"
                        class="mdc-button mdc-button--outlined mdc-text-field__icon mdc-text-field__icon--trailing"
                        tabindex="0" role="button">
                        <span class="mdc-button__ripple"></span>
                        <i id="dni-visibility" class="material-icons">search</i>
                    </button>
                </label>
            </div>
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-3"></div>
        </div>
    </form>
</div>
<div id="dialog-confirm" class="mdc-dialog">
    <div class="mdc-dialog__container">
        <div class="mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title"
            aria-describedby="my-dialog-content">
            <h2 class="mdc-dialog__title" id="my-dialog-title">¡Se ha validado tu DNI!</h2>
            <form id="form-register" action="/register" method="POST" autocomplete="on" class="mdc-dialog__content" id="my-dialog-content">
                <span class="description">
                    Tu número de DNI es correcto, verifica si tus nombres coinciden y agrega un correo electrónico para enviarte tu contraseña que da acceso a esta plataforma.
                </span>
                <span class="name-description">
                    <strong>Nombre: </strong>
                    <span class="name-text"></span>
                </span>
                <label id="email-confirm" class="mdc-text-field mdc-text-field--outlined">
                    <span class="mdc-notched-outline">
                        <span class="mdc-notched-outline__leading"></span>
                        <span class="mdc-notched-outline__notch">
                            <span class="mdc-floating-label" id="email-label">Correo Electrónico</span>
                        </span>
                        <span class="mdc-notched-outline__trailing"></span>
                    </span>
                    <input type="email" required class="mdc-text-field__input" aria-labelledby="email-label">
                </label>
            </form>
            <div class="mdc-dialog__actions">
                <button type="button" id="btn-send" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="discard">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Enviar</span>
                </button>
                <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="cancel">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Cancelar</span>
                </button>
            </div>
        </div>
    </div>
    <div class="mdc-dialog__scrim"></div>
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/register.css" />
@endpush

@push('body_scripts')
<script src="static/js/register.js"></script>
@endpush
