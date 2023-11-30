import { MDCSelect } from '@material/select';

interface Data {
    names: string;
    dni: string;
    email: string;
    gender: string;
    phone: string;
    birth_date: string;
}

const rules = {
    names: ['required', 'string'],
    dni: ['required', 'string', 'size:8'],
    email: ['required', 'string', 'email'],
    gender: ['required', 'string'],
    phone: ['required', 'string'],
    birth_date: ['required', 'date']
}

const labels = {
    names: 'Nombres',
    email: 'Correo Electrónico',
    gender: 'Sexo',
    birth_date: 'Fecha de nacimiento',
    phone: 'Celular'
}

class Information {
    private id?: number;
    private data: Partial<Data> = {};
    private btnSave: HTMLButtonElement;

    private inputs: Map<string, MDCTextField> = new Map();
    private selects: Map<string, MDCSelect> = new Map();

    constructor(personId: number) {
        this.id = personId;

        this.init();
        this.initEvents();
    }

    private async onClickSave(event: MouseEvent) {
        this.selects.forEach((select, name) => {
            this.setData(name, select.value);
        });

        const valid = new Validator(this.data, rules);

        valid.setAttributeNames(labels);

        if (!valid.check()) {
            Notifier.error('Verifique la información:<br>' + this.transformErrors(valid));
            return;
        }

        this.btnSave.disabled = true;

        const response = await Http.api().put<Partial<Data>, any>(`persons/${this.id}`, this.data);

        this.btnSave.disabled = false;

        if (response.error) {
            Notifier.error('No se ha podido modificar la información');
            return;
        }

        return Notifier.success('La acción se realizó satisfactoriamente.');
    }

    private transformErrors(valid: Validator.Validator<Partial<Data>>): string {
        const msg_errors: string[] = [];
        const errors = valid.errors.all();

        for (const name in errors) {
            if (Object.prototype.hasOwnProperty.call(errors, name)) {
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
            .querySelectorAll('.input_text.information')
            .forEach((element) => {
                const input: HTMLInputElement = element.querySelector('input');

                this.setData(input.name, input.value.trim());

                this.inputs.set(input.name, new MDCTextField(element));
            });

        document
            .querySelectorAll('.input-select.information')
            .forEach(element => {
                const select = element as HTMLDivElement;
                const name = select.dataset['name'];

                this.selects.set(name, new MDCSelect(select));

                this.setData(name, this.selects.get(name).value)
            });

        this.btnSave = document.getElementById('btn-save_info') as HTMLButtonElement;
    }

    private setData(name: string, value: any) {
        this.data = { ...this.data, [name]: value };
    }

    private initEvents() {
        this.inputs.forEach((textfield, name) => {
            textfield.listen('change', this.onChangeInputText.bind(this, name));
        });

        this.btnSave.addEventListener('click', this.onClickSave.bind(this));
    }
}

export default Information;
