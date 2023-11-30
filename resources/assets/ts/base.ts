import "./components/Notify";
import "./components/Http";
import "./components/Loader";
import "./helpers/createElement";
import './helpers/statusMessage';
import './helpers/getStoragePeriod';
import './components/Confirm';

class Base {
    constructor() {
        this.autoInit();
    }

    private autoInit(): void {
        Validator.useLang('es');
    }
}

new Base();

export default Base;
