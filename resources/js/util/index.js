const util = {
	base64ToUint8: (input) => {
		return Uint8Array.from(
			atob(input.replace(/-/g, "+").replace(/_/g, "/")),
			(c) => c.charCodeAt(0),
		);
	},
	arrayBufferToBase64: (arrayBuffer) => {
		return btoa(String.fromCharCode(...new Uint8Array(arrayBuffer))).replace(
			/=/g,
			"",
		);
	},
	stringifyJsonRecursive: function (data) {
		if (data === undefined) {
			return undefined;
		}

		if (data === null) {
			return "null";
		}

		if (data.constructor === String) {
			return `"${data.replace(/"/g, '\\"')}"`;
		}

		if (data.constructor === Number) {
			return String(data);
		}

		if (data.constructor === Boolean) {
			return data ? "true" : "false";
		}

		if (data.constructor === Array) {
			return `[${data
				.reduce((acc, v) => {
					if (v === undefined) {
						acc.push("null");
					} else {
						acc(this.stringifyJsonRecursive(v));
					}

					return acc;
				}, [])
				.join(",")}]`;
		}

		if (data.constructor === Object) {
			return `{${Object.keys(data)
				.reduce((acc, k) => {
					if (data[k] !== undefined) {
						acc.push(
							`${this.stringifyJsonRecursive(k)}:${this.stringifyJsonRecursive(
								data[k],
							)}`,
						);
					}

					return acc;
				}, [])
				.join(",")}}`;
		}

		return "{}";
	},
};

try {
	window.FP2024Util = util;
} catch (_e) {
	console.warn("Not in browser env");
}

export default util;
