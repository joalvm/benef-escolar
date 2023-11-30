import {MDCDataTable} from '@material/data-table';
import DialogForm from './dialogForm';

interface Period {
    id: number;
    name: string;
    start_date: string;
    finish_date: string;
    amount_bonds: number;
    max_amount_loan: number;
    active: boolean;
    created_at: string;
}

class TablePeriods {
    private table: MDCDataTable;
    private dialog: DialogForm;

    constructor() {
        this.init();
        this.loadData();
    }

    private init() {
        this.table = new MDCDataTable(document.getElementById('dt-periods'));
    }

    public async loadData() {
        this.setEmptyRow('loading...');

        const response = await Http.api().get<Period[]>('periods', {
            paginate: false,
            sort: {
                id: 'desc'
            },
            fields: ['id', 'name', 'start_date', 'finish_date', 'amount_bonds', 'max_amount_loan', 'active', 'created_at']
        });

        if (!response.data.length) {
            this.setEmptyRow('No se registran niveles de educación');
            return;
        }

        this.addData(response.data);
    }

    public setDialog(dialog: DialogForm) {
        this.dialog = dialog;
    }

    private addData(data: Period[]) {
        const tbody = createElement('tbody', {className: ['mdc-data-table__content']});

        this.table.root.querySelector('tbody').remove();

        data.forEach(item => {
            tbody.append(this.row(item));
        });

        this.table.root.append(tbody);
    }

    private row(item: Period): HTMLTableRowElement {
        const tr = createElement('tr', {className: ['mdc-data-table__row']});
        const nameCol = createElement('td', {className: ['mdc-data-table__cell']});
        const startDateCol = createElement('td', {className: ['mdc-data-table__cell']});
        const finishDateCol = createElement('td', {className: ['mdc-data-table__cell']});
        const amountBondstCol = createElement('td', {className: ['mdc-data-table__cell']});
        const maxAmountLoanCol = createElement('td', {className: ['mdc-data-table__cell']});
        const activeCol = createElement('td', {className: ['mdc-data-table__cell']});
        const ActionsCol = createElement('td', {
            className: ['mdc-data-table__cell']
        });

        nameCol.textContent =  `${item.name}`;
        startDateCol.textContent = item.start_date;
        finishDateCol.textContent = item.finish_date;
        amountBondstCol.textContent = item.amount_bonds.toString();
        maxAmountLoanCol.textContent = item.max_amount_loan.toString();
        activeCol.textContent = item.active ? 'Activo' : 'No Activo';

        ActionsCol.append(...this.getActionbuttons(item));

        tr.append(...[nameCol, startDateCol, finishDateCol, amountBondstCol, maxAmountLoanCol, activeCol, ActionsCol]);

        return tr;
    }

    private getActionbuttons(item: Period): HTMLButtonElement[] {
        const edit = this.actionButton('editar', 'edit');
        const remove = this.actionButton('borrar', 'delete');

        edit.addEventListener('click', this.openModalEditAction.bind(this, item));

        return [edit, remove];
    }

    private openModalEditAction(item: Period) {
        this.dialog.openForUpdate(item as any);
    }

    private setEmptyRow(text?: string): void {
        this.table.root.querySelector('tbody').remove();

        const tbody = createElement('tbody', {className: ['mdc-data-table__content']});
        const tr = createElement('tr', {className: ['mdc-data-table__row']});
        const td = createElement('td', {className: ['mdc-data-table__cell']});

        td.colSpan = 4;
        td.textContent = text;
        td.style.textAlign = 'center';

        tr.append(td);

        tbody.append(tr);

        this.table.root.append(tbody);
        this.table.layout();
    }

    private actionButton(text: string, icon: string): HTMLButtonElement {
        const button = createElement('button', {
            className: ['mdc-button']
        });
        const label = createElement('span', {className: ['mdc-button__label']});
        const preIcon = createElement('i', {
            className: ['material-icons', 'mdc-button__icon'],
            aria: {hidden: true}
        });

        preIcon.textContent = icon;

        label.textContent = text;

        button.append(...[
            createElement('span', {className: ['mdc-button__ripple']}),
            preIcon,
            label,
        ]);

        return button;
    }
}

export default TablePeriods;