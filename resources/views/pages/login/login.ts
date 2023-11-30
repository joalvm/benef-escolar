import { MDCTextField } from '@material/textfield';
import { MDCTextFieldIcon } from '@material/textfield/icon';
import { MDCRipple } from '@material/ripple';

interface LoginData {
    dni: string;
    password: string;
}

interface Response {
    token: string,
    expire: number
}

class Login {
    private data: LoginData = {
        dni: '',
        password: ''
    };

    private form: HTMLFormElement;
    private txtUsername: MDCTextField;
    private txtPassword: MDCTextField;

    constructor() {
        window.localStorage.clear();

        this.init();
        this.addEvents();
    }

    private async onSubmit(event: Event) {

        if (!this.txtUsername.value.trim().match(/^(\d+){8}$/)) {
            Notifier.error('El DNI debe contener 8 digitos numericos.', 'Validación');
            this.txtUsername.valid = false;
            event.preventDefault();
        }
    }

    private async onChange(event: HashChangeEvent) {
        const element = (event.target as HTMLInputElement);

        this.data = {...this.data, [element.name]: element.value.trim()};
    }

    private init(): void {
        this.form = document.getElementById('login-form') as HTMLFormElement;

        this.txtUsername = new MDCTextField(this.form.querySelector('#form-username'));
        this.txtPassword = new MDCTextField(this.form.querySelector('#form-password'));

        new MDCRipple(this.form.querySelector('#btn-submit'));
    }

    private addEvents(): void {
        this.form.addEventListener('submit', this.onSubmit.bind(this));

        this.txtUsername.listen('change', this.onChange.bind(this));
        this.txtPassword.listen('change', this.onChange.bind(this));
    }
}

new Login();
