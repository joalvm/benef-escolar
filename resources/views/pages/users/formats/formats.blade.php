@extends('templates.admin.admin')

@section('title', 'Formatos de descarga')

@section('content')
<div class="mdc-layout-grid__inner">
    <!--CARDS-->
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-12-phone">
        <div class="mdc-card card-amount">
            <div class="mdc-card__content">
                <h2 class="mdc-typography--headline4">Formato #1</h2>
                <h2 class="mdc-typography--body2">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Itaque perferendis laudantium est at deserunt, nesciunt modi debitis optio quibusdam! Doloribus ex ea rem. Alias earum molestiae adipisci quo quibusdam mollitia?</h2>
                <a href="{{url('formats/coquito.pdf')}}" target="_blank" class="mdc-button mdc-button--raised">
                    <span class="mdc-button__ripple"></span>
                    <i class="material-icons mdc-button__icon" aria-hidden="true">download</i>
                    <span class="mdc-button__label">DESCARGAR</span>
                </a>
            </div>
        </div>
    </div>
    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-6-tablet mdc-layout-grid__cell--span-12-phone">
        <div class="mdc-card card-amount">
            <div class="mdc-card__content">
                <h2 class="mdc-typography--headline4">Formato #2</h2>
                <h2 class="mdc-typography--body2">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Itaque perferendis laudantium est at deserunt, nesciunt modi debitis optio quibusdam! Doloribus ex ea rem. Alias earum molestiae adipisci quo quibusdam mollitia?</h2>
                <a href="{{url('formats/constitucion1993.pdf')}}" target="_blank" class="mdc-button mdc-button--raised">
                    <span class="mdc-button__ripple"></span>
                    <i class="material-icons mdc-button__icon" aria-hidden="true">download</i>
                    <span class="mdc-button__label">DESCARGAR</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
