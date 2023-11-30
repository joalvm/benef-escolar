interface Documents {
    id: string,
    file: string
}

interface PersonExtra {
    phone?: string;
    boats_id?: number;
}

interface Uploaded {
    path: string;
    file: File;
}

class Requests {
    private personId: number;
    private container: HTMLDivElement;
    private btnFormat: HTMLButtonElement;
    private list: HTMLUListElement;
    private inputFile: HTMLInputElement;
    private documents: Documents[] = [];
    private inputPhone: MDCTextField;
    private selectBoat: HTMLSelectElement;

    private currentReloadElement: HTMLButtonElement;
    private inputReload: HTMLInputElement;

    private btnNewFormats: HTMLButtonElement;
    private inputNewFormats: HTMLInputElement;

    constructor(personId: number) {
        this.personId = personId;

        this.init();
        this.initEvents();
    }

    private async onClickBtnSave()
    {
        let data: PersonExtra = {};

        if (this.documents.length == 0) {
            Notifier.error('Debe registar los formatos firmados');
            return;
        }

        if (this.inputPhone) {
            if (this.inputPhone.value.trim().length === 0) {
                Notifier.error('Debe especificar su télefono de contacto');
                this.inputPhone.valid = false;
                return;
            }

            data.phone = this.inputPhone.value.trim();
        }

        if (this.selectBoat) {
            if (this.selectBoat.selectedIndex < 1) {
                Notifier.error('Debe seleccionar una embarcación');
                this.selectBoat.setAttribute('valid', 'false');
                return;
            }

            data.boats_id = parseInt(this.selectBoat.options.item(this.selectBoat.selectedIndex).value);
        }

        this.btnFormat.disabled = true;

        const response = await Http.api().post('persons/requests', {
            persons_id: this.personId,
            person: data,
            documents: this.documents
        });


        if (response.error) {
            Notifier.error(response.message);
            this.btnFormat.disabled = false;
            return;
        }

        Notifier.success('La solicitud se creo satisfactoriamente.');

        window.setTimeout(() => {
            window.location.reload();
        }, 3500);
    }

    private init() {
        this.container = document.getElementById('frm-person_requests') as HTMLDivElement;
        const inputPhone = document.getElementById('input-phone');

        this.inputReload = document.getElementById('input-observed-files') as HTMLInputElement;

        if (this.container) {
            this.list = this.container.querySelector('ul#bonds-list');
            this.inputFile = this.container.querySelector('input[type=file]');
            this.btnFormat = this.container.querySelector('#btn-init_request');
        }

        this.inputPhone = inputPhone ? new MDCTextField(inputPhone) : undefined;
        this.selectBoat = document.getElementById('cbo-boats') as HTMLSelectElement;

        this.btnNewFormats = document.getElementById('btn-new_formats') as HTMLButtonElement;
        this.inputNewFormats = document.getElementById('input-new-formats') as HTMLInputElement;
    }

    private initEvents() {
        if (this.inputReload) {
            this.inputReload.addEventListener('change', this.onChangeReloadFile.bind(this));

            document.querySelectorAll('.btn-reload').forEach((element: HTMLButtonElement) => {
                element.addEventListener('click', this.onClickReloadFile.bind(this));
            });
        }
        console.log(this.btnNewFormats);
        if (this.btnNewFormats) {
            this.inputNewFormats.addEventListener('change', this.onChangeNewFormats.bind(this));
            this.btnNewFormats.addEventListener('click', () => {
                this.inputNewFormats.click();
            })
        }

        if (this.container) {
            this.container
                .querySelectorAll('.upload')
                .forEach(element => {
                    element.addEventListener('click', this.onClickUploadButton.bind(this))
                });

            this.inputFile.addEventListener('change', this.onChangeInputFile.bind(this));
            this.btnFormat.addEventListener('click', this.onClickBtnSave.bind(this));
        }
    }

    private onClickUploadButton(event: MouseEvent) {
        this.inputFile.click();
    }

    private onChangeInputFile(event: Event) {
        const element: HTMLInputElement = event.target as HTMLInputElement;

        for (let i = 0; i < element.files.length; i++) {
            this.createItemList(
                element.files[i],
                this.list,
                'bonds'
            );
        }

        element.value = null;
    }

    private async onClickReloadFile(event: MouseEvent) {
        const element = event.currentTarget as HTMLButtonElement;

        this.currentReloadElement = element;

        this.inputReload.click();
    }

