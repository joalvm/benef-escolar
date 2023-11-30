import { MDCDataTable } from '@material/data-table';
import { MDCFormField } from '@material/form-field';
import { MDCCheckbox } from '@material/checkbox';
import PersonsPagination from '../persons/pagination';
import Searcher from '../persons/searcher';
import moment from 'moment';

const STORAGE_ITEM = 'admin.request.table';

export enum DocumentStatus {
    PENDING='pending',
    OBSERVED='observed',
    APPROVED='approved',
    CLOSED='closed',
}

export interface Unit {
    id: number;
    name: string;
}

export interface Boat {
    id: number;
    name: string;
}

export interface Person {
    id: number;
    names: string;
    dni: string;
    gender: string;
    unit: Unit;
    phone: string;
    boat: Boat;
}

export interface Period {
    id: number;
    name: string;
    max_amount_loan: number;
}

export interface Request {
    id: number;
    status: string;
    person: Person;
    period: Period;
    created_at: string;
}

export interface Counter {
    observeds: number,
    pendings: number;
    approveds: number;
}

interface TableFilter extends ParamsGET {
    units?: number[],
    boats?: number[],
    status?: DocumentStatus[]
}

class Requests {
    private datatable: MDCDataTable;
    private paginator: PersonsPagination;
    private searcher: Searcher;
    private selects: Map<string, HTMLSelectElement> = new Map();
    private checkboxs: Map<string, MDCCheckbox> = new Map();
    private counters: Map<string, HTMLSpanElement> = new Map();
    private period: StoragePeriod;

    constructor() {
        this.init();
        this.loadTable();
    }

    private init() {
        this.period = getStoragePeriod();

        this.paginator = new PersonsPagination(
            document.getElementById('dt-request-pagination') as HTMLDivElement,
            STORAGE_ITEM
        );
        this.searcher = new Searcher(
            document.getElementById('dt-request-search') as HTMLInputElement,
            document.getElementById('dt-request-search-button') as HTMLButtonElement,
            STORAGE_ITEM
        );

        this.datatable = new MDCDataTable(document.getElementById('dt-persons'));

        this.paginator.onChangeSelectRow = this.loadTable.bind(this);
        this.searcher.loadTable = this.loadTable.bind(this);

        document.querySelectorAll('.input-select').forEach((element: HTMLSelectElement) => {
            element.addEventListener('change', this.onChangeSelects.bind(this));
            this.selects.set(element.name, element);
        });

        const formField = new MDCFormField(document.getElementById('form-status'));

        formField.root.querySelectorAll('.mdc-checkbox').forEach((element: HTMLDivElement) => {
            const value = element.querySelector('input').value;
            const checkbox = new MDCCheckbox(element);
            formField.input = checkbox;

            this.checkboxs.set(value, checkbox);

            checkbox.listen('change', this.onChangeCheckbox.bind(this, value));
        });

        document.querySelectorAll('.counter').forEach((element: HTMLSpanElement) => {
            this.counters.set(element.dataset.status, element);
        })

        document.getElementById('btn-export-excel').addEventListener('click', this.onClickExportExcel.bind(this));
        document.getElementById('btn-export-zip').addEventListener('click', this.onClickExportExcel.bind(this));
    }

    private async onChangeSelects(event: Event) {
        const select = event.currentTarget as HTMLSelectElement;

        select.disabled = true;

        await this.loadTable();

        select.disabled = false;
    }

    private async onChangeCheckbox(name: string, event: Event) {
        const checkbox = this.checkboxs.get(name);

        checkbox.disabled = true;

        await this.loadTable();

        checkbox.disabled = false;
    }

    private onClickExportExcel(event: MouseEvent) {
        const link = event.currentTarget as HTMLLinkElement;
        const base = link.href.split('?')[0];
        let params = [];

        if (this.selects.get('boats').selectedIndex > 0) {
            params.push('boats=' + this.selects.get('boats').value);
        }

        if (this.selects.get('units').selectedIndex > 0) {
            params.push('units=' + this.selects.get('units').value);
        }

        if (params.length > 0) {
            link.href = base + '?' + params.join('&');
        } else {
            link.href = base;
        }
    }

