interface Data {
    current_password: string;
    password: string;
    confirm_password: string;
}

const rules = {
    current_password: ['required', 'string'],
    password: ['required', 'string', 'min:8', 'same:confirm_password'],
    confirm_password: ['required', 'string', 'min:8', 'same:password'],
};

const labels = {
    current_password: 'Contraseña actual',
    password: 'Nueva Contraseña',
    confirm_password: 'Confirmar Contraseña'
};
class ChangePassword {
    private id: number;
    private btnSave: HTMLButtonElement;
    private inputs: Map<string, MDCTextField> = new Map();
    private data: Data = {
        current_password: '',
        password: '',
        confirm_password: ''
    };

    constructor(userId: number) {
        this.id = userId;

        this.init();
        this.initEvents();
    }

    private async onClickSaveData(event: Event) {
        const valid = new Validator(this.data, rules);

        valid.setAttributeNames(labels);

        if (!valid.check()) {
            Notifier.error(this.transformErrors(valid), 'Verificar');
            return;
        }

        this.btnSave.disabled = true;

        const response = await Http.api().put(`persons/users/${this.id}`, this.data);

        this.btnSave.disabled = false;

        if (response.error) {
            Notifier.error('No se ha podido modificar la información');
            return;
        }

        this.inputs.forEach(element => {
            element.value = '';
            element.valid = true;
        });

        this.inputs.get('current_password').focus();

        this.data = {
            current_password: '',
            password: '',
            confirm_password: ''
        };

        Notifier.success('La acción se realizó satisfactoriamente.');
    }

    private transformErrors(valid: Validator.Validator<Partial<Data>>): string {
        const msg_errors: string[] = [];
        const errors = valid.errors.all();

        for (const name in errors) {
            if (Object.prototype.hasOwnProperty.call(errors, name)) {
                this.inputs.get(name).valid = false;
                errors[name].forEach(msg => msg_errors.push(msg));
            }
        }

        return '- ' + msg_errors.join('<br />- ');
    }

    private onChangeInputText(name: string, event: Event) {
        this.setData(name, (event.target as HTMLInputElement).value.trim());
    }

    private init() {
        document
            .querySelectorAll('.input_text.change_password')
            .forEach((element) => {
                const input: HTMLInputElement = element.querySelector('input');

                this.setData(input.name, input.value.trim());

                this.inputs.set(input.name, new MDCTextField(element));
            });

        this.btnSave = document.getElementById('btn-change_password') as HTMLButtonElement;
    }

    private initEvents() {
        this.inputs.forEach((textfield, name) => {
            textfield.listen('change', this.onChangeInputText.bind(this, name));
        });

        this.btnSave.addEventListener('click', this.onClickSaveData.bind(this));
    }

    private setData(name: string, value: any) {
        this.data = { ...this.data, [name]: value };
    }
}

export default ChangePassword;
