import {h} from "dom-chef";
import "./modal.pcss";

export interface ModalElement extends HTMLDivElement {
    open: boolean
    closeable: boolean
}

export default function Modal(props) {
    const modalElement : ModalElement = <div className="modal" open></div>;
    const card : HTMLDivElement = <div className="card"></div>;
    modalElement.append(card);

    let closeable : boolean = Boolean(props.closeable || true);

    Object.defineProperties(modalElement, {
        open: {
            get(): boolean {
                return modalElement.hasAttribute("open");
            },
            set(v: any) {
                if(Boolean(v)) {
                    document.body.append(modalElement);
                    modalElement.setAttribute("open", "");
                } else {
                    modalElement.remove();
                    modalElement.removeAttribute("open");
                }
            }
        },
        closeable: {
            get(): boolean {
                return closeable
            },
            set(v: any) {
                closeable = Boolean(v);
            }
        }
    })

    new MutationObserver((mutations) => {
        (mutations
            .filter(mutation => mutation.type === "childList")
            .map(mutation => [...mutation.addedNodes])
            .flat() as Element[])
            .forEach(el => {
                el.remove()
                card.append(el);
            });

        if(mutations.some(mutation => mutation.type === "attributes" && mutation.attributeName === "open"))
            modalElement.dispatchEvent(new Event(modalElement.hasAttribute("open") ? "open" : "close"));
    }).observe(modalElement, { childList: true, attributes: true, attributeFilter: ["open"] });

    let mouseDownTarget = null;
    modalElement.onmousedown = (e) => mouseDownTarget = e.target;
    modalElement.onmouseup = (e) => {
        if(e.target === mouseDownTarget && e.target === modalElement)
            modalElement.removeAttribute("open");
        mouseDownTarget = null;
    }

    return modalElement;
}

Modal.defaultProps = {
    closeable: true
}