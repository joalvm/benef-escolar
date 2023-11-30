<?php

use Illuminate\Support\Facades\Request;

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Project Blue :: @yield('title')</title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css"
        integrity="sha512-oHDEc8Xed4hiW6CxD7qjbnI+B07vDdX7hEPTvn9pSZO1bcRqHp8mj9pyr+8RVC2GmtEfI2Bi9Ke9Ass0as+zpg=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="static/css/base.css">
    <link rel="stylesheet" href="static/css/initial.css">
    @stack('header_styles')
</head>

<body class="mdc-typography">
    <main class="grid-container">
        <div class="panel-left"></div>
        <div class="header">
            <div class="header-container">
                <div class="header-title">
                    <img src="static/img/logo.png" alt="logo">
                </div>
                <div class="header-actions">
                    @if (Request::path() != 'login')
                        <a href="./login" class="mdc-button mdc-button--raised">
                            <div class="mdc-button__ripple"></div>
                            <i class="material-icons mdc-button__icon" aria-hidden="true">login</i>
                            <span class="mdc-button__label">INICIAR SESSIÓN</span>
                        </a>
                    @else
                        <a href="./register" class="mdc-button">
                            <div class="mdc-button__ripple"></div>
                            <i class="material-icons mdc-button__icon" aria-hidden="true">arrow_back</i>
                            <span class="mdc-button__label">Volver a Inicio</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="content">
            <div class="mdc-layout-grid">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-2"></div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8">
                        @yield('content')
                    </div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-2"></div>
                </div>
            </div>
        </div>
    </main>
</body>
<link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700"
    />
<link
    rel="stylesheet"
    href="https://fonts.googleapis.com/icon?family=Material+Icons"
    />
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js"
    integrity="sha512-DZqqY3PiOvTP9HkjIWgjO6ouCbq+dxqWoJZ/Q+zPYNHmlnI2dQnbJ5bxAHpAMw+LXRm4D72EIRXzvcHQtE8/VQ=="
    crossorigin="anonymous"
    >
</script>
<script src="https://cdn.jsdelivr.net/npm/validatorjs@3.22.1/dist/validator.js"></script>
<script src="https://cdn.jsdelivr.net/npm/validatorjs@3.22.1/dist/lang/es.js"></script>
<script src="static/js/base.js"></script>
<script src="static/js/initial.js"></script>
@stack('body_scripts')
</html>
