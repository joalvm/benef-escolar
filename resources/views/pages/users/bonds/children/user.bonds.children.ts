import { MDCFormField } from '@material/form-field';
import { MDCRadio } from '@material/radio';
import Documents from './documents';
import HandleDocuments, {DocumentFile, DocumentTypes, DocumentStatus} from './documents';

const STORAGE_ITEM = 'user.bonds.children';

interface Child {
    id?: number;
    name: string;
    paternal_surname: string;
    maternal_surname: string;
    gender: string;
    birth_date: string;
}

interface DataChildren extends Child {
    request: Request;
}

interface Request {
    id?: number;
    children_id?: number;
    education_levels_id: number;
    get_loan: boolean;
    get_pack: boolean;
    plants_id: number;
    delivery_type: string;
    responsable_name: string;
    responsable_dni: string;
    responsable_phone: string;
    address: string;
    address_reference: string;
    districts_id: null;
    documents?: DocumentFile[];
}

const otherRules = {
    delivery_type: ['string'],
    plants_id: ['present', 'integer', {required_if: ['request.delivery_type', 'pick_in_plant']}],
    responsable_name: ['present', 'string', {required_if: ['request.delivery_type', 'delivery']}],
    responsable_dni: ['present', 'string', 'size:8', {required_if: ['request.delivery_type', 'delivery']}],
    responsable_phone: ['present', 'string', {required_if: ['request.delivery_type', 'delivery']}],
    districts_id: ['present', 'integer', {required_if: ['request.delivery_type', 'delivery']}],
    address: ['present', 'string', {required_if: ['request.delivery_type', 'delivery']}],
    address_reference: ['present', 'string', {required_if: ['request.delivery_type', 'delivery']}]
}

const rules = {
    name: ['required', 'string'],
    paternal_surname: ['required', 'string'],
    maternal_surname: ['required', 'string'],
    gender: ['required', 'string'],
    birth_date: ['required', 'date'],
    request: {
        education_levels_id: ['required', 'integer'],
        get_loan: ['required', 'boolean'],
        get_pack: ['required', 'boolean'],
    }
};

const labels = {
    'name': 'Nombres',
    'paternal_surname': 'Apellido Paterno',
    'maternal_surname': 'Apellido Materno',
    'gender': 'Sexo',
    'birth_date': 'F. De Nacimiento',
    'request.education_levels_id': 'Nivel Educativo',
    'request.get_loan': 'Solicitar Prestamo',
    'request.get_pack': 'Recibir Pack educativo',
    'request.delivery_type': 'Tipo de recojo',
    'request.plants_id': 'Lugar de recojo',
    'request.responsable_name': 'Nombres del responsable',
    'request.responsable_dni': 'DNI del responsable',
    'request.responsable_phone': 'Celular del responsable',
    'request.districts_id': 'Distrito',
    'request.address': 'Dirección',
    'request.address_reference': 'Referencia'
}

class Children {
    private action = 'create';
    private btnSave: HTMLButtonElement;
    private handleDocuments: HandleDocuments;
    private data: DataChildren = this.defaultData();
    private inputs: Map<string, MDCTextField> = new Map();
    private selects: Map<string, HTMLSelectElement> = new Map();
    private handleChangeProvince: EventListener;

    private inputReloadFile: HTMLInputElement;
    private currentButtonReload: HTMLButtonElement;

    constructor() {
        this.init();
        this.initEvents();
        this.loadPlants();
        this.loadDepartments();
        this.loadEducationLevels();
    }

    private async onClickSaveData(event: MouseEvent) {
        const pack = document.getElementById('input-pack-yes') as HTMLInputElement;
        let nrule = Object.assign({}, rules);

        if (pack.checked) {
            nrule.request = {...nrule.request, ...otherRules};
        }

        const valid = new Validator(this.data, nrule);

        valid.setAttributeNames(labels);

        if (!valid.check()) {
            Notifier.error(this.transformErrors(valid), 'Verificar información:');
            return;
        }

        if (!this.handleDocuments.getByType(DocumentTypes.DNI).length) {
            Notifier.error('Debe agregar la copia del DNI.');
            return;
        }

        if (!this.handleDocuments.getByType(DocumentTypes.STUDIES).length) {
            Notifier.error('Debe agregar las imagenes de los documentos educativos.');
            return;
        }

        if (this.action == 'create') {
            this.onCreate();
        } else {
            this.onUpdate();
        }
    }

