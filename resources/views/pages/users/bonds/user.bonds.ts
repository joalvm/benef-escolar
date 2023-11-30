import { MDCDataTable } from '@material/data-table';
import { MDCDialog } from '@material/dialog';
import Requests from './requests';

export interface Parent {
    id: number;
    names: string;
    gender: string;
    dni: string;
}

export interface Plant {
    id: number;
    name: string;
}

export interface EducationLevel {
    id: number;
    name: string;
    amount: string;
}

export interface Request {
    id: number;
    get_loan: boolean;
    get_pack: boolean;
    status: string;
    plant: Plant;
    education_level: EducationLevel;
}

export interface Children {
    id: number;
    name: string;
    paternal_surname: string;
    maternal_surname: string;
    gender: string;
    birth_date: string;
    fullname: string;
    parent: Parent;
    request: Request;
    created_at: string;
}

class Bonds {
    private session: UserSession;

    private data: Children;
    private dialog: MDCDialog;
    private table: MDCDataTable;
    private requests: Requests;

    constructor() {
        this.session = JSON.parse(window.localStorage.getItem('user'));
        this.data = JSON.parse(window.localStorage.getItem('children'));

        window.localStorage.removeItem('children');

        this.init();
        this.initEvents();
    }

    private init() {
        this.table = new MDCDataTable(document.getElementById('td-children'));

        this.requests = new Requests(this.session.person_id);
    }

    private initEvents() {
    }
}

new Bonds();

export default Bonds;
