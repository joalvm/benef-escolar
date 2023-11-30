<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;


$needPhone = empty(Arr::get($person, 'phone'));
$needBoat = empty(Arr::get($person, 'boat'));

?>

<div class="mdc-card mdc-card--outlined" style="margin-bottom: 24px">
    <div class="mdc-card__content" style="padding: 12px">
        @if ($status == 'no_requested')
        <span class="mdc-typography--headline6" style="color: #0065e9;">PASO 2 (Opcional):</span>
        <br>
        @endif
        <small class="mdc-typography--headline6" style="font-size: 1rem;">DESCARGAR FORMATOS</small>
        <br>
        <br>
        <a href="formats/formato_bono_escolar_2021.pdf" target="_blank" class="mdc-button mdc-button--outlined">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">FORMATO BONO TRIPULANTE</div>
        </a>
        <a href="formats/formato_de_prestamo_escolar_2021.pdf" target="_blank" class="mdc-button mdc-button--outlined">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">FORMATO PRÉSTAMO TRIPULANTE</div>
        </a>
        <a href="formats/formato_prestamo_empleados_2021.pdf" target="_blank" class="mdc-button mdc-button--outlined">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">FORMATO PRÉSTAMO EMPLEADOS</div>
        </a>
        <a href="formats/formato_prestamo_obreros_2021.pdf" target="_blank" class="mdc-button mdc-button--outlined">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">FORMATO PRÉSTAMO OBREROS</div>
        </a>
        <a href="formats/formato_bono_empleados_obreros_2021.pdf" target="_blank" class="mdc-button mdc-button--outlined">
            <div class="mdc-button__ripple"></div>
            <div class="mdc-button__label">FORMATO BONO EMPLEADOS Y OBREROS</div>
        </a>
    </div>
</div>

@if ($status !== 'no_requested')
<!--CUANDO YA EXISTE UNA PETICION-->
<ul class="mdc-list mdc-list--image-list mdc-list--two-line list-files">
    <h3 class="mdc-list-group__subheader">Documentos Firmados</h3>

    @foreach ($requests['documents'] ?? [] as $document)
    <li id="list-_wd1mdxtjn" class="mdc-list-item">
        <a href="{{ file_url($document['file']) }}" target="_blank" class="mdc-list-item__graphic" style="background-image: url({{ file_url($document['file']) }})"></a>
        <span class="mdc-list-item__text">
            <span class="mdc-list-item__primary-text status-{{$document['status']}}">
                {{ status_message($document['status']) }}
            </span>
            <span class="mdc-list-item__secondary-text">{{$document['file']}}</span>
        </span>
        <span class="mdc-list-item__meta" aria-hidden="true">
            @if ($document['status'] == 'observed')
            <button data-id="{{$document['id']}}" class="btn-reload mdc-icon-button material-icons status-{{$document['status']}}">cached</button>
            @endif
        </span>
    </li>
    @endforeach
</ul>
<div style="text-align: right">
    <button id="btn-new_formats" class="mdc-button mdc-button--raised">
        <span class="mdc-button__label">modificar nuevos formatos</span>
    </button>
</div>
<input id="input-new-formats" multiple data-id="{{$requests['id']}}" type="file" style="display: none">
<input id="input-observed-files" type="file" style="display: none">
@else
    @if (count($children) > 0)
    <!--CUANDO TIENE HIJOS REGISTRADOS PUEDE PROCEDER A INICIAR UNA SOLICITUD DE BONO-->
    <div id="frm-person_requests" class="mdc-card mdc-card--outlined">
        <div class="mdc-card__content">
            <span class="mdc-typography--headline6" style="color: #0065e9;">PASO 3:</span>
            <br>
            <small class="mdc-typography--headline6" style="font-size: 1rem;">ADJUNTAR FORMATO DE PRÉSTAMO Y BONO</small>
            <br>
            <span class="mdc-typography--body2">Asegúrese de completar toda la información de sus hijos.</span>
            <br><br>
            <div class="mdc-typography--Subtitle1">Imagen de formatos firmados por el colaborador.</div>
            <ul id="bonds-list" class="mdc-list mdc-list--image-list mdc-list--two-line list-files"></ul>
            <button type="button" class="request-empty-list upload mdc-button mdc-button--outlined">
                <div class="mdc-button__ripple"></div>
                <i class="material-icons mdc-button__icon" aria-hidden="true">upload</i>
                <div class="mdc-button__label">AGREGAR IMAGENES DE LOS FORMATOS FIRMADOS</div>
            </button>
            <input type="file" style="display: none">
        </div>
        @if ($needPhone or $needBoat)
        <div class="mdc-card__content">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                    <span class="mdc-typography--subtitle2">Datos requeridos</span>
                </div>
                @if ($needPhone)
                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
                    <label id="input-phone" class="mdc-text-field mdc-text-field--outlined">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="lbl-phone">Telefono del trabajador</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <input type="phone" id="txtphone" name="phone" required class="input-text mdc-text-field__input" aria-labelledby="lbl-phone">
                    </label>
                </div>
                @endif
                @if ($needBoat AND Arr::get($person, 'unit.id') == 6)
                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
                    <div class="select">
                        <select id="cbo-boats" name="boats" class="input-select select-text" required>
                        <option value="" disabled selected>Seleccione...</option>
                        @foreach ($boats as $boat)
                            <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                        @endforeach
                        </select>
                        <label class="select-label">Embarcación pesquera actual</label>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        <div class="mdc-card__actions" style="align-self: flex-end;">
            <div class="mdc-card__action-buttons">
                <button id="btn-init_request" type="button" disabled
                    class="mdc-button mdc-button--raised mdc-card__action mdc-card__action--button">
                    <div class="mdc-button__ripple"></div>
                    <div class="mdc-button__label">INICIAR SOLICITUD</div>
                </button>
            </div>
        </div>
    </div>
    @else
    <!--SI NO TIENE HIJOS REGISTRADOS-->
    <div class="mdc-card__content">
        <span class="mdc-typography--subtitle1 request-empty-list">La lista de hijos está vacía.</span>
    </div>
    @endif
@endif
