import { h } from "dom-chef";
import "./TestComponent.pcss";

export default function TestComponent({username}) {
    return (
        <div class={"testcomponetn"}>
            {username}
        </div>
    );
}

TestComponent.defaultProps = {
    username: null
}