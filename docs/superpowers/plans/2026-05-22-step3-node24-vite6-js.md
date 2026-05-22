# Step 3: Node 24 + Vite 6 + JS Libraries — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.
> **Model:** Spawn all subagents with `model: haiku` to minimise token cost. Each task is self-contained so Haiku has sufficient context.
> **Subagent instructions:** Each subagent must invoke the `caveman` skill at session start and run at `/effort medium`.

**Goal:** Modernise the entire JS build stack to Node 24, Vite 6, and pnpm with install-script lockdown. Remove Cypress and bump Alpine, htmx, and Biome to their latest versions.

**Architecture:** JS/pnpm-only changes. No PHP, CSS, or Tailwind changes. `vite.config.js` structure is unchanged — `laravel-vite-plugin` 1.x is API-compatible with pnpm. htmx 2 requires no changes in this codebase (only `htmx.config.withCredentials` and CSRF headers are used, both unchanged in v2).

**Tech Stack:** Node 24, pnpm 9.x, Vite 6, laravel-vite-plugin 1.x, Alpine.js 3.x, htmx 2.x, Biome 1.9.x, Sass 1.80.x

> ⚠️ **Review gate:** Do NOT commit at any point without first presenting the diff to the user and receiving explicit approval.

---

### Task 1: Switch to Node 24 and install pnpm

**Files:**
- Create: `.nvmrc`

- [ ] **Step 1: Switch to Node 24 in your environment**

```bash
nvm install 24 && nvm use 24
# or: fnm install 24 && fnm use 24
```

- [ ] **Step 2: Verify Node version**

```bash
node --version
```

Expected: `v24.x.x`

- [ ] **Step 3: Pin the project to Node 24**

Create `.nvmrc` at the project root:

```
24
```

- [ ] **Step 4: Install pnpm globally**

```bash
npm install -g pnpm@9
pnpm --version
```

Expected: `9.x.x`

---

### Task 2: Update package.json

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Update devDependencies**

Replace the `devDependencies` block with:

```json
"devDependencies": {
    "@biomejs/biome": "^1.9.0",
    "alpinejs": "^3.14.0",
    "autoprefixer": "^10.4.16",
    "daisyui": "^4.4.24",
    "htmx.org": "^2.0.0",
    "laravel-vite-plugin": "^1.0.0",
    "postcss": "^8.4.32",
    "sass": "^1.80.0",
    "tailwindcss": "^3.4.0",
    "vite": "^6.0.0"
}
```

Notes:
- `cypress` removed entirely
- `daisyui` kept at `^4.4.24` — bumped in Step 4
- `tailwindcss` kept at `^3.4.0` — upgraded in Step 4
- `autoprefixer` kept — removed in Step 4 by `@tailwindcss/upgrade`

- [ ] **Step 2: Remove the test:cypress script and update scripts to use pnpm**

Replace the `scripts` section with:

```json
"scripts": {
    "dev": "vite",
    "build": "vite build",
    "lf": "biome check --apply . --verbose --log-level=info --log-kind=compact",
    "check": "biome check . --verbose --log-level=info --log-kind=compact",
    "format": "biome format . --write --verbose --log-level=info --log-kind=compact",
    "lint": "biome lint . --verbose --log-level=info --log-kind=compact"
}
```

- [ ] **Step 3: Remove the dependencies block**

Delete the `dependencies` block entirely (it only contained `graceful-fs`, a Cypress transitive dep).

- [ ] **Step 4: Add packageManager field and pnpm security config**

Add these two fields to `package.json`:

```json
"packageManager": "pnpm@9",
"pnpm": {
    "onlyBuiltDependencies": ["@biomejs/biome", "esbuild", "sass"]
}
```

`onlyBuiltDependencies` whitelists only these three packages to run install scripts. All other packages are blocked from running postinstall/preinstall scripts, preventing supply-chain script attacks.

---

### Task 3: Install dependencies with pnpm

- [ ] **Step 1: Remove old npm artifacts**

```bash
rm -rf node_modules package-lock.json
```

- [ ] **Step 2: Install with pnpm**

```bash
cd /Volumes/CaseSensitive/src/fantapronostico && pnpm install 2>&1 | tee /tmp/pnpm-step3.log
```

- [ ] **Step 3: Check for errors**

```bash
grep -E "ERR|WARN|Cannot resolve|Unmet peer" /tmp/pnpm-step3.log | grep -v "deprecated" | head -20
```

Expected: no errors. Deprecation warnings are acceptable.

- [ ] **Step 4: Update composer.json deploy script**

In `composer.json`, find the `deploy` script that runs `npm run build` and update it to use pnpm:

```json
"deploy": [
    "git pull",
    "composer install --no-dev --optimize-autoloader -q",
    "php artisan migrate --force",
    "php artisan optimize",
    "pnpm run build",
    "chown -R www-data:www-data storage/ bootstrap/cache/"
]
```

---

### Task 4: Verify htmx 2 compatibility

- [ ] **Step 1: Confirm no blade-level htmx attributes exist**

```bash
grep -rn "hx-" /Volumes/CaseSensitive/src/fantapronostico/app/Modules --include="*.blade.php" | wc -l
grep -rn "hx-" /Volumes/CaseSensitive/src/fantapronostico/resources/views --include="*.blade.php" | wc -l
```

Expected: both return `0`. If non-zero, list the matches and check for htmx 1-only attributes (`hx-vars`, `hx-on="..."` old syntax). If found, apply these renames:
- `hx-vars` → `hx-vals`
- `hx-on="htmx:eventName: ..."` → `hx-on:htmx:event-name="..."`

- [ ] **Step 2: Confirm JS-side htmx usage is compatible**

`resources/js/app.js` uses:
- `htmx.config.withCredentials = true` — unchanged in htmx 2 ✓
- `htmx:configRequest` event listener — unchanged in htmx 2 ✓

No changes needed in `app.js`.

---

### Task 5: Build, dev server, and Biome check

- [ ] **Step 1: Run production build**

```bash
cd /Volumes/CaseSensitive/src/fantapronostico && pnpm run build 2>&1 | tee /tmp/build-step3.log
grep -E "error|Error|failed|✓ built" /tmp/build-step3.log | tail -20
```

Expected: `✓ built in Xs` with no errors.

- [ ] **Step 2: Run dev server**

```bash
pnpm run dev
```

Expected: Vite starts, shows `VITE v6.x.x ready`.

- [ ] **Step 3: Run Biome check**

```bash
cd /Volumes/CaseSensitive/src/fantapronostico && pnpm run check 2>&1 | tee /tmp/biome-step3.log
grep -E "error|✖|diagnostics" /tmp/biome-step3.log | head -30
```

If issues found, fix them:

```bash
pnpm run lf
pnpm run check 2>&1 | tail -10
```

---

### Task 6: Review gate — present diff to user before committing

- [ ] **Step 1: Show the diff**

```bash
git -C /Volumes/CaseSensitive/src/fantapronostico diff --stat
git -C /Volumes/CaseSensitive/src/fantapronostico diff package.json composer.json
git -C /Volumes/CaseSensitive/src/fantapronostico status
```

- [ ] **Step 2: Present diff to user and wait for explicit approval before running any `git commit` command.**
