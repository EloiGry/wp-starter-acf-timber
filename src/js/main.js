import Alpine from "alpinejs";
import persist from "@alpinejs/persist";

Alpine.plugin(persist); 

import "./cart";


window.Alpine = Alpine;
Alpine.start();
