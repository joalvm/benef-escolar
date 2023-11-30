<div id="dg-periods-form" class="mdc-dialog">
    <div class="mdc-dialog__container">
        <div class="mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title"
            aria-describedby="my-dialog-content">
            <h2 class="mdc-dialog__title" id="my-dialog-title">Información Personal</h2>
            <div id="form-register" class="mdc-dialog__content">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada
                            campo.</span>
                    </div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <label id="input-name" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-name">Nombre</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="text" id="txname" name="name" required class="mdc-text-field__input"
                                aria-labelledby="lbl-name">
                        </label>
                    </div>
                    <div
                        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-start_date" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-start_date">F. Inicio</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="date" id="txtstart_date" name="start_date" required
                                class="mdc-text-field__input" aria-labelledby="lbl-start_date">
                        </label>
                    </div>
                    <div
                        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-finish_date" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-finish_date">F. Fin</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="date" id="txtfinish_date" name="finish_date" required
                                class="mdc-text-field__input" aria-labelledby="lbl-finish_date">
                        </label>
                    </div>
                    <div
                        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-amount_bonds" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-amount_bonds">Cantidad de bonos</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="number" id="txtamount_bonds" name="amount_bonds" required
                                class="mdc-text-field__input" aria-labelledby="lbl-amount_bonds">
                        </label>
                    </div>
                    <div
                        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-max_amount_loan" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-max_amount_loan">Importe máximo de
                                        préstamo</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="number" id="txtmax_amount_loan" name="max_amount_loan" required
                                class="mdc-text-field__input" aria-labelledby="lbl-max_amount_loan">
                        </label>
                    </div>
                    <div
                        class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-max_children" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-max_children">N° Max. de hijos.</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="number" id="txtmax_children" name="max_children" required value="4" class="mdc-text-field__input" aria-labelledby="lbl-max_children">
                        </label>
                    </div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <div class="input_radio_button" name="active">
                            <label>Activo</label>
                            <br>
                            <div id="form-active-yes" class="mdc-form-field">
                                <div class="mdc-radio">
                                    <input class="mdc-radio__native-control" type="radio" id="input-active-yes"
                                        value="1" name="active" checked>
                                    <div class="mdc-radio__background">
                                        <div class="mdc-radio__outer-circle"></div>
                                        <div class="mdc-radio__inner-circle"></div>
                                    </div>
                                    <div class="mdc-radio__ripple"></div>
                                </div>
                                <label for="input-active-yes">SI</label>
                            </div>
                            <div id="form-active-no" class="mdc-form-field">
                                <div class="mdc-radio">
                                    <input class="mdc-radio__native-control" type="radio" id="input-active-no" value="0"
                                        name="active">
                                    <div class="mdc-radio__background">
                                        <div class="mdc-radio__outer-circle"></div>
                                        <div class="mdc-radio__inner-circle"></div>
                                    </div>
                                    <div class="mdc-radio__ripple"></div>
                                </div>
                                <label for="input-active-no">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mdc-dialog__actions">
                <button type="button" id="btn-send" class="mdc-button mdc-dialog__button">
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
