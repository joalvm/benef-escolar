@extends('templates.admin.admin')
@section('title', 'Aprobaciones')

@section('content')
<div class="approval mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
            <h3 class="mdc-typography--headline4" style="margin-top: 0">{{$person->get('names')}}</h3>
            <span class="dni mdc-typography--button">
                <span>estado:<span>
                <span class="badge-status {{$request->get('status')}}">
                    {{status_message($request->get('status'), true)}}
                </span>
            </span>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
            <span class="dni mdc-typography--body1"><strong>DNI:</strong> {{$person->get('dni')}}</span><br>
            <span class="dni mdc-typography--body1"><strong>UNIDAD:</strong> {{$person->get('unit')['name']}}</span><br>
            <span class="dni mdc-typography--body1"><strong>CELULAR:</strong> {{$person->get('phone')}}</span>
            @if ($person->has('boat'))
            @endif
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
            <h3 class="mdc-typography--headline6" style="margin: 0;">Hijos</h3>
        </div>

        @foreach ($children as $child)
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-phone">
            <div class="mdc-card card-child">
                <div class="mdc-card__content">
                    <div class="mdc-layout-grid__inner">
                        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                            <div class="mdc-layout-grid__inner">
                                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-8">
                                    <h3 class="mdc-typography--body1" style="margin: 0;">{{ Arr::get($child, 'child.fullname') }}</h3>
                                    <span class="mdc-typography--subtitle2">Sexo: {{ Arr::get($child, 'child.gender') }}</span>
                                </div>
                                <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-4" style="text-align: right">
                                    <a href="admin/requests/{{ $child['id'] }}/children/edit" class="card-icon edit mdc-icon-button material-icons">edit</a>
                                    <button class="delete-child card-icon delete mdc-icon-button material-icons" data-id="{{Arr::get($child, 'id')}}" data-child="{{Arr::get($child, 'child.fullname')}}">delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                            <p class="child-data">
                                <span class="mdc-typography--body2"><strong>Fecha de nacimiento:
                                    </strong>{{ Arr::get($child, 'child.birth_date') }}</span>
                                <span class="mdc-typography--body2"><strong>Edad:</strong>{{ get_age(Arr::get($child, 'child.birth_date')) . ' Años' }}</span>
                                <span class="mdc-typography--body2"><strong>Nivel Educativo:
                                    </strong>{{ Arr::get($child, 'education_level.name') }}</span>
                                <span class="mdc-typography--body2"><strong>Prestamo: </strong>{{ Arr::get($child, 'get_loan') ? 'SI' : 'NO' }}</span>
                                <span class="mdc-typography--body2"><strong>Pack Educativo:
                                    </strong>{{ Arr::get($child, 'get_pack') ? 'SI' : 'NO' }}</span>
                                <span class="mdc-typography--body2"><strong>Tipo de entrega:
                                    </strong>{{ Arr::get($child, 'delivery_type') == 'pick_in_plant' ? 'Recojo en planta' : 'Delivery' }}</span>
                                @if (Arr::get($child, 'delivery_type') == 'pick_in_plant')
                                <span class="mdc-typography--body2"><strong>Lugar de recojo: </strong>{{ Arr::get($child, 'plant.name') }}</span>
                                @else
                                <p class="responsable">
                                    <span><strong>Nombres: </strong>{{ Arr::get($child, 'responsable_name') }}</span>
                                    <span><strong>DNI: </strong>{{ Arr::get($child, 'responsable_dni') }}</span>
                                    <span><strong>Teléfono: </strong>{{ Arr::get($child, 'responsable_phone') }}</span>
                                    <span><strong>Lugar de recojo: </strong>{{ Arr::get($child, 'plant.name') }}</span>
                                </p>
                                @endif
                            </p>
                        </div>
                        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                            <ul class="mdc-list mdc-list--two-line mdc-list--avatar-list">
                                @foreach ($child['documents'] ?? [] as $document)
                                <x-ListDocument id="{{$document['id']}}"
                                    file="{{$document['file']}}"
                                    status="{{ $document['status'] }}"
                                    type="child_document"
                                    created-at="{{ $document['last_update'] }}"
                                    observation="{{ Arr::get($document, 'observation') }}"/>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
            <h3 class="mdc-typography--headline6" style="margin: 0;">Formatos Firmados</h3>
        </div>
        <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
            <ul class="format-list mdc-list mdc-list--two-line mdc-list--avatar-list">
                @foreach ($documents as $document)
                <x-ListDocument id="{{$document['id']}}"
                    file="{{$document['file']}}"
                    status="{{ $document['status'] }}"
                    type="person_document"
                    created-at="{{ $document['last_update'] }}"
                    observation="{{ Arr::get($document, 'observation') }}"/>
                @endforeach
            </ul>
        </div>

    </div>

    @include('pages.admin.requests.approval.dialog')
</div>
@endsection

@push('header_styles')
<link rel="stylesheet" href="static/css/admin.requests.approval.css">
@endpush

@push('body_scripts')
<script type="text/javascript" src="static/js/admin.requests.approval.js"></script>
@endpush
