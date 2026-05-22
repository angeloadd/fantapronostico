# Step 4: Tailwind 4 + DaisyUI 5 — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Tailwind 3 + DaisyUI 4 CSS pipeline with Tailwind 4 + DaisyUI 5, rewrite the custom `fp2024` theme in CSS syntax, and remove the `tailwind.config.js` entirely.

**Architecture:** CSS/npm-only changes. No PHP changes. The automated `@tailwindcss/upgrade` tool handles the majority of the migration. The `fp2024` custom DaisyUI theme must be manually rewritten from a JS object spread to a CSS `@plugin` block. Sass stays for custom styles.

**Tech Stack:** Tailwind CSS 4, DaisyUI 5, `@tailwindcss/vite`, Sass, Vite 6 (already in place from Step 3)

> ⚠️ **Review gate:** Do NOT commit at any point without first presenting the diff to the user and receiving explicit approval.

---

### Task 1: Run the official Tailwind upgrade tool

**Files:**
- Auto-modified by tool: `package.json`, `resources/scss/app.scss`, `tailwind.config.js` (deleted or converted), `postcss.config.js`, `vite.config.js`

- [ ] **Step 1: Run `@tailwindcss/upgrade` (requires Node 24)**

```bash
npx @tailwindcss/upgrade 2>&1 | tee /tmp/tw-upgrade.log
```

The tool will:
- Update `tailwindcss` to v4 in `package.json`
- Add `@tailwindcss/vite` to `package.json`
- Remove `autoprefixer` from `package.json`
- Convert `tailwind.config.js` `content` paths into CSS `@source` directives in `app.scss`
- Replace `@tailwind base/components/utilities` directives with `@import "tailwindcss"`
- Update `postcss.config.js`

- [ ] **Step 2: Review what the tool changed**

```bash
git diff --stat
```

Read the diff for each file the tool touched before proceeding.

- [ ] **Step 3: Run npm install to get the new packages**

```bash
npm install 2>&1 | tee /tmp/npm-step4.log
grep -E "npm ERR|ERESOLVE" /tmp/npm-step4.log | head -10
```

---

### Task 2: Upgrade DaisyUI to v5

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Update daisyui in package.json**

```bash
npm install daisyui@^5.0 --save-dev
```

- [ ] **Step 2: Verify install**

```bash
npm list daisyui
```

Expected: `daisyui@5.x.x`

---

### Task 3: Fix app.scss — Tailwind import and DaisyUI plugin

**Files:**
- Modify: `resources/scss/app.scss`

- [ ] **Step 1: Check what the upgrade tool produced**

Read the current state of `resources/scss/app.scss` using Serena `get_symbols_overview` or `find_symbol`, then view the full file since it is small.

- [ ] **Step 2: Ensure the Tailwind and DaisyUI imports are correct**

The top of `app.scss` must read:

```scss
@import "tailwindcss";
@plugin "daisyui";
```

If the upgrade tool left `@tailwind` directives or used `@source` instead of `@import "tailwindcss"`, correct them with Serena `replace_content`.

The Sass variables and custom `.fp2024` class block below the imports are **unchanged**.

---

### Task 4: Rewrite the fp2024 custom theme

