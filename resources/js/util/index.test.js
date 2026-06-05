import assert from "node:assert/strict";
import { describe, it } from "node:test";
import util from "./index.js";

describe("FP2024Util can", () => {
	it("recursively stringify a json", (_t) => {
		assert.equal(
			util.stringifyJsonRecursive({
				ciao: undefined,
				ehi: null,
				say: {
					something: 1,
					ect: 3,
				},
			}),
			'{"ehi":null,"say":{"something":1,"ect":3}}',
		);
	});
});
