import {MDCChipSet} from '@material/chips/chip-set';
import Information from './information';
import ChangePassword from './changePassword';

const chipSetEl = document.querySelector('.mdc-chip-set');
const chipSet = new MDCChipSet(chipSetEl);

class Persons {
    private userSession: UserSession;

    constructor() {
        this.userSession = JSON.parse(window.localStorage.getItem('user'));

        new Information(this.userSession.person_id);
        new ChangePassword(this.userSession.user_id);
    }
}

new Persons();
