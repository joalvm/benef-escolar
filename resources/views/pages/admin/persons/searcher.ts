interface Search {
    offset: number;
    search: number;
}

class Searcher {
    private storageItem: string;
    public oldText: string;
    public text: string;
    public loadTable: Function;
    private input: HTMLInputElement;
    private button: HTMLButtonElement;
    private timeOutId: number;

    constructor(input: HTMLInputElement, button: HTMLButtonElement, storageItem: string) {
        this.input = input;
        this.button = button;
        this.storageItem = storageItem;

        this.init();
        this.initEvents();
    }

    private init() {}

    private initEvents() {
        this.input.addEventListener('keypress', this.onKeypressSearchTable.bind(this));
        this.button.addEventListener('click', this.onClickInitSearch.bind(this));
    }

    private async onClickInitSearch(event: MouseEvent) {
        if (this.input.value.trim().length === 0) {
            event.preventDefault();
            return false;
        }

        this.button.disabled = true;

        this.oldText = this.text;
        this.text = this.input.value.trim();
        this.setPaginate({offset: 1});
        await this.loadTable();

        this.button.disabled = false;
    }

    private onKeypressSearchTable(event: KeyboardEvent) {
        const element = event.target as HTMLInputElement;
        const code = !event.charCode ? event.which : event.charCode;

        if (code === 13) {
            if (this.oldText === element.value.trim()) {
                event.preventDefault();
                return false;
            }

            if (this.timeOutId) {
                window.clearTimeout(this.timeOutId);
            }

            this.timeOutId = window.setTimeout(() => {
                this.oldText = this.text;
                this.text = element.value.trim();
                this.setPaginate({offset: 1});
                this.loadTable();
            }, 500);
        }
    }

    private setPaginate(item: Partial<Search>) {
        this.setStorage(Object.assign(this.getStorage(), item));
    }

    public getStorage(): Search {
        return JSON.parse(window.localStorage.getItem(this.storageItem));
    }

    private setStorage(item: Search) {
        window.localStorage.setItem(this.storageItem, JSON.stringify(item));
    }
}

export default Searcher;
