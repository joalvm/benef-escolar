import { MDCTextField } from '@material/textfield';
import { MDCDrawer } from "@material/drawer";
import Periods from './periods';

class Admin {
    private btnMenu: HTMLButtonElement;
    private drawer: MDCDrawer;

    constructor() {
        this.init();
        this.initEvents();

        if (user.role !== 'user') {
            new Periods();
        }
    }

    private init(): void {
        this.btnMenu = document.getElementById('btn-menu') as HTMLButtonElement;

        if (this.btnMenu) {
            this.drawer = MDCDrawer.attachTo(document.querySelector('.mdc-drawer'));
        }

        window.MDCTextField = MDCTextField;
    }

    private initEvents() {
        if (this.btnMenu) {
            this.btnMenu.addEventListener('click', this.onClickMenu.bind(this));
        }
    }

    private onClickMenu(event: MouseEvent) {
        this.drawer.open = !this.drawer.open;
    }
}

new Admin();

export default Admin;
