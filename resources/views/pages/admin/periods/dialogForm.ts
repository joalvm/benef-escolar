import {MDCDialog} from '@material/dialog';
import {MDCFormField} from '@material/form-field';
import {MDCRadio} from '@material/radio';
import TablePeriods from './tablePeriods';

interface Period {
    id?: number;
    name: string;
    start_date: string;
    finish_date: string;
    amount_bonds: number;
    max_amount_loan: number;
    max_children: number;
    active: boolean;
}

const rules = {
    name: ['required', 'string'],
    start_date: ['required', 'string', 'date','before_or_equal:finish_date'],
    finish_date: ['required', 'string', 'date','after_or_equal:start_date'],
    amount_bonds: ['required', 'integer'],
    max_amount_loan: ['required', 'integer'],
    max_children: ['required', 'integer'],
    active: ['required', 'boolean'],
};

class DialogForm {
    private id: number = undefined;
    private data: Period = this.defaultData();

    private btnSave: HTMLButtonElement;
    private inputs: Map<string, MDCTextField> = new Map();
    private selects: Map<string, HTMLSelectElement> = new Map();
    private radios: Map<string, HTMLInputElement> = new Map();
    private table: TablePeriods;

    public action: string = 'create';
    public dialog: MDCDialog;

    constructor() {
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
            response = await Http.api().post('periods', this.data);
        } else {
            response = await Http.api().put(`periods/${this.id}`, this.data);
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

    public setTable(table: TablePeriods) {
        this.table = table;
    }

    public openForUpdate(item: Period) {
        this.action = 'update';

        this.id = item.id;

        for (const name in item) {

            if (Object.prototype.hasOwnProperty.call(item, name)) {
                const value = item[name as keyof Period] as string;

                if (typeof value === 'boolean') {
                    this.radios.get(`input-${name}-${ (value === true) ? 'yes': 'no'}`).checked = true;
                }

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
        this.dialog = new MDCDialog(document.getElementById('dg-periods-form'));

        this.dialog.root.querySelectorAll('.input_text').forEach(element => {
            const input = element.querySelector<HTMLInputElement>('input');

            this.setData(input.name, input.value.trim());

            this.inputs.set(input.name, new MDCTextField(element));
        });

        this.dialog.root.querySelectorAll('select').forEach(select => {
            this.setData(select.name, select.value);

            this.selects.set(select.name, select);
        });

        document.querySelectorAll('.mdc-form-field').forEach(element => {
            (new MDCFormField(element)).input = (
                new MDCRadio(element.querySelector('.mdc-radio'))
            );

            const input = element.querySelector<HTMLInputElement>('input');

            this.radios.set(input.id, input);

            element.querySelector('input').addEventListener('change', this.onChangeRadio.bind(this));
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

    private transformErrors(valid: Validator.Validator<Partial<Period>>): string {
        const msg_errors: string[] = [];
        const errors = valid.errors.all();

        for (const name in errors) {
            if (Object.prototype.hasOwnProperty.call(errors, name)) {
                errors[name].forEach(msg => msg_errors.push(msg));
            }
        }

        return '- ' + msg_errors.join('<br />- ');
    }

    private onChangeRadio(event: MouseEvent) {
        const element: HTMLInputElement = event.target as HTMLInputElement;
        this.data.active = (element.value) == '1';
    }

    private defaultData(): Period {
        return {
            name: null,
            start_date: null,
            finish_date: null,
            amount_bonds: null,
            max_amount_loan: null,
            max_children: 4,
            active: true,
        };
    }
}

export default DialogForm;
