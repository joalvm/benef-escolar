interface NotifierItem {
    id: string;
    options: NotifierOptions;
}

export enum NotifierTypes {
    DEFAULT = 'default',
    SUCCESS = 'success',
    WARNING = 'warning',
    DANGER = 'danger',
    INFO = 'info',
}

export enum Vertical {
    TOP = 'top',
    BOTTOM = 'bottom',
}

export enum Horizontal {
    LEFT = 'left',
    RIGHT = 'right',
    CENTER = 'center',
}

export interface Position {
    vertical: Vertical;
    horizontal: Horizontal;
}

export interface NotifierOptions {
    title?: string;
    message: string;
    type: NotifierTypes;
    withIcon: boolean;
    autoclose: boolean;
    timeout: number;
    position?: Position;
    animation?: 'slide';
    makeSkeleton?: (options: NotifierOptions) => HTMLElement;
}

export default class Notifier {
    private currentId: string;

    private default: NotifierOptions = {
        title: '',
        message: '',
        type: NotifierTypes.DEFAULT,
        withIcon: true,
        autoclose: true,
        timeout: 5000,
        makeSkeleton: null,
        animation: 'slide',
        position: {
            vertical: Vertical.TOP,
            horizontal: Horizontal.RIGHT,
        },
    };

    constructor(options: Partial<NotifierOptions> = {}) {
        window.NotifierItems.items.push({
            id: this.getId(),
            options: {
                ...this.default,
                ...options,
            },
        });
    }

    static info(message: string, title: string = 'Info') {
        return new Notifier({
            title: title,
            message: message,
            type: NotifierTypes.INFO,
        }).show();
    }

    static warning(message: string, title: string = 'Warning') {
        return new Notifier({
            title: title,
            message: message,
            type: NotifierTypes.WARNING,
        }).show();
    }

    static success(message: string, title: string = 'Success') {
        return new Notifier({
            title: title,
            message: message,
            type: NotifierTypes.SUCCESS,
        }).show();
    }

    static error(message: string, title: string = 'Error') {
        return new Notifier({
            title: title,
            message: message,
            type: NotifierTypes.DANGER,
        }).show();
    }

    show(): Notifier {
        if (
            !window.NotifierItems.active &&
            window.NotifierItems.items.length > 0
        ) {
            const item = window.NotifierItems.items.shift();

            this.currentId = item.id;
            window.NotifierItems.active = true;

            this.build(item);
        }

        return this;
    }

    hide() {
        console.log(this.currentId);

        return this;
    }

    private close(item: NotifierItem) {
        var notification = document.getElementById(item.id);

        if (notification) {
            notification.classList.remove(`${this.default.animation}-in`);
            notification.classList.add(`${this.default.animation}-out`);

            setTimeout(() => {
                if (notification) {
                    notification.parentNode.removeChild(notification);

                    if (window.NotifierItems.items.length > 0) {
                        this.build(window.NotifierItems.items.shift());
                    } else {
                        window.NotifierItems.active = false;
                    }
                }
            }, 600);

            return true;
        } else {
            return false;
        }
    }

    private build(item: NotifierItem) {
        const options = item.options;

        if (typeof options.timeout != 'number') {
            options.timeout = 0;
        }

        const element = this.makeSkeleton(options);

        element.setAttribute('id', item.id);
        element.querySelector('.notifier-body-title').innerHTML = options.title;
        element.querySelector('.notifier-body-message').innerHTML =
            options.message;

        element.classList.add(
            ...[
                `${options.position.vertical}-${options.position.horizontal}`,
                `${options.animation}-in`,
            ],
        );

        document.body.appendChild(element);

        if (options.autoclose) {
            setTimeout(() => {
                this.close(item);
            }, options.timeout);
        }

        element
            .querySelector<HTMLDivElement>('.notifier-close')
            .addEventListener('click', this.onClickClose(item));

        return item.id;
    }

    private makeSkeleton(options: NotifierOptions): HTMLElement {
        const container = Notifier.makeElement('div', {
            class: 'notifier ' + options.type,
        });

        const closeButton = Notifier.makeElement('span', {
            class: 'notifier-close',
        });

        closeButton.appendChild(
            Notifier.makeElement('span', { class: 'notifier-close-icon' }),
        );

        const body = Notifier.makeElement('div', {
            class: 'notifier-body',
        });

        const bodyIcon = Notifier.makeElement('div', {
            class: 'notifier-icon',
        });

        const bodyContent = Notifier.makeElement('div', {
            class: 'notifier-content',
        });

        bodyContent.appendChild(
            Notifier.makeElement('span', { class: 'notifier-body-title' }),
        );

        bodyContent.appendChild(
            Notifier.makeElement('div', { class: 'notifier-body-message' }),
        );

        bodyIcon.innerHTML = this.getIcons(options.type);

        body.appendChild(bodyIcon);
        body.appendChild(bodyContent);

        container.appendChild(closeButton);
        container.appendChild(body);

        return container;
    }

    private onClickClose(item: NotifierItem) {
        return () => {
            this.close(item);
        };
    }

    private getIcons(type: NotifierTypes): string {
        const icons = {
            success: `<path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>`,
            danger: `<path d="M11 15h2v2h-2v-2zm0-8h2v6h-2V7zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>`,
            warning: `<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z"/>`,
            info: `<path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>`,
            default: `<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2c.15-2.65 1.51-4.97 3.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43c2.02 1.45 3.39 3.77 3.54 6.42z"/>`,
        };

        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">${icons[type]}</svg>`;
    }

    private getId() {
        return (
            Math.random().toString(36).substring(2, 15) +
            Math.random().toString(36).substring(2, 15)
        );
    }

    private static makeElement(elem: string, attrs: any) {
        let el = document.createElement(elem);
        for (const prop in attrs) {
            el.setAttribute(prop, attrs[prop]);
        }
        return el;
    }
}

window.Notifier = Notifier;
window.NotifierItems = {
    active: false,
    items: [],
};
