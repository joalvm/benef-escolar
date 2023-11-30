import { MDCDialog } from '@material/dialog';

export enum AlertTypes {
    CONFIRM='confirm',
}

export interface AlertOptions {
    message: string;
    btnText: string;
    callback: Function;
}

class Alert {
    public choise: boolean;

    constructor(private options: AlertOptions) {
        const element = this.element();

        document.body.append(element);

        let dialog = new MDCDialog(element);
        dialog.scrimClickAction = null;

        dialog.listen('MDCDialog:closing', () => {
            if (this.choise) {
                this.options.callback();
            }
        });
        dialog.listen('MDCDialog:closed', () => {
            dialog.root.remove();
            dialog.destroy();

            dialog = undefined;
            this.choise = undefined;
        });

        dialog.open();
    }

    private element(): HTMLDivElement {
        const dialog = createElement('div', {className: ['mdc-dialog']});
        const scrim = createElement('div', {className: ['mdc-dialog__scrim']});
        const container = createElement('div', {className: ['mdc-dialog__container']});
        const surface = this.surface();
        const content = createElement('div', {
            className: ['mdc-dialog__content'],
            id: 'confirm-content'
        });
        const actions = createElement('div', {className: ['mdc-dialog__actions']});
        const btnCancel = this.btnAction('Cancelar', 'cancel');
        const btnAccept = this.btnAction(this.options.btnText, 'discard');

        btnAccept.addEventListener('click', () => {this.choise = true});
        btnCancel.addEventListener('click', () => {this.choise = false});

        content.textContent = this.options.message;

        actions.append(btnCancel, btnAccept);
        surface.append(content, actions);
        container.append(surface);
        dialog.append(container, scrim);

        return dialog;
    }

    private surface(): HTMLDivElement {
        return createElement('div', {
            className: ['mdc-dialog__surface'],
            role: 'alertdialog',
            aria: {
                model: 'true',
                labelledby: 'confirm-title',
                describedby: 'confirm-content'
            }
        });
    }

    private btnAction(text: string, action: string) {
        const content = createElement('span', {className: ['mdc-button__label']});
        const btn = createElement('button', {
            className: ['mdc-button', 'mdc-dialog__button'],
            dataset: {mdcDialogAction: action}
        });

        content.textContent = text;

        btn.append(
            createElement('div', {className: ['mdc-button__ripple']}),
            content
        );

        return btn;
    }
}

window.Alert = Alert;

export default Alert;
