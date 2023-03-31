import { h } from "dom-chef";
import TestComponent from "./components/TestComponent";

document.body.append(<TestComponent username={"joran"} />);

fetch("http://api.boodschappenservice.loc/leveranciers").then(res => res.json).then(console.log);