    private async onCreate() {
        this.data.request.documents = this.handleDocuments.getAll();
        this.btnSave.disabled = true;

        const response = await Http.api().post<DataChildren, any>('children', this.data);

        if (response.error) {
            Notifier.error(response.message);
            this.btnSave.disabled = false;
            return false;
        }

        Notifier.success('LOS DATOS HAN SIDO REGISTRADOS SATISFACTORIAMENTE');
        window.setTimeout(() => {
            window.location.href = (document.getElementById('btn-cancel') as HTMLLinkElement).href;
        }, 3000);
    }

    private async onUpdate() {
        this.btnSave.disabled = true;

        document.querySelectorAll('button.btn-upload')
            .forEach((element: HTMLButtonElement) => {element.disabled = true});

        const resp1 = await this.updateChild(this.data);
        const resp2 = await this.updateRequest(this.data.request, this.data.id);
        const resp3 = await this.updateDocuments(this.data.request.id);

        Notifier.success('LOS DATOS HAN SIDO REGISTRADOS SATISFACTORIAMENTE');
        window.setTimeout(() => {
            const hrf = (document.getElementById('btn-cancel') as HTMLLinkElement).href;
            window.location.href = hrf;
        }, 3000);
    }

    private async updateChild(data: DataChildren) {
        const child: Child = {
            name: data.name,
            paternal_surname: data.paternal_surname,
            maternal_surname: data.maternal_surname,
            gender: data.gender,
            birth_date: data.birth_date
        };

        return await Http.api().put<Child, any>(`children/${data.id}`, child);
    }

    private async updateRequest(data: Request, childId: number) {
        const req: Omit<Request, 'documents'> = {
            children_id: childId,
            education_levels_id: data.education_levels_id,
            plants_id: data.plants_id,
            get_loan: data.get_loan,
            get_pack: data.get_pack,
            delivery_type: data.delivery_type,
            address: data.address,
            address_reference: data.address_reference,
            districts_id: data.districts_id,
            responsable_name: data.responsable_name,
            responsable_dni: data.responsable_dni,
            responsable_phone: data.responsable_phone
        };

        if (data.id) {
            return await Http.api().put<Omit<Request, 'documents'>, any>(`children/requests/${data.id}`, req);
        } else {
            const response = await Http.api().post<Omit<Request, 'documents'>, any>(`children/requests`, req);

            if (!response.error) {
                this.data.request.id = response.data.id;
            }

            return response;
        }


    }

    private async updateDocuments(requestId: number)
    {
        const news = this.handleDocuments.getByStatus(DocumentStatus.NEW)
            .map((doc) => {
                return {...doc, children_requests_id: requestId, status: DocumentStatus.PENDING}
            });

        if (news.length > 0) {
            await Http.api().post<DocumentFile[], any>('children/requests/documents', news);
        }

        this.handleDocuments.getDeletables().forEach(async (doc) => {
            const url = `children/requests/documents/${doc.id}`;

            if (doc.status === DocumentStatus.PENDING) {
                await Http.api().delete(url);
            }
        });

        this.handleDocuments.getUpdatables().forEach(async (doc) => {
            const url = `children/requests/documents/${doc.id}`;
            await Http.api().put(url, doc);
        });
    }

    private init(): void {
        this.handleDocuments = new HandleDocuments();

        document.querySelectorAll('.mdc-text-field').forEach((element) => {
            const mdcElement = new MDCTextField(element);
            const name = element.querySelector('input').name;

            this.setData(name, mdcElement.value);

            mdcElement.listen('change', this.onChangeInput.bind(this, name));
            this.inputs.set(name, mdcElement);
        });

        document.querySelectorAll('.mdc-form-field').forEach(element => {
            const input: HTMLInputElement = element.querySelector('input');
            (new MDCFormField(element)).input = (
                new MDCRadio(element.querySelector('.mdc-radio'))
            );

            input.addEventListener('change', this.onChangeRadio.bind(this));

            if (input.name == 'get_pack') {
                input.addEventListener('change', this.onChangeHandleDelivery.bind(this));
            }
        });

        document.querySelectorAll('select.input-select').forEach((select: HTMLSelectElement) => {
            this.selects.set(select.name, select);
            this.setData(select.name, select.value);
        });

        this.btnSave = document.getElementById('btn-save') as HTMLButtonElement;

        this.handleChangeProvince = this.onchangeProvince.bind(this);

        if (this.btnSave.dataset.action == 'update') {
            this.inputReloadFile = document.getElementById('input-reload-file') as HTMLInputElement;
            this.initUpdateActions();
        }
    }

    private initEvents(): void {
        this.selects.forEach((select: HTMLSelectElement) => {
            select.addEventListener('change', this.onChangeInputSelect.bind(this, select.name));

            if (select.name == 'delivery_type') {
                select.addEventListener('change', this.onChangeDeliveryType.bind(this));
            }
        });

        this.btnSave.addEventListener('click', this.onClickSaveData.bind(this));

        if (this.inputReloadFile) {
            this.inputReloadFile.addEventListener('change', this.onChangeReloadDocument.bind(this));
        }
    }

    private initUpdateActions() {
        this.data = JSON.parse(window.localStorage.getItem(STORAGE_ITEM));

        this.handleDocuments.set(this.data.request.documents);

        delete this.data.request.documents;

        document.querySelectorAll('.upgradeable')
            .forEach((button: HTMLButtonElement) => {
                button.addEventListener('click', this.onClickHandleUpdateFile.bind(this));
            });

        window.localStorage.removeItem(STORAGE_ITEM);
        this.action = 'update';
    }

    private async onClickHandleUpdateFile(event: MouseEvent) {
        const button = event.currentTarget as HTMLButtonElement;
        const dts = button.dataset;

        if (dts.status === 'pending') {
            this.handleDocuments.setDeletable(button.dataset.id);
            button.parentElement.parentElement.remove();
        } else if (dts.status === 'observed') {
            this.currentButtonReload = button;
            this.inputReloadFile.click();
        }
    }

    private async onChangeReloadDocument(event: Event) {
        const input = event.currentTarget as HTMLInputElement;
        const allowedTypes = ['image/png', 'image/jpeg','image/jpg', 'image/bmp', 'image/webp', 'application/pdf'];
        const file = input.files[0];

        if (!allowedTypes.includes(file.type)) {
            Notifier.error('Solo imagenes o archivos pdf.');
            return;
        }

        if (file.name.match(/.*\.(gif|jpe?g|bmp|png)$/igm)) {
            await new Compressor(file, {
                quality: 0.6,
                maxWidth: 1024,
                success: async (result: File) => {
                    const path = await this.handleDocuments.loadFile(result);
                    this.handleDocuments.setUpdatable(
                        this.currentButtonReload.dataset.id,
                        DocumentStatus.PENDING,
                        path
                    );

                    const graphic = this.currentButtonReload
                        .parentElement
                        .parentElement
                        .querySelector('.mdc-list-item__graphic') as HTMLLinkElement;

                    graphic.classList.remove('material-icons');
                    graphic.textContent = '';
                    graphic.style.backgroundImage = `url(${path})`;
                }
            });
        } else if (file.name.match(/.*\.pdf/igm)) {
            const path = await this.handleDocuments.loadFile(file);
            this.handleDocuments.setUpdatable(
                this.currentButtonReload.dataset.id,
                DocumentStatus.PENDING,
                path
            );

            const graphic = this.currentButtonReload
                .parentElement
                .parentElement
                .querySelector('.mdc-list-item__graphic') as HTMLLinkElement;

            graphic.classList.add('material-icons');
            graphic.textContent = 'picture_as_pdf';
            graphic.style.removeProperty('background-image');
        }

        input.value = null;
        console.log(this.handleDocuments.getUpdatables());
    }

