<div id="dg-approval" class="mdc-dialog">
    <div class="mdc-dialog__container">
        <div class="mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title"
            aria-describedby="my-dialog-content">
            <h2 class="mdc-dialog__title" id="my-dialog-title">Validar Documento</h2>
            <div id="form-register" class="mdc-dialog__content">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada
                            campo.</span>
                    </div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <div id="frm-approval-options" class="mdc-form-field">
                            <div class="mdc-radio">
                                <input class="mdc-radio__native-control" id="rbo-verified" type="radio" name="rbo-approval" checked value="approved">
                                <div class="mdc-radio__background">
                                    <div class="mdc-radio__outer-circle"></div>
                                    <div class="mdc-radio__inner-circle"></div>
                                </div>
                                <div class="mdc-radio__ripple"></div>
                            </div>
                            <label for="rbo-verified">EL DOCUMENTO ES CORRECTO</label>
                            <div class="mdc-radio">
                                <input class="mdc-radio__native-control" id="rbo-observed" type="radio" name="rbo-approval" value="observed">
                                <div class="mdc-radio__background">
                                    <div class="mdc-radio__outer-circle"></div>
                                    <div class="mdc-radio__inner-circle"></div>
                                </div>
                                <div class="mdc-radio__ripple"></div>
                            </div>
                            <label for="rbo-observed">EL DOCUMENTO PRESENTA UNA OBSERVACIÓN</label>
                        </div>
                    </div>
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <label id="txt-observation"
                            class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-observation">Observación</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <span class="mdc-text-field__resizer">
                                <textarea class="mdc-text-field__input" rows="4" cols="40"
                                    aria-label="lbl-observation"></textarea>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mdc-dialog__actions">
                <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="cancel">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Cancelar</span>
                </button>
                <button type="button" id="btn-send" class="mdc-button mdc-dialog__button">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Enviar</span>
                </button>
            </div>
        </div>
    </div>
    <div class="mdc-dialog__scrim"></div>
</div>
