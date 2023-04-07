declare global {
    interface Window {
        build: ((this: WindowEventHandlers, ev: CustomEvent) => any);
    }
}

Object.defineProperty(window, 'build', {
    set(v: any) {
        window.addEventListener("build", v);
    },
    get() {
        return null;
    }
})

export {};