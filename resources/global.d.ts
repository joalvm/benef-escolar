import { MDCTextField as MDCTextFieldBase } from '@material/textfield';
import { MDCRipple as MDCRippleBase } from '@material/ripple';
import { Compressor as CompressorBase } from 'compressorjs';
import { Validator as ValidatorBase } from 'validatorjs';
import { AlertOptions } from './assets/ts/components/Confirm';

declare global {

    enum RoleTypes {
        USER='user',
        ADMIN='admin',
        SUPER_ADMIN='super_admin'
    };

    interface ParamsGET {
        offset?: number;
        limit?: number;
        contains?: {
            fields: string[],
            text: string
        },
        sort?: {[T: string]: 'desc' | 'asc' }
    }

    interface GlobalNotifierItems {
        active: boolean;
        items: NotifierItem[];
    }
    interface Response<T> {
        error: boolean;
        message: string;
        code: number;
        data: T;
    }

    interface UserSession {
        dni: string;
        enabled: boolean;
        isVerified: boolean;
        names: string;
        person_id: number;
        role: RoleTypes;
        user_id: number;
    }

    interface StoragePeriod {
        id: number;
        name: string;
    }

    interface Plants {
        id: number,
        name: string,
        created_at: Date
    }

    interface EducationLevels {
        id: number,
        name: string,
        amount: number,
        created_at: Date
    }

    interface Departments {
        id: number,
        name: string,
        created_at: string
    }

    interface CreateElementOptions {
        id?: string;
        name?: string;
        role?: string;
        className?: string[];
        styles?: Partial<CSSStyleDeclaration>;
        dataset?: { [T: string]: any };
        aria?: { [T: string]: any };
    }

    class MDCTextField extends MDCTextFieldBase {};
    class MDCRipple extends MDCRippleBase {};

    function createElement<K extends keyof HTMLElementTagNameMap>(
        tagName: K,
        options?: CreateElementOptions
    ): HTMLElementTagNameMap[K];

    function statusMessage(status: string, short: bool = true);

    function getStoragePeriod(): StoragePeriod | null;

    // CLASES GLOBALES
    class Compressor extends CompressorBase {
        constructor(File: File, {
            quality: number,
            maxWidth: number,
            success: Function
        })
    };

    class Validator extends ValidatorBase {};

    class Alert {
        constructor(options: AlertOptions);
    }

    class Loader {
        static show(parent?: HTMLElement);
        static hide();
    }

    class Notifier {
        constructor(options: Partial<NotifierOptions>);
        static info(message: string, title?: string): Notifier;
        static warning(message: string, title?: string): Notifier;
        static success(message: string, title?: string): Notifier;
        static error(message: string, title?: string): Notifier;
        show(): Notifier;
        hide(): Notifier;
    }

    class Http {
        constructor(config?: AxiosRequestConfig);
        static api(config?: AxiosRequestConfig): Http;
        get<T>(path: string, parameters?: any): Promise<Response<T>>;
        post<K, T>(path: string, body?: K): Promise<Response<T>>;
        put<K, T>(path: string, body?: K): Promise<Response<T>>;
        delete<T>(path: string, parameters?: object): Promise<Response<T>>;
        handleResponseSuccess<T>(success: AxiosResponse): Promise<Response<T>>;
    }

    interface Window {
        NotifierItems: GlobalNotifierItems;
        Loader: typeof Loader;
        Http: typeof Http;
        Notifier: typeof Notifier;
        Alert: typeof Alert;
        MDCTextField: typeof MDCTextField;
        MDCRipple: typeof MDCRipple;
        createElement: createElement;
        statusMessage: statusMessage;
        getStoragePeriod: getStoragePeriod;
        Compressor: typeof Compressor;
    }

    const user: UserSession;
}
