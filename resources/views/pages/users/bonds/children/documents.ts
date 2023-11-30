export enum DocumentTypes {
    DNI='dni',
    STUDIES='studies'
}

export enum DocumentStatus {
    PENDING='pending',
    NEW='new',
    OBSERVED='observed',
    CLOSED='closed',
}

export enum FileType {
    IMAGE='image',
    PDF='pdf'
}

export interface DocumentFile {
    id?: string,
    children_requests_id?: number;
    type: DocumentTypes;
    file: string;
    status?: DocumentStatus;
    observation?: string;
}

class Documents {
    private documents: Map<string, DocumentFile> = new Map();
    private deleteDocuments: Map<string, DocumentFile> = new Map();
    private updateDocuments: Map<string, DocumentFile> = new Map();

    constructor() {
        this.init();
    }

    public find(id: string): DocumentFile {
        return this.documents.get(id);
    }

    public getByType(type: DocumentTypes): DocumentFile[] {
        return this.getAll().filter(doc => doc.type === type);
    }

    public getByStatus(status: DocumentStatus): DocumentFile[] {
        return this.getAll().filter(doc => doc.status === status);
    }

    public getAll() {
        return Array.from(this.documents.values());
    }

    public getDeletables() {
        return Array.from(this.deleteDocuments.values());
    }

    public getUpdatables() {
        return Array.from(this.updateDocuments.values());
    }

    public set(docs: DocumentFile[]) {
        docs.forEach((doc) => {
            this.documents.set(doc.id.toString(), doc);
        });
    }

    public setDeletable(id: string) {
        if (this.documents.has(id)) {
            this.deleteDocuments.set(id, this.find(id));
            this.documents.delete(id);
        }
    }

    public setUpdatable(id: string, status: DocumentStatus, path: string) {
        if (this.updateDocuments.has(id)) {
            this.updateDocuments.get(id).status = status;
            this.updateDocuments.get(id).file = path;
        } else if (this.documents.has(id)) {
            this.updateDocuments.set(id, {
                ...this.find(id),
                file: path,
                status: status,
                observation: null
            });
        }
    }

    private init() {
        document.querySelectorAll('.btn-upload').forEach(element => {
            element
                .addEventListener('click', this.onClickUploadButton.bind(this));
            element
                .nextElementSibling
                .addEventListener('change', this.onChangeInputFile.bind(this));
        });
    }

    private onClickUploadButton(event: MouseEvent) {
        const input: HTMLInputElement = (
            event.currentTarget as HTMLButtonElement
        ).nextElementSibling as HTMLInputElement;

        input.click();
    }

    private onChangeInputFile(event: Event) {
        const element: HTMLInputElement = event.target as HTMLInputElement;
        const uList = document.getElementById(element.dataset['list']) as HTMLUListElement;

        for (let i = 0; i < element.files.length; i++) {
            this.createItemList(
                element.files[i],
                uList,
                element.dataset['type'] as DocumentTypes
            );
        }

        element.value = null;
    }

    private async createItemList(
        file: File,
        element: HTMLUListElement,
        type: DocumentTypes
    ) {
        const allowedTypes = ['image/png', 'image/jpeg','image/jpg', 'image/bmp', 'image/webp', 'application/pdf'];
        const id = this.uid();
        let list = createElement('li', {
            id: `list-${id}`,
            className: ['mdc-list-item']
        });

        if (!allowedTypes.includes(file.type)) {
            Notifier.error('Solo imagenes o archivos pdf.');
            return;
        }

        if (file.name.match(/.*\.(gif|jpe?g|bmp|png)$/igm)) {
            new Compressor(file, {
                quality: 0.6,
                maxWidth: 1024,
                success: async (result: File) => {
                    await this.setDocument(result, FileType.IMAGE, list, id, type);
                    element.append(list);
                }
            });
        } else if (file.name.match(/.*\.pdf/igm)) {
            await this.setDocument(file, FileType.PDF, list, id, type);
            element.append(list);
        }
    }

    private async setDocument(
        result: File,
        iconType: FileType,
        list: HTMLLIElement,
        id: string,
        type: DocumentTypes
    ) {
        list.append(await this.thumbnail(result, iconType));
        list.append(this.description(result.name, result.size));
        list.append(this.itemAction(list, id));

        const path = await this.loadFile(result);

        if (!path) {
            return;
        }

        this.documents.set(id, {
            type,
            file: path,
            status: DocumentStatus.NEW
        });
    }

    public async loadFile(file: File) {
        const formData = new FormData();

        formData.append('file', file, file.name);

        const response = (
            await Http.api().post<FormData, { path: string; }>('files', formData)
        );

        if (response.error) {
            return null;
        }

        return response.data.path;
    }

    private async thumbnail(file: File, iconType: FileType): Promise<HTMLSpanElement> {
        const element = createElement('span', {className: ['mdc-list-item__graphic']});

        if (iconType == FileType.IMAGE) {
            element.style.backgroundImage = `url('${await this.getImage(file)}')`;
        }

        if (iconType == FileType.PDF) {
            element.setAttribute('aria-hidden', 'true');
            element.classList.add('material-icons');
            element.textContent = 'picture_as_pdf';
        }

        return element;
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
            this.documents.delete(id);
            list.remove();
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

export default Documents;