    private onChangeDeliveryType(event: Event) {
        const select = event.currentTarget as HTMLSelectElement;

        if (select.value == 'pick_in_plant') {
            document.querySelectorAll('.type-pick_in_plant')
            .forEach((element: HTMLSelectElement) => {
                this.clearInputs(element.name, false);
                this.setData(element.name, element.value);
            });
            document.querySelectorAll('.type-delivery')
            .forEach((element: HTMLSelectElement) => {
                this.clearInputs(element.name, true);
                this.setData(element.name, element.value);
            });
            (document.querySelector('.type-pick_in_plant') as HTMLElement).focus();
        } else if (select.value == 'delivery') {
            document.querySelectorAll('.type-pick_in_plant')
            .forEach((element: HTMLSelectElement) => {
                this.clearInputs(element.name, true);
                this.setData(element.name, element.value);
            });
            document.querySelectorAll('.type-delivery')
            .forEach((element: HTMLSelectElement) => {
                this.clearInputs(element.name, false);
                this.setData(element.name, element.value);
            });
            (document.querySelector('.type-delivery') as HTMLElement).focus();
        }

        this.setData(select.name, select.options[select.selectedIndex].value);
    }

    private onChangeHandleDelivery(event: MouseEvent) {
        const input = event.currentTarget as HTMLInputElement;
        const val = (input.value) == '1';

        if (!val) {
            document.querySelectorAll('.handle-pack')
            .forEach((element: HTMLInputElement | HTMLSelectElement) => {
                this.clearInputs(element.name, !val);
                this.setData(element.name, null);
            });
        } else {
            this.selects.get('delivery_type').disabled = false;
            this.selects.get('delivery_type').focus();
            this.setData(
                this.selects.get('delivery_type').name,
                this.selects.get('delivery_type').value
            );
        }
    }

    private onChangeRadio(event: MouseEvent) {
        const element: HTMLInputElement = event.currentTarget as HTMLInputElement;
        this.setData(element.name, (element.value) == '1');
    }

    private onChangeInputSelect(name: string, event: Event) {
        const element = this.selects.get(name);
        this.setData(name, element.options[element.selectedIndex].value);
    }

    private onChangeInput(name: string, event: Event) {
        const element = this.inputs.get(name);
        this.setData(name, element.value.trim());
    }

    private async loadPlants()
    {
        const select = this.selects.get('plants_id');
        const prevState = select.disabled;

        select.disabled = true;

        const response = await Http.api().get<Plants[]>('plants', {paginate: false});

        response.data.forEach(plant => {
            const option = createElement('option');

            option.textContent = plant.name;
            option.value = plant.id.toString();

            if (plant.id.toString() == select.dataset.value) {
                select.options.item(0).selected = false;
                option.selected = true;
            }

            select.append(option);
        });

        select.disabled = prevState;
    }

    private async loadEducationLevels()
    {
        const select = this.selects.get('education_levels_id');

        select.disabled = true;

        const response = await Http.api().get<EducationLevels[]>(
            'education_levels',
            { paginate: false, sort: {id: 'asc'} }
        );

        select.disabled = false;

        response.data.forEach(edu => {
            const option = createElement('option');

            option.textContent = edu.name;
            option.value = edu.id.toString();

            if (edu.id.toString() == select.dataset.value) {
                select.options.item(0).selected = false;
                option.selected = true;
            }

            select.append(option);
        });

    }

    private async loadDepartments()
    {
        const select = this.selects.get('departments');
        const value = select.dataset.value;
        const prevState = select.disabled;

        select.disabled = true;

        const response = await Http.api().get<Departments[]>(
            'ubigeo/departments',
            { paginate: false, sort: {name: 'asc'} }
        );

        select.disabled = prevState;

        response.data.forEach(department => {
            const option = createElement('option');

            option.textContent = department.name;
            option.value = department.id.toString();

            if (department.id.toString() == value) {
                select.options.item(0).selected = false;
                option.selected = true;
            }

            select.append(option);
        });

        select.addEventListener('change', this.onChangeDepartment.bind(this));

        if (value) {
            select.dispatchEvent(new Event('change'));
        }
    }

    private async loadProvinces(departmentId: number)
    {
        const select = this.selects.get('provinces');
        const value = select.dataset.value;

        select.disabled = true;

        const response = await Http.api().get<Departments[]>(
            'ubigeo/provinces',
            {
                departments: [departmentId],
                paginate: false,
                sort: { name: 'asc' },
                fields: ['id', 'name']
            }
        );

        select.disabled = false;

        const disableOption = createElement('option', {});

        disableOption.disabled = true;
        disableOption.selected = true;
        disableOption.value = '';

        select.append(disableOption);

        response.data.forEach(department => {
            const option = createElement('option');

            option.textContent = department.name;
            option.value = department.id.toString();

            if (department.id.toString() == value) {
                select.options.item(0).selected = false;
                option.selected = true;
            }

            select.append(option);
        });

        select.addEventListener('change', this.handleChangeProvince);

        if (value) {
            select.dispatchEvent(new Event('change'));
        }
    }

