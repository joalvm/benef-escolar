import axios, {
    AxiosInstance,
    AxiosError,
    AxiosRequestConfig,
    AxiosResponse,
} from "axios";

export default class Http {
    private client: AxiosInstance;
    private errors: AxiosError;

    constructor(config: AxiosRequestConfig = {}) {
        this.client = axios.create({
            ...{
                paramsSerializer: this.paramSerializer.bind(this),
            },
            ...config,
        });

        this.client.interceptors.response.use(
            this.handleResponseSuccess,
            this.handleResponseError.bind(this)
        );

        this.client.interceptors.request.use(
            this.handleRequestConfiguration.bind(this)
        );

        this.errors = null;
    }

    static api(config: AxiosRequestConfig = {}): Http {
        return new Http({
            baseURL: process.env.apiUrl,
        });
    }

    getHardCoded<T>(path: string): Promise<Response<T>> {
        return this.client.get<T, Response<T>>(path);
    }

    get<T>(path: string, parameters?: object): Promise<Response<T>> {
        return this.client.get<T, Response<T>>(path, { params: parameters });
    }

    post<K, T>(path: string, body?: K): Promise<Response<T>> {
        return this.client.post<T, Response<T>>(path, body);
    }

    put<K, T>(path: string, body?: K): Promise<Response<T>> {
        return this.client.put<T, Response<T>>(path, body);
    }

    delete<T>(path: string, parameters?: object): Promise<Response<T>> {
        return this.client.delete<T, Response<T>>(path, { params: parameters });
    }

    handleResponseSuccess(response: AxiosResponse) {
        Loader.hide();

        return response.data;
    }

    handleResponseError(error: AxiosError) {
        Loader.hide();

        try {
            const response = JSON.parse(error.request.response);

            this.errors = response.errors;

            return Object.keys(response).includes("data")
                ? response
                : Object.assign(response, { data: {} });
        } catch (exception) {
            return {
                error: true,
                code: error.request.status,
                message: error.request.statusText,
                data: null,
            };
        }
    }

    handleRequestConfiguration(config: AxiosRequestConfig) {
        this.setAuthorizationToken(config);
        this.setPeriodId(config);

        Loader.show();

        return config;
    }

    setAuthorizationToken(config: AxiosRequestConfig) {
        const session: any =
            JSON.parse(localStorage.getItem("session")) ?? null;

        if (session) {
            config.headers = {
                ...config.headers,
                Authorization: `Bearer ${session.token}`,
            };
        }
    }

    setPeriodId(config: AxiosRequestConfig) {
        const period = JSON.parse(
            window.localStorage.getItem("selected_period")
        ) ?? null;

        if (period) {
            config.headers = {
                ...config.headers,
                Period: period.id,
            };
        }
    }

    private paramSerializer(params: object) {
        const encodeKeys = process.env.environment === "production";

        return Object.entries(this.serializeParams(params, encodeKeys))
            .map(function (pair) {
                const encode = encodeKeys ? pair.map(encodeURIComponent) : pair;
                return encode.join("=");
            })
            .join("&");
    }

    private serializeParams(
        obj: object,
        encodeKey: boolean,
        path: string = null,
        result: any = {}
    ) {
        encodeKey = encodeKey || false;

        // Si es la primerera llamada osea cuando no es un llamado de la recursividad
        if (result === undefined) {
            const type = Object.prototype.toString.call(obj);

            if (type === "[object Object]") {
                result = {};
            } else if (type === "[object Array]") {
                result = [];
            } else {
                return;
            }
        }

        for (const key in obj) {
            if (!obj.hasOwnProperty(key)) continue;

            let val = (obj as any)[key];

            if (val == null) continue;

            switch (Object.prototype.toString.call(val)) {
                case "[object Array]":
                case "[object Object]":
                    this.serializeParams(
                        val,
                        encodeKey,
                        this.joinParams(path, key),
                        result
                    );
                    break;
                default:
                    result[this.joinParams(path, key)] = !encodeKey
                        ? encodeURIComponent(val)
                        : val;
                    break;
            }
        }

        return result;
    }

    private joinParams(path: string, key: string) {
        return path != null ? path + "[" + key + "]" : key;
    }
}

window.Http = Http;
