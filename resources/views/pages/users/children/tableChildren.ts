import {MDCDataTable} from '@material/data-table';
import DialogForm from './dialogForm';

interface Children {
    id: number;
    name: string;
    paternal_surname: string;
    maternal_surname: string;
    gender: string;
    birth_date: string;
    created_at: string;
}

class TableChildren {
    private table: MDCDataTable;
    private dialog: DialogForm;

    constructor(personId: number) {
        this.init();
        this.loadData();
    }

    private init() {
        this.table = new MDCDataTable(document.getElementById('dt-children'));
    }

    public async loadData() {
        this.setEmptyRow('loading...');

        const response = await Http.api().get<Children[]>('children', {
            paginate: false,
            fields: ['id', 'name', 'paternal_surname', 'maternal_surname', 'gender', 'birth_date', 'created_at']
        });

        if (!response.data.length) {
            this.setEmptyRow('No se registran hijos');
            return;
        }

        this.addData(response.data);
    }

    public setDialog(dialog: DialogForm) {
        this.dialog = dialog;
    }

    private addData(data: Children[]) {
        const tbody = createElement('tbody', {className: ['mdc-data-table__content']});

        this.table.root.querySelector('tbody').remove();

        data.forEach(item => {
            tbody.append(this.row(item));
        });

        this.table.root.append(tbody);
    }

    private row(item: Children): HTMLTableRowElement {
        const tr = createElement('tr', {className: ['mdc-data-table__row']});
        const namesCol = createElement('td', {className: ['mdc-data-table__cell']});
        const genderCol = createElement('td', {className: ['mdc-data-table__cell']});
        const birthDateCol = createElement('td', {className: ['mdc-data-table__cell']});
        const ActionsCol = createElement('td', {
            className: ['mdc-data-table__cell']
        });

        namesCol.textContent =  `${item.name} ${item.paternal_surname} ${item.maternal_surname}`;
        genderCol.textContent = item.gender.toUpperCase();
        birthDateCol.textContent = item.birth_date;

        ActionsCol.append(...this.getActionbuttons(item));

        tr.append(...[namesCol, genderCol, birthDateCol, ActionsCol]);

        return tr;
    }

    private getActionbuttons(item: Children): HTMLButtonElement[] {
        const edit = this.actionButton('editar', 'edit');
        const remove = this.actionButton('borrar', 'delete');

        edit.addEventListener('click', this.openModalEditAction.bind(this, item));

        return [edit, remove];
    }

    private openModalEditAction(item: Children) {
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

export default TableChildren;
