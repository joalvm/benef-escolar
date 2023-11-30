<div id="dg-education-levels-form" class="mdc-dialog">
    <div class="mdc-dialog__container">
        <div class="mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title"
            aria-describedby="my-dialog-content">
            <h2 class="mdc-dialog__title" id="my-dialog-title">Información Personal</h2>
            <div id="form-register" class="mdc-dialog__content">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
                        <span class="mdc-typography--body2">Agregue y seleccione la información correcta para cada campo.</span>
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
                    <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-12-mobile">
                        <label id="input-amount" class="input_text mdc-text-field mdc-text-field--outlined">
                            <span class="mdc-notched-outline">
                                <span class="mdc-notched-outline__leading"></span>
                                <span class="mdc-notched-outline__notch">
                                    <span class="mdc-floating-label" id="lbl-amount">Importe</span>
                                </span>
                                <span class="mdc-notched-outline__trailing"></span>
                            </span>
                            <input type="number" min="0" id="txtamount" name="amount" required class="mdc-text-field__input"
                                aria-labelledby="lbl-amount">
                        </label>
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