**Files:**
- Delete: `tailwind.config.js` (if the upgrade tool didn't already delete it)
- Modify: `resources/scss/app.scss`

- [ ] **Step 1: Delete tailwind.config.js if it still exists**

```bash
ls tailwind.config.js 2>/dev/null && rm tailwind.config.js || echo "already gone"
```

- [ ] **Step 2: Add the fp2024 theme as a CSS @plugin block**

Append to `resources/scss/app.scss` (after `@plugin "daisyui"`), using Serena `insert_after_symbol` or `replace_content`:

```scss
@plugin "daisyui/theme" {
  name: fp2024;
  default: true;
  color-scheme: light;

  /* Custom brand colours */
  --color-primary: oklch(from #00AACC l c h);
  --color-secondary: oklch(from #01B359 l c h);
  --color-accent: oklch(from #00849E l c h);
  --color-neutral: oklch(from #13525F l c h);
  --color-info: oklch(from #14B0D0 l c h);
  --color-success: oklch(from #01DB6E l c h);

  /* Content colours (white on all brand colours) */
  --color-primary-content: oklch(from #ECEFF4 l c h);
  --color-secondary-content: oklch(from #ECEFF4 l c h);
  --color-accent-content: oklch(from #ECEFF4 l c h);
  --color-info-content: oklch(from #ECEFF4 l c h);
  --color-success-content: oklch(from #ECEFF4 l c h);
  --color-error-content: oklch(from #ECEFF4 l c h);
  --color-warning-content: oklch(from #ECEFF4 l c h);

  /* Nord base palette */
  --color-base-100: oklch(from #ECEFF4 l c h);
  --color-base-200: oklch(from #E5E9F0 l c h);
  --color-base-300: oklch(from #D8DEE9 l c h);
  --color-base-content: oklch(from #2E3440 l c h);
}
```

Note: `#ECEFF4` is Nord's `base-100`. DaisyUI 5 expects OKLCH colour values. The `oklch(from <hex> l c h)` syntax converts hex to OKLCH — supported in modern browsers. If you prefer to resolve exact OKLCH values, use https://oklch.com to convert each hex.

Also add the `nord` theme so it remains available alongside fp2024:

```scss
@plugin "daisyui" {
  themes: nord, fp2024;
}
```

Replace the `@plugin "daisyui"` line added in Task 3 Step 2 with this version.

---

### Task 5: Update postcss.config.js

**Files:**
- Modify: `postcss.config.js`

- [ ] **Step 1: Check what the upgrade tool produced**

Read `postcss.config.js`. The upgrade tool should have updated it to:

```js
export default {
  plugins: {
    "@tailwindcss/postcss": {},
  },
};
```

If it still contains `tailwindcss` or `autoprefixer` entries, update it to match the above using Serena `replace_content`.

---

### Task 6: Audit for raw DaisyUI CSS variable usage

DaisyUI 5 renames CSS variables (e.g. `--p` → `--color-primary`). This codebase has none detected at plan time, but verify:

- [ ] **Step 1: Grep blade files for raw CSS variable usage**

```bash
grep -rn "var(--" app/Modules --include="*.blade.php" | head -20
grep -rn "var(--" resources/views --include="*.blade.php" | head -20
grep -rn "var(--" resources/scss/ | head -20
```

Expected: no results (confirmed clean at plan time).

If results appear, map old → new names:

| DaisyUI 4 | DaisyUI 5 |
|---|---|
| `--p` | `--color-primary` |
| `--s` | `--color-secondary` |
| `--a` | `--color-accent` |
| `--n` | `--color-neutral` |
| `--b1` | `--color-base-100` |
| `--b2` | `--color-base-200` |
| `--b3` | `--color-base-300` |
| `--bc` | `--color-base-content` |
| `--in` | `--color-info` |
| `--su` | `--color-success` |
| `--wa` | `--color-warning` |
| `--er` | `--color-error` |

Use Serena `replace_content` with the file path to apply renames without reading full files.

---

### Task 7: Build and visual check

- [ ] **Step 1: Run production build**

```bash
npm run build 2>&1 | tee /tmp/build-step4.log
grep -E "error|Error|✓ built" /tmp/build-step4.log | tail -20
```

Expected: `✓ built in Xs` with no errors.

- [ ] **Step 2: Start dev server**

```bash
npm run dev
```

Expected: Vite starts cleanly with no errors.

- [ ] **Step 3: Start Laravel and visually verify**

```bash
php artisan serve
```

Open the app in a browser and verify:
- Main page renders with correct colours (teal primary `#00AACC`, green secondary `#01B359`)
- DaisyUI components (buttons, cards, modals) display correctly
- No unstyled content (white/blank pages)
- Nord theme applies to the non-fp2024 pages if any

---

### Task 8: Request review before committing

- [ ] **Step 1: Show the full diff**

```bash
git diff --stat
git diff resources/scss/app.scss postcss.config.js package.json
git status  # confirm tailwind.config.js is deleted
```

- [ ] **Step 2: Present diff to user and wait for explicit approval before running any `git commit` command.**
