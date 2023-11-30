<?php
    $countChild = 0;
?>

<div class="mdc-data-table">
    <div class="mdc-data-table__table-container">
        <table id="td-children" class="mdc-data-table__table" aria-label="Children">
            <thead>
                <tr class="mdc-data-table__header-row">
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nombres</th>
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Nivel Educativo</th>
                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric"
                        role="columnheader" scope="col">Monto</th>
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Pack</th>
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Ciudad</th>
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Estado</th>
                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="mdc-data-table__content">
                @foreach ($children as $child)
                @if ( $countChild < Arr::get($period, 'max_children') )
                <tr class="mdc-data-table__row">
                    <th class="mdc-data-table__cell" scope="row">{{ Arr::get($child, 'fullname', '-') }}</th>
                    <td class="mdc-data-table__cell">
                        {{ Arr::get($child, 'request.education_level.name', 'No Definido') }}</td>
                    <td class="mdc-data-table__cell mdc-data-table__cell--numeric">
                        @if (Arr::get($child, 'request') == null)
                        {{ 'No definido' }}
                        @else
                        {{ (Arr::get($child, 'request.get_loan')) ? 'S/.' . $period['max_amount_loan'] : 0 }}
                        @endif
                    </td>
                    <td class="mdc-data-table__cell">
                        {{ (Arr::get($child, 'request') == null) ? 'No definido' : (Arr::get($child, 'request.get_pack') ? 'SI' : 'NO') }}
                    </td>
                    <td class="mdc-data-table__cell">
                        {{ (Arr::get($child, 'request') == null) ? 'No definido' : Arr::get($child, 'request.plant.name') }}
                    </td>
                    <td class="mdc-data-table__cell">
                        <span class="badge-status {{Arr::get($child, 'request.status')}}">{{ Arr::get($child, 'request') == null ? 'No definido' : status_message(Arr::get($child, 'request.status'), true) }}</span>
                    </td>
                    <td class="mdc-data-table__cell" style="text-align: right">
                        <a href="{{url('user/bonds/children/' . Arr::get($child, 'id'))}}" class="mdc-button mdc-button--unelevated">
                            <span class="mdc-button__ripple"></span>
                            <i class="material-icons mdc-button__icon" aria-hidden="true">edit</i>
                            <span class="mdc-button__label">modificar info.</span>
                        </a>
                    </td>
                </tr>
                <?php $countChild++; ?>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
