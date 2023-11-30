interface PaginateRequest {
    offset: number;
    limit: number;
}

interface PaginateResponse {
    from: number;
    to: number;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

class PersonsPagination {
    private storageItem: string;
    private content: HTMLDivElement;
    public onChangeSelectRow: Function;
    private buttons: Map<string, HTMLButtonElement> = new Map();
    private data: PaginateResponse = {
        from: 0,
        to: 0,
        current_page: 0,
        last_page: 0,
        per_page: 0,
        total: 0
    };

    constructor(content: HTMLDivElement, storageItem: string) {
        this.content = content;
        this.storageItem = storageItem;

        this.init();
    }

    init () {
        this.content.querySelector('select')
            .addEventListener('change', this.changeRowLength.bind(this));

        this.content.querySelector('select').value = (this.getStorage().limit ?? 10).toString();

        this.content.querySelectorAll('.mdc-data-table__pagination-button')
            .forEach((element: HTMLButtonElement) => {
                element.addEventListener(
                    'click',
                    this.onClickChangePage.bind(this, element.dataset['action'])
                );

                this.buttons.set(element.dataset['action'], element);
            })
    }

    public setData(data: PaginateResponse) {
        this.data = data;

        this.handleButtons();
        this.setTextInfo();
    }

    private setTextInfo() {
        this.content.querySelector('.mdc-data-table__pagination-total').textContent = (
            this.data.from + '-' + this.data.to + ' de ' + this.data.total
        );
    }

    private onClickChangePage(action: string, event: MouseEvent) {
        let offset = this.getStorage().offset;

        this.buttons.forEach(element => element.disabled = true);

        switch(action) {
            case 'first': offset = 1; break;
            case 'preview': offset -= 1; break;
            case 'next': offset +=1; break;
            case 'last': offset = this.data.last_page; break;
        }

        if (this.onChangeSelectRow) {
            this.setPaginate({ offset });
            this.onChangeSelectRow();
        }
    }

    private changeRowLength(event: MouseEvent) {
        const element = event.currentTarget as HTMLSelectElement;
        if (this.onChangeSelectRow) {
            this.setPaginate({
                limit: parseInt(element.value) || 10,
                offset: 1
            });
            this.onChangeSelectRow();
        }
    }

    private handleButtons() {
        let paginate = this.getStorage();

        paginate.limit = paginate.limit ?? 10;
        paginate.offset = paginate.offset ?? 1;

        if (this.data.total == 0 || this.data.total <= paginate.limit) {
            this.buttons.get('preview').disabled = true;
            this.buttons.get('first').disabled = true;
            this.buttons.get('last').disabled = true;
            this.buttons.get('next').disabled = true;
        } else if (paginate.offset <= 1) {
            this.buttons.get('preview').disabled = true;
            this.buttons.get('first').disabled = true;
            this.buttons.get('last').disabled = false;
            this.buttons.get('next').disabled = false;
        } else if (paginate.offset > 1 && paginate.offset < this.data.last_page) {
            this.buttons.get('preview').disabled = false;
            this.buttons.get('first').disabled = false;
            this.buttons.get('last').disabled = false;
            this.buttons.get('next').disabled = false;
        } else if (paginate.offset == this.data.last_page) {
            this.buttons.get('preview').disabled = false;
            this.buttons.get('first').disabled = false;
            this.buttons.get('last').disabled = true;
            this.buttons.get('next').disabled = true;
        }

        this.setStorage(paginate);
    }

    private setPaginate(item: Partial<PaginateRequest>) {
        this.setStorage(Object.assign(this.getStorage(), item));
    }

    public getStorage(): PaginateRequest {
        return JSON.parse(window.localStorage.getItem(this.storageItem)) ?? {};
    }

    private setStorage(item: PaginateRequest) {
        window.localStorage.setItem(this.storageItem, JSON.stringify(item));
    }
}

export default PersonsPagination;
