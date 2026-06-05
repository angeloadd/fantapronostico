import Alpine from "alpinejs";
import countdown from "./countdown.js";
import theme from "./theme.js";

window.Alpine = Alpine;

Alpine.store("theme", theme);
Alpine.data("countdown", countdown);

Alpine.start();
