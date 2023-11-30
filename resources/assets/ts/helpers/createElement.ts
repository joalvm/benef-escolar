function createElement<K extends keyof HTMLElementTagNameMap>(
    tagName: K,
    options: CreateElementOptions = {}
): HTMLElementTagNameMap[K] {
    options = options || {};
    let element: HTMLElementTagNameMap[K] = document.createElement(tagName);

    if (options.id) {
        element.id = options.id;
    }

    if (options.name) {
        element.setAttribute('name', options.name);
    }

    if (options.className) {
        options.className.forEach(klass => {
            element.classList.add(klass);
        });
    }

    if (options.role) {
        element.setAttribute('role', options.role);
    }

    if (options.styles) {
        for (const style in options.styles) {
            if (Object.prototype.hasOwnProperty.call(options.styles, style)) {
                element.style[style] = options.styles[style];
            }
        }
    }

    if (options.dataset) {
        for (const data in options.dataset) {
            if (Object.prototype.hasOwnProperty.call(options.dataset, data)) {
                element.dataset[data] = options.dataset[data];
            }
        }
    }

    if (options.aria) {
        for (const data in options.aria) {
            if (Object.prototype.hasOwnProperty.call(options.aria, data)) {
                element.setAttribute(`aria-${data}`, options.aria[data]);
            }
        }
    }

    return element;
}

window.createElement = createElement;

export default createElement;
