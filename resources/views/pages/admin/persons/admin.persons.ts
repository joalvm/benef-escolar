import { MDCDataTable } from '@material/data-table';
import PersonsPagination from './pagination';
import Searcher from './searcher';

const STORAGE_ITEM = 'admin.persons.table';

interface Unit {
    id: number;
    name: string;
}

interface Boat {
    id: number;
    name: string;
}

interface Person {
    id: number;
    names: string;
    dni: string;
    email: string;
    gender: string;
    birth_date: string;
    hiring_date: string;
    status: string;
    unit: Unit;
    boat: Boat;
    created_at: string;
}

class Persons {
    private datatable: MDCDataTable;
    private paginator: PersonsPagination;
    private searcher: Searcher;

    constructor() {
        this.init();
        this.loadTable();
    }

    private init() {
        this.paginator = new PersonsPagination(
            document.getElementById('dt-persons-pagination') as HTMLDivElement,
            STORAGE_ITEM
        );

        this.searcher = new Searcher(
            document.getElementById('dt-persons-search') as HTMLInputElement,
            document.getElementById('dt-persons-search-button') as HTMLButtonElement,
            STORAGE_ITEM
        );

        this.datatable = new MDCDataTable(document.getElementById('dt-persons'));

        this.paginator.onChangeSelectRow = this.loadTable.bind(this);
        this.searcher.loadTable = this.loadTable.bind(this);
    }

    private async loadTable()
    {
        const body = createElement('tbody', {className: ['mdc-data-table__content']});
        const params = this.paginator.getStorage() as ParamsGET;

        this.datatable.root.querySelector('tbody').remove();

        if (this.searcher.text) {
            params.contains = {
                fields: ['id', 'names', 'dni'],
                text: this.searcher.text
            }
        }
        console.log(this.searcher.text);

        const request = await Http.api().get<Person[]>('persons', params);

        delete params.contains;

        window.localStorage.setItem(
            'admin.persons.table',
            JSON.stringify(params)
        );

        request.data.forEach(item => {
            body.append(this.addRow(item));
        });

        this.datatable.root.querySelector('table').append(body);
        this.paginator.setData({
            from: (request as any).from,
            to: (request as any).to,
            current_page: (request as any).current_page,
            last_page: (request as any).last_page,
            per_page: (request as any).per_page,
            total: (request as any).total
        });

        this.datatable.layout();
    }

    private addRow(item: Person) {
        const row = createElement('tr', {className: ['mdc-data-table__row']});
        const idCell = createElement('td', {className: ['mdc-data-table__cell']});
        const nameCell = createElement('td', {className: ['mdc-data-table__cell']});
        const dniCell = createElement('td', {className: ['mdc-data-table__cell']});
        const plantCell = createElement('td', {className: ['mdc-data-table__cell']});
        const boatCell = createElement('td', {className: ['mdc-data-table__cell']});
        const moreCell = createElement('td', {className: ['mdc-data-table__cell', 'mdc-data-table__cell--checkbox']});
        const button = createElement('button', {className: ['mdc-icon-button', 'material-icons']});

        idCell.textContent = item.id.toString();
        nameCell.textContent = item.names;
        dniCell.textContent = item.dni || '-';
        plantCell.textContent = item.unit?.name || '-';
        boatCell.textContent = item.boat?.name || '-';
        button.textContent = 'more_vert';

        moreCell.append(button);

        row.append(...[
            idCell,
            nameCell,
            dniCell,
            plantCell,
            boatCell,
            moreCell
        ]);

        return row;
    }
}

new Persons();

export default Persons;
