import { MDCDialog } from '@material/dialog';
import { MDCFormField } from '@material/form-field';
import { MDCRadio } from '@material/radio';

export enum DocumentStatus {
    PENDING='pending',
    OBSERVED='observed',
    APPROVED='approved',
    CLOSED='closed',
}

interface Data {
    id?: number;
    status?: DocumentStatus;
    type?: string;
}

interface Request {
    status?: DocumentStatus;
    observation?: string;
}

class Approval {
    private dialog: MDCDialog;
    private txtObservation: MDCTextField;
    private radios: Map<string, MDCRadio> = new Map();
    private btnSend: HTMLButtonElement;
    private current: Data = null;
    private data: Request = {
        status: DocumentStatus.APPROVED
    };

    constructor() {
        this.init();
    }

    private init() {
        document.querySelectorAll('button.btn-actions')
        .forEach((element: HTMLButtonElement) => {
            element.addEventListener('click', this.onClickApproval.bind(this));
        });

        document.querySelectorAll('button.btn-notify-email')
        .forEach((element: HTMLButtonElement) => {
            element.addEventListener('click', this.onClickSendEmail.bind(this));
        });

        this.txtObservation = new MDCTextField(document.getElementById('txt-observation'));
        this.txtObservation.disabled = true;

        this.dialog = new MDCDialog(document.getElementById('dg-approval'));
        this.dialog.escapeKeyAction = null;

        const formField = new MDCFormField(document.querySelector('.mdc-form-field'));

        this.dialog.root.querySelectorAll('.mdc-radio').forEach(element => {
            const radio = new MDCRadio(element);
            const name = element.querySelector('input').value;
            formField.input = radio;

            radio.listen('change', this.onChangeApproval.bind(this));

            this.radios.set(name, radio);
        });

        this.dialog.listen('MDCDialog:closed', this.onClosedDialog.bind(this));

        this.btnSend = this.dialog.root.querySelector('#btn-send');

        this.btnSend.addEventListener('click', this.onClickSaveData.bind(this));

        document.querySelectorAll('.delete-child')
        .forEach((element: HTMLButtonElement) => {
            element.addEventListener('click', this.onClickDeleteChild.bind(this));
        });
    }

    private async onClickSaveData() {
        let url: string = '';

        if (this.radios.get('observed').checked && this.txtObservation.value.trim().length == 0) {
            Notifier.error('Debe asignar una observación');
            return;
        }

        this.data.observation = this.txtObservation.value.trim();

        url = this.current.type == 'person_document'
            ? `persons/requests/documents/${this.current.id}`
            : `children/requests/documents/${this.current.id}`;

        this.btnSend.disabled = true;

        const response = await Http.api().put(url, this.data);

        this.btnSend.disabled = false;

        if (response.error) {
            Notifier.error(response.message);
            return;
        }

        Notifier.success('La información ha sido guardada');
        this.dialog.close();
        window.location.reload();
    }

    private async onClickSendEmail(event: MouseEvent) {
        const button = event.currentTarget as HTMLButtonElement;
        let url = (button.dataset.type == 'person_document')
            ? 'persons/notify/document'
            : 'children/notify/document';

        button.disabled = true;

        const response = await Http.api().post(url, {'documents_id': button.dataset.id});

        button.disabled = false;

        if (!response.error) {
            Notifier.success('El mensaje se envió correctamente.');
        }
    }

    private async onClickDeleteChild(event: MouseEvent) {
        const button = event.currentTarget as HTMLButtonElement;
        const dataset = button.dataset;

        new Alert({
            message: `¿Desea eliminar a ${dataset.child}?`,
            btnText: 'Eliminar',
            callback: async () => {
                const response = await Http.api().delete(`children/requests/${dataset.id}`);

                if (!response.error) {
                    Notifier.success('Espere un momento...', 'Solicitud eliminada');
                    window.setTimeout(() => {
                        window.location.reload();
                    });
                } else {
                    Notifier.error(response.message, 'Error al eliminar');
                }
            }
        });
    }

    private onChangeApproval(event: Event) {
        const value = (event.currentTarget as HTMLDivElement).querySelector('input').value;

        this.txtObservation.disabled = !(value == 'observed');
        this.txtObservation.value = '';
        if (!this.txtObservation.disabled) {
            this.txtObservation.focus();
        }

        this.data.status = value as DocumentStatus;
    }

    private onClickApproval(event: MouseEvent) {
        const button = event.currentTarget as HTMLButtonElement;

        if (button.dataset.status == DocumentStatus.OBSERVED) {
            this.radios.get(button.dataset.status).checked = true;
            this.txtObservation.value = button.dataset.observation;
            this.txtObservation.disabled = false;
            this.data = {
                status: button.dataset.status,
                observation: button.dataset.observation
            };
        }

        this.current = button.dataset;
        this.dialog.open();
    }

    private onClosedDialog() {
        this.txtObservation.value = '';
        this.txtObservation.disabled = true;
        this.radios.get('approved').checked = true;
        this.current = {status: DocumentStatus.APPROVED};
    }
}

new Approval();

export default Approval;