    private async loadDistricts(proviceId: number)
    {
        const select = this.selects.get('districts_id');
        const value = select.dataset.value;

        select.disabled = true;

        const response = await Http.api().get<Departments[]>(
            'ubigeo/districts',
            {
                provinces: [proviceId],
                paginate: false,
                sort: { name: 'asc' },
                fields: ['id', 'name']
            }
        );

        select.disabled = false;

        const disableOption = createElement('option', {});

        disableOption.disabled = true;
        disableOption.selected = true;
        disableOption.value = '';

        select.append(disableOption);

        response.data.forEach(department => {
            const option = createElement('option');

            option.textContent = department.name;
            option.value = department.id.toString();

            if (department.id.toString() == value) {
                select.options.item(0).selected = false;
                option.selected = true;
            }

            select.append(option);
        });

        select.value = value;
    }

    private onChangeDepartment(event: Event) {
        const element = event.currentTarget as HTMLSelectElement;
        const province = this.selects.get('provinces');
        const district = this.selects.get('districts_id');

        province.value = '';
        province.disabled = true;
        district.value = '';
        district.disabled = true;

        while (province.childNodes.length > 1) {
            province.removeChild(province.lastChild);
        }

        while (district.childNodes.length > 1) {
            district.removeChild(district.lastChild);
        }

        if (element.value.trim().length > 0) {
            province.removeEventListener('change', this.handleChangeProvince, true);
            this.loadProvinces(parseInt(element.value));
        }
    }

    private onchangeProvince(event: Event) {
        const element = event.currentTarget as HTMLSelectElement;
        const child = this.selects.get('districts_id');

        child.value = '';
        child.disabled = true;

        while (child.childNodes.length > 1) {
            child.removeChild(child.lastChild);
        }

        if (element.value.trim().length > 0) {
            this.loadDistricts(parseInt(element.value));
        }
    }

    private setData(name: string, value: any) {
        const main = [
            'name', 'paternal_surname', 'maternal_surname', 'gender', 'birth_date'
        ];
        const responsableElements = [
            'education_levels_id', 'get_pack', 'get_loan', 'delivery_type',
            'plants_id', 'responsable_name', 'responsable_dni',
            'responsable_phone', 'districts_id', 'address', 'address_reference'
        ];

        if (responsableElements.includes(name)) {
            this.data.request = {...this.data.request, [name]: value};
        } else if (main.includes(name)) {
            this.data = {...this.data, [name]: value}
        }
    }

    private transformErrors(valid: Validator.Validator<Partial<DataChildren>>): string {
        const msg_errors: string[] = [];
        const errors = valid.errors.all();

        console.log(this.data);

        for (const name in errors) {
            if (Object.prototype.hasOwnProperty.call(errors, name)) {
                errors[name].filter(el => el).forEach(msg => msg_errors.push(msg));

                if (this.inputs.has(name)) {
                    this.inputs.get(name).valid = false;
                } else if (this.selects.has(name)) {
                    this.selects.get(name).setAttribute('valid', 'true');
                }
            }
        }

        return '- ' + msg_errors.join('<br />- ');
    }

    private defaultData(): DataChildren {
        return {
            name: null,
            paternal_surname: null,
            maternal_surname: null,
            gender: 'femenino',
            birth_date: null,
            request: {
                education_levels_id: null,
                get_loan: true,
                get_pack: true,
                delivery_type: 'pick_in_plant',
                plants_id: null,
                responsable_name: null,
                responsable_dni: null,
                responsable_phone: null,
                address: null,
                address_reference: null,
                districts_id: null,
                documents: []
            }
        };
    }

    private clearInputs(name: string, disabled: boolean = true) {
        if (this.inputs.has(name)) {
            this.inputs.get(name).value = '';
            this.inputs.get(name).disabled = disabled;
        } else if (this.selects.has(name)) {
            this.selects.get(name).value = '';
            this.selects.get(name).disabled = disabled;
        }
    }
}

new Children();
