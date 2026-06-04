import Alpine from "alpinejs";
import theme from "./theme.js";
import countdown from './countdown.js';

window.Alpine = Alpine;

Alpine.store("theme", theme);
Alpine.data("countdown", countdown)

Alpine.start();