    private async onChangeNewFormats(event: Event) {
        const element: HTMLInputElement = event.currentTarget as HTMLInputElement;
        const data = [];

        this.btnNewFormats.disabled = true;

        for (let i = 0; i < element.files.length; i++) {
            const result = await this.uploadFile(element.files[i]);

            if (!result) {
                element.value = null;
                return;
            }

            data.push({
                persons_requests_id: parseInt(element.dataset.id),
                file: result.path
            });
        }

        const res = await Http.api().post(`persons/requests/documents`, data);

        if (!res.error) {
            Notifier.success('Espere un momento...', 'Nuevo documento agregado');
            window.setTimeout(() => {
                window.location.reload();
            }, 2500);
        }

        element.value = null;
    }

    private async onChangeReloadFile(event: Event) {
        const id = this.currentReloadElement.dataset.id;
        const element: HTMLInputElement = event.currentTarget as HTMLInputElement;

        const result = await this.uploadFile(element.files[0]);

        if (!result) {
            element.value = null;
            return;
        }

        this.currentReloadElement.disabled = true;

        const res = await Http.api().put(`persons/requests/documents/${id}`, {
            file: result.path,
            status: 'pending',
            observation: null
        });

        if (!res.error) {
            Notifier.success('El documento se ha modificado satisfactoriamente.');
            window.setTimeout(() => {
                window.location.reload();
            }, 2500);
        }

        this.currentReloadElement.disabled = false;
        element.value = null;
    }

    private async createItemList(
        file: File,
        element: HTMLUListElement,
        type: string
    ) {
        const allowedTypes = ['image/png', 'image/jpeg','image/jpg', 'image/bmp', 'image/webp'];
        const id = this.uid();
        let list = createElement('li', {
            id: `list-${id}`,
            className: ['mdc-list-item']
        });

        if (!allowedTypes.includes(file.type)) {
            Notifier.error('Solo imagenes pueden ser incluidas');
            return;
        }

        new Compressor(file, {
            quality: 0.6,
            maxWidth: 1024,
            success: async (result: File) => {
                const formData = new FormData();

                formData.append('file', result, result.name);

                list.append(await this.thumbnail(result));
                list.append(this.description(result.name, result.size));
                list.append(this.itemAction(list, id));

                const response = await Http.api().post<FormData, { path: string; }>('files', formData);

                this.documents.push({
                    id: id,
                    file: response.data.path
                });

                if (this.documents.length > 0) {
                    this.btnFormat.disabled = false;
                }
            }
        });

        element.append(list);
    }

    private async uploadFile(file: File): Promise<Uploaded> {
        const allowedTypes = ['image/png', 'image/jpeg','image/jpg', 'image/bmp', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            Notifier.error('Solo imagenes pueden ser incluidas.');
            return null;
        }

        return new Promise((resolve, reject) => {
            new Compressor(file, {
                quality: 0.6,
                maxWidth: 1024,
                success: async (result: File) => {
                    const formData = new FormData();

                    formData.append('file', result, result.name);

                    const response = await Http.api().post<FormData, { path: string; }>('files', formData);

                    resolve({
                        path: response.data.path,
                        file: result
                    });
                }
            });
        });
    }

    private async thumbnail(file: File): Promise<HTMLSpanElement> {
        return createElement('span', {
            className: ['mdc-list-item__graphic'],
            styles: {
                backgroundImage: `url('${await this.getImage(file)}')`
            }
        });
    }

    private description(title: string, size: number): HTMLSpanElement {
        let container = createElement('span', {className: ['mdc-list-item__text']});
        let header = createElement('span', {className: ['mdc-list-item__primary-text']});
        let desc = createElement('span', {className: ['mdc-list-item__secondary-text']});

        header.textContent = title;
        desc.textContent = size.toString();

        container.append(header);
        container.append(desc);

        return container;
    }

    private itemAction(list: HTMLLIElement, id: string): HTMLSpanElement {
        let container = createElement('span', {className: ['mdc-list-item__meta']});
        let button = createElement('button', {
            className: ['mdc-icon-button', 'material-icons']
        });

        container.setAttribute('aria-hidden', 'true');
        button.textContent = 'delete';

        button.addEventListener('click', (_) => {
            this.documents = this.documents.filter(doc => doc.id !== id);
            list.remove();

            if (this.documents.length == 0) {
                this.btnFormat.disabled = true;
            }
        });

        container.append(button);

        return container;
    }

    private async getImage(file: File) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    private uid(): string {
        return '_' + Math.random().toString(36).substr(2, 9);
    };
}

export default Requests;
