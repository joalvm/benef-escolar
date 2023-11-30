import { MDCLinearProgress } from '@material/linear-progress';

class Loader {

    parent: HTMLElement;
    static Element: MDCLinearProgress;

    constructor(parent?: HTMLElement) {
        this.parent = parent;
    }

    static show(parent?: HTMLElement) {
        Loader.element(parent).open();
    }

    static hide() {
        let element = Loader.element();

        element.close();
        element.root.remove();
        element.destroy();

        Loader.Element = undefined;
        element = undefined;
    }

    private static element(parent?: HTMLElement): MDCLinearProgress {
        if (Loader.Element) {
            return Loader.Element;
        }

        const container = createElement('div', {
            role: 'progressbar',
            className: [
                'mdc-linear-progress',
                'mdc-linear-progress--indeterminate'
            ],
            aria: { label: 'Progress Bar'}
        });

        container.append(...[
            Loader.buffer(),
            Loader.primary(),
            Loader.secondary()
        ]);

        if (parent) {
            parent.prepend(container);
        } else {
            document.body.prepend(container);
        }

        this.Element = new MDCLinearProgress(container);


        return this.Element;
    }

    private static buffer(): HTMLDivElement {
        const container = createElement('div', {
            className: ['mdc-linear-progress__buffer']
        });

        container.append(...[
            createElement('div', {className: ['mdc-linear-progress__buffer-bar']}),
            createElement('div', {className: ['mdc-linear-progress__buffer-dots']})
        ]);

        return container;
    }

    private static primary(): HTMLDivElement {
        const container = createElement('div', {
            className: [
                'mdc-linear-progress__bar',
                'mdc-linear-progress__primary-bar'
            ]
        });

        container.append(createElement('div', {
            className: ['mdc-linear-progress__bar-inner']
        }));

        return container;
    }

    private static secondary(): HTMLDivElement {
        const container = createElement('div', {
            className: [
                'mdc-linear-progress__bar',
                'mdc-linear-progress__secondary-bar'
            ]
        });

        container.append(createElement('div', {
            className: ['mdc-linear-progress__bar-inner']
        }));

        return container;
    }
}

window.Loader = Loader;

export default Loader;
