<?php

use Illuminate\Support\Str;

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base href="{{ url('/') }}" />
    <title>Project Blue :: @yield('title')</title>
    <script type="text/javascript">
        var user = @json(session('user'));

        localStorage.setItem('session', JSON.stringify(@json(session('api_token'))));
        localStorage.setItem('user', JSON.stringify(user));

        @if (session('user.role') != App\Models\Users::ROLE_USER)
            localStorage.setItem('selected_period', JSON.stringify(@json(session('selected_period'))));
        @endif
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css"
        integrity="sha512-oHDEc8Xed4hiW6CxD7qjbnI+B07vDdX7hEPTvn9pSZO1bcRqHp8mj9pyr+8RVC2GmtEfI2Bi9Ke9Ass0as+zpg=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="static/css/base.css">
    <link rel="stylesheet" href="static/css/admin.css">
    @stack('header_styles')
</head>

<body class="mdc-typography">
    @include('templates.admin.menu')

    <div class="mdc-drawer-scrim"></div>
    <div class="mdc-drawer-app-content">
        <header class="mdc-top-app-bar app-bar" id="app-bar">
            <div class="mdc-top-app-bar__row">
                <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                    <button id="btn-menu" class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button">menu</button>
                    <span class="mdc-top-app-bar__title">Project Blue</span>
                </section>
                <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end">
                    <button id="btn-period" aria-describedby="btn-period-tooltip" class="mdc-button mdc-button--outlined">
                        <span class="mdc-button__ripple"></span>
                        <span class="mdc-button__label">---</span>
                        <i class="material-icons mdc-button__icon" aria-hidden="true">today</i>
                    </button>
                    <button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button">notifications</button>
                    <a href="/logout" class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button">logout</a>
                </section>
            </div>

            <!--TOOLTIP BOTON PERIODOS-->
            <div id="btn-period-tooltip" class="mdc-tooltip" role="tooltip" aria-hidden="true">
                <div class="mdc-tooltip__surface">Periodo actual</div>
            </div>

            @if (session('user.role') != App\Models\Users::ROLE_USER)
            <div id="dialog-selected_period" class="mdc-dialog">
                <form action="{{ url('/change-period') }}" method="POST" class="mdc-dialog__container">
                  {{ csrf_field() }}
                  <div
                    class="mdc-dialog__surface"
                    role="alertdialog"
                    aria-modal="true"
                    aria-labelledby="my-dialog-title"
                    aria-describedby="my-dialog-content"
                  >
                    <h2 class="mdc-dialog__title" id="my-dialog-title">Seleccione un periodo</h2>
                    <div class="mdc-dialog__content" id="my-dialog-content">
                      <ul class="mdc-list"></ul>
                    </div>
                    <div class="mdc-dialog__actions">
                      <button
                        type="submit"
                        class="mdc-button mdc-dialog__button"
                        data-mdc-dialog-action="accept"
                      >
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label">Aceptar</span>
                      </button>
                    </div>
                  </div>
                </form>
                <div class="mdc-dialog__scrim"></div>
            </div>
            @endif
        </header>

        <main class="mdc-layout-grid main-content mdc-top-app-bar--fixed-adjust" id="main-content">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid--fixed-column-width mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-1-tablet"></div>
                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8-desktop mdc-layout-grid__cell--span-9-tablet mdc-layout-grid__cell--span-12-phone">
                    @yield('empty_content')
                    <div class="mdc-card my-custom-card">
                        <div class="mdc-card__content">@yield('content')</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js"
    integrity="sha512-DZqqY3PiOvTP9HkjIWgjO6ouCbq+dxqWoJZ/Q+zPYNHmlnI2dQnbJ5bxAHpAMw+LXRm4D72EIRXzvcHQtE8/VQ=="
    crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/validatorjs@3.22.1/dist/validator.js"></script>
<script src="https://cdn.jsdelivr.net/npm/validatorjs@3.22.1/dist/lang/es.js"></script>
<script>
    if (window.innerWidth > 1024) {
        document.querySelector('.mdc-drawer')
            .classList
            .remove(...['mdc-drawer--modal', 'mdc-top-app-bar--fixed-adjust']);

        document.getElementById('btn-menu').remove();
    }
</script>
<script src="static/js/base.js"></script>
<script src="static/js/admin.js"></script>
@stack('body_scripts')

</html>