    private async loadTable()
    {
        const body = createElement('tbody', {className: ['mdc-data-table__content']});
        const params = this.paginator.getStorage() as TableFilter;

        this.datatable.root.querySelector('tbody').remove();

        if (this.searcher.text) {
            params.contains = {
                fields: ['person.id', 'person.names', 'person.dni'],
                text: this.searcher.text
            }
        }

        if (this.selects.get('units').value !== 'all') {
            params.units = [parseInt(this.selects.get('units').value)];
        } else {
            delete params.units;
        }

        if (this.selects.get('boats').value !== 'all') {
            params.boats = [parseInt(this.selects.get('boats').value)];
        } else {
            delete params.boats;
        }

        params.status = [];

        if (this.checkboxs.get('pending').checked) {
            params.status = [...params.status, DocumentStatus.PENDING];
        } else {
            params.status = params.status.filter(el => el != DocumentStatus.PENDING);
        }

        if (this.checkboxs.get('observed').checked) {
            params.status = [...params.status, DocumentStatus.OBSERVED];
        } else {
            params.status = params.status.filter(el => el != DocumentStatus.OBSERVED);
        }

        if (this.checkboxs.get('approved').checked) {
            params.status = [...params.status, DocumentStatus.APPROVED];
        } else {
            params.status = params.status.filter(el => el != DocumentStatus.APPROVED);
        }

        if (params.status.length == 3 || params.status.length == 0) {
            delete params.status;
        }

        const request = await Http.api().get<Request[]>('persons/requests', params);
        const counter = await Http.api().get<Counter>('persons/requests/counter', params);

        delete params.contains;

        window.localStorage.setItem(
            STORAGE_ITEM,
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

        this.counters.get('observed').textContent = counter.data.observeds.toString();
        this.counters.get('approved').textContent = counter.data.approveds.toString();
        this.counters.get('pending').textContent = counter.data.pendings.toString();

        this.datatable.layout();
    }

    private addRow(item: Request) {
        const row = createElement('tr', {className: ['mdc-data-table__row']});
        const idCell = createElement('td', {className: ['mdc-data-table__cell']});
        const nameCell = createElement('td', {className: ['mdc-data-table__cell']});
        const dniCell = createElement('td', {className: ['mdc-data-table__cell']});
        const phoneCell = createElement('td', {className: ['mdc-data-table__cell']});
        const unityCell = createElement('td', {className: ['mdc-data-table__cell']});
        const boatCell = createElement('td', {className: ['mdc-data-table__cell']});
        const fcontratoCell = createElement('td', {className: ['mdc-data-table__cell']});
        const statusCell = createElement('td', {className: ['mdc-data-table__cell']});
        const statusText = createElement('span', {className: [`badge-status`, `${item.status}`]})
        const moreCell = createElement('td', {className: ['mdc-data-table__cell']});
        const button = createElement('a', {className: ['mdc-icon-button', 'material-icons']});
        const deleteBtn = createElement('button', {className: ['mdc-icon-button', 'material-icons']});


        idCell.textContent = item.person.id.toString();
        nameCell.textContent = item.person.names;
        dniCell.textContent = item.person.dni ?? '-';
        phoneCell.textContent = item.person.phone ?? '-';
        unityCell.textContent = item.person.unit.name ?? '-';
        boatCell.textContent = item.person.boat?.name ?? '-';
        fcontratoCell.textContent = moment(item.created_at).fromNow();
        button.textContent = 'visibility';
        button.href = `admin/requests/approval/${item.id}`;

        console.log(this.period);

        deleteBtn.textContent = 'delete';

        statusText.textContent = statusMessage(item.status, true);
        statusText.title = statusMessage(item.status);
        statusCell.append(statusText);
        moreCell.append(...[button, deleteBtn]);

        deleteBtn.addEventListener('click', (ev) => {
            new Alert({
                message: `¿Desea eliminar la solicitud de ${item.person.names}?`,
                btnText: 'Eliminar',
                callback: async () => {
                    const response = await Http.api().delete(`persons/requests/${item.id}`);

                    if (!response.error) {
                        Notifier.success('Solicitud eliminada');
                        this.loadTable();
                    }
                }
            })
        })

        row.append(...[
            idCell,
            nameCell,
            dniCell,
            phoneCell,
            unityCell,
            boatCell,
            fcontratoCell,
            statusCell,
            moreCell
        ]);

        return row;
    }
}

new Requests();
