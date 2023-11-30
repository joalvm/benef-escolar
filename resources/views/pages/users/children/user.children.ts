import DialogForm from './dialogForm';
import TableChildren from './tableChildren';

class Users {
    private user: UserSession;
    private btnAddChild: HTMLButtonElement;
    private dialogForm: DialogForm;
    private table: TableChildren;

    constructor() {
        this.user = JSON.parse(window.localStorage.getItem('user'));

        this.init();
        this.initEvents();
    }

    private onClickOpenModalActionCreate() {
        this.dialogForm.dialog.open();
    }

    private init() {
        this.btnAddChild = document.getElementById('btn-add_child') as HTMLButtonElement;

        this.dialogForm = new DialogForm(this.user.person_id);
        this.table = new TableChildren(this.user.person_id);

        this.table.setDialog(this.dialogForm);
        this.dialogForm.setTable(this.table);
    }

    private initEvents() {
        this.btnAddChild.addEventListener('click', this.onClickOpenModalActionCreate.bind(this));
    }
}

new Users();

export default Users;
