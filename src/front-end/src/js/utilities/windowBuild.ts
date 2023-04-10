declare global {
    interface Window {
        onbuild: ((this: WindowEventHandlers, ev: CustomEvent) => any);
        build: (data?: any) => CustomEvent;
        onupdate: ((this: WindowEventHandlers, ev: CustomEvent) => any);
        update: (data?: any) => CustomEvent;
    }
}

Object.defineProperties(window, {
    build: {
        get(): any {
            return function (data: any) {
                const event = new CustomEvent("build", {
                    detail: data
                });
                window.dispatchEvent(event);
                window.update();
                return event;
            }
        }
    },
    onbuild: {
        set(v: any) {
            window.addEventListener("build", v);
        }
    },
    update: {
        get(): any {
            return function (data: any) {
                const event = new CustomEvent("update", {
                    detail: data
                });
                window.dispatchEvent(event);
                return event;
            }
        }
    },
    onupdate: {
        set(v: any) {
            window.addEventListener("update", v);
        }
    }
})

export {};