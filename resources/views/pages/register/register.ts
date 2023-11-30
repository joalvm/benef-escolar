import {MDCTextField} from '@material/textfield';
import {MDCDialog} from '@material/dialog';

enum Gender {
    FEMENINO='femenino',
    MASCULINO='masculino'
}

enum Status {
    PENDING='pending',
    REGISTERED='registered',
    VERIFIED='verified',
}

interface Person {
    id: number;
    names: string;
    gender: Gender,
    status: Status
}

interface RegisterData {
    id: number;
    email: string;
}

class Register {
    private formDocument: HTMLFormElement;
    private formRegister: HTMLFormElement;
    private dialogElement: HTMLDivElement;
    private inputSearch: MDCTextField;
    private inputEmail: MDCTextField;
    private dialog: MDCDialog;
    private btnSearch: HTMLButtonElement;
    private btnSend: HTMLButtonElement;

    private currentId: number;

    constructor() {
        this.init();
        this.addEvents();
    }

    private async verifiedDNI() {
        if (!this.inputSearch.value.trim().match(/[0-9]{8}/)) {
            Notifier.error('El dato es incorrecto.', 'validación');
            return;
        }

        this.inputSearch.disabled = true;

        const uri = `/register/${this.inputSearch.value}`;
        const response = await Http.api().get<Person>(uri);


        if (response.error) {
            this.inputSearch.disabled = false;
            this.inputSearch.focus();

            Notifier.error(response.message, 'Error');

            return;
        }

        if (response.data.status == Status.REGISTERED) {
            Notifier.warning('Sus credenciales han sido enviados al correo electrónico.', 'Advertencia!');

            return;
        } else if (response.data.status == Status.VERIFIED) {
            Notifier.warning('Su proceso de registro a concluido, puede acceder a la plataforma.', 'Advertencia!');

            return;
        }

        this.currentId = response.data.id;
        this.dialogElement.querySelector('.name-text').textContent = response.data.names;
        this.dialog.open();
    }

    private async registerEmail() {
        if (!this.isEmail(this.inputEmail.value.trim())) {
            Notifier.error('Formato de email incorrecto');
            return;
        }

        this.inputEmail.disabled = true;

        const response = await Http.api().post<RegisterData, boolean>('register', {
            id: this.currentId,
            email: this.inputEmail.value.trim()
        });

        if (response.error) {
            Notifier.error(response.message);
            return;
        } else {
            Notifier.success('Hemos enviado las credenciales de acceso a su correo electrónico.', 'Enhorabuena!');
            window.setTimeout(() => {
                window.location.href = './login';
            }, 2000);
        }

        this.dialog.close('success');
    }

    private async onSubmitVerifiedDNI(event: Event) {
        event.preventDefault();

        await this.verifiedDNI();
    }

    private async onSubmitRegister(event: Event) {
        event.preventDefault();

        await this.registerEmail();
    }

    private onClosedDialog(event: Event) {
        this.inputSearch.value = '';
        this.inputSearch.disabled = false;
        this.inputSearch.focus();

        this.inputEmail.value = '';
        this.currentId = null;
        this.dialogElement.querySelector('.name-text').textContent = '';
    }

    private async onClickSearch(event: MouseEvent) {
        event.preventDefault();

        await this.verifiedDNI();
    }

    private async onClickSendEmail(event: MouseEvent) {
        event.preventDefault();

        await this.registerEmail();

        event.stopImmediatePropagation();

    }

    private init(): void {
        this.formDocument = document.getElementById('form-document') as HTMLFormElement;
        this.formRegister = document.getElementById('form-register') as HTMLFormElement;
        this.dialogElement = document.getElementById('dialog-confirm') as HTMLDivElement;

        this.btnSearch = this.formDocument.querySelector('#btn-search');
        this.btnSend = document.getElementById('btn-send') as HTMLButtonElement;

        this.dialog = new MDCDialog(this.dialogElement);
        this.inputSearch = new MDCTextField(
            this.formDocument.querySelector('#input-dni')
        );
        this.inputEmail = new MDCTextField(
            this.dialogElement.querySelector('#email-confirm')
        );
    }

    private addEvents(): void {
        this.formDocument.addEventListener('submit', this.onSubmitVerifiedDNI.bind(this));
        this.formRegister.addEventListener('submit', this.onSubmitRegister.bind(this));
        this.btnSearch.addEventListener('click', this.onClickSearch.bind(this));
        this.btnSend.addEventListener('click', this.onClickSendEmail.bind(this));

        this.dialog.listen('MDCDialog:closed', this.onClosedDialog.bind(this));
        this.dialog.scrimClickAction = '';
    }

    private isEmail(email: string) {
        return /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/.test(email);
    }
}

new Register();
