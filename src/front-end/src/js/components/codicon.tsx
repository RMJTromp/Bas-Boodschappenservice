import { h } from "dom-chef";

export default function Codicon(props) {
    return <i className={`codicon codicon-${props.name}`} />;
}

Codicon.defaultProps = {
    name: "",
}