import {MDCDialog} from '@material/dialog';
import TableChildren from './tableChildren';

enum Genders {
    FEMENINO='femenino',
    MASCULINO='masculino'
}

interface Children {
    id?: number;
    name: string;
    paternal_surname: string;
    maternal_surname: string;
    gender: Genders;
    birth_date: string;
}

const rules = {
    name: ['required', 'string'],
    paternal_surname: ['required', 'string'],
    maternal_surname: ['required', 'string'],
    gender: ['required', 'string'],
    birth_date: ['required', 'date']
};

class DialogForm {
    private id: number = undefined;
    private data: Children = this.defaultData();

    private btnSave: HTMLButtonElement;
    private inputs: Map<string, MDCTextField> = new Map();
    private selects: Map<string, HTMLSelectElement> = new Map();
    private table: TableChildren;

    public action: string = 'create';
    public dialog: MDCDialog;

    constructor(personId: number) {
        this.init();
        this.initEvent();
    }

    private async onClickSaveData() {
        let response: Response<unknown>;
        const valid = new Validator(this.data, rules);

        if (!valid.check()) {
            Notifier.error(this.transformErrors(valid), 'Verificar información:');
            return;
        }

        this.btnSave.disabled = true;

        if (this.action === 'create') {
            response = await Http.api().post('children', this.data);
        } else {
            response = await Http.api().put(`children/${this.id}`, this.data);
        }

        this.btnSave.disabled = false;

        if (response.error) {
            Notifier.error(response.message);
            return;
        }

        Notifier.success('La acción se realizó satisfactoriamente.');
        this.dialog.close();
        this.table.loadData();
    }

    public setTable(table: TableChildren) {
        this.table = table;
    }

    public openForUpdate(item: Children) {
        this.action = 'update';

        this.id = item.id;

        for (const name in item) {
            if (Object.prototype.hasOwnProperty.call(item, name)) {
                const value = item[name as keyof Children] as string;

                if (this.inputs.has(name)) {
                    this.inputs.get(name).value = value;
                } else {
                    const select = this.dialog.root.querySelector(`select[name=${name}]`) as HTMLSelectElement;

                    if (select) {
                        select.value = value;
                    }
                }

                this.setData(name, value);
            }

            this.dialog.open();
        }
    }

    private init() {
        this.dialog = new MDCDialog(document.getElementById('dg-child-form'));

        this.dialog.root.querySelectorAll('.input_text').forEach(element => {
            const input = element.querySelector<HTMLInputElement>('input');

            this.setData(input.name, input.value.trim());

            this.inputs.set(input.name, new MDCTextField(element));
        });

        this.dialog.root.querySelectorAll('select').forEach(select => {
            this.setData(select.name, select.value);

            this.selects.set(select.name, select);
        });

        this.btnSave = this.dialog.root.querySelector('#btn-send');

        this.dialog.scrimClickAction = '';
    }

    private initEvent() {
        this.btnSave.addEventListener('click', this.onClickSaveData.bind(this));

        this.inputs.forEach((element, name) => {
            element.listen('change', this.onChangeInputText.bind(this, name));
        });

        this.selects.forEach((element, name) => {
            element.addEventListener('change', this.onChangeSelect.bind(this, name));
        })

        this.dialog.listen('MDCDialog:closed', this.onClosedDialog.bind(this));
    }

    private onChangeInputText(name: string, event: Event) {
        this.setData(name, (event.target as HTMLInputElement).value.trim());
    }

    private onChangeSelect(name: string, event: Event) {
        this.setData(name, (event.target as HTMLSelectElement).value);
    }

    private onClosedDialog() {
        this.data = this.defaultData();
        this.action = 'create';

        this.inputs.forEach((element, name) => {
            element.value = '';
            element.valid = true;
        });
    }

    private setData(name: string, value: any) {
        this.data = { ...this.data, [name]: value };
    }

    private transformErrors(valid: Validator.Validator<Partial<Children>>): string {
        const msg_errors: string[] = [];
        const errors = valid.errors.all();

        for (const name in errors) {
            if (Object.prototype.hasOwnProperty.call(errors, name)) {
                errors[name].forEach(msg => msg_errors.push(msg));
            }
        }

        return '- ' + msg_errors.join('<br />- ');
    }

    private defaultData(): Children {
        return {
            name: '',
            paternal_surname: '',
            maternal_surname: '',
            gender: Genders.FEMENINO,
            birth_date: ''
        };
    }
}

export default DialogForm;
