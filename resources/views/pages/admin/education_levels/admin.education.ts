import DialogForm from './dialogForm';
import TableEducationLevels from './tableEducationLevels';

class EducationLevels {
    private btnAddChild: HTMLButtonElement;
    private dialogForm: DialogForm;
    private table: TableEducationLevels;

    constructor() {
        this.init();
        this.initEvents();
    }

    private onClickOpenModalActionCreate() {
        this.dialogForm.dialog.open();
    }

    private init() {
        this.btnAddChild = document.getElementById('btn-add_child') as HTMLButtonElement;

        this.dialogForm = new DialogForm();
        this.table = new TableEducationLevels();

        this.table.setDialog(this.dialogForm);
        this.dialogForm.setTable(this.table);
    }

    private initEvents() {
        this.btnAddChild.addEventListener('click', this.onClickOpenModalActionCreate.bind(this));
    }
}

new EducationLevels();

export default EducationLevels;
