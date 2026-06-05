export default {
	mode: "corporate",

	themes: {
		light: "corporate",
		dark: "luxury",
	},

	toggle() {
		this.mode =
			this.mode === this.themes.dark ? this.themes.light : this.themes.dark;

		window.localStorage.setItem("fpTheme", this.mode);
	},

	isDarkMode() {
		return this.mode === this.themes.dark;
	},

	getIcon() {
		return this.mode === this.themes.dark ? "☽" : "☼";
	},

	init() {
		const theme = window.localStorage.getItem("fpTheme");
		if (null !== theme) {
			this.mode = theme;
			return;
		}

		this.mode =
			this.themes[
				window.matchMedia?.("(prefers-color-scheme: dark)").matches
					? "dark"
					: "light"
			];
	},
};
