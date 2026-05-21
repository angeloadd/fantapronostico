# Full Stack Upgrade Design

**Date:** 2026-05-21  
**Status:** Approved  
**Scope:** PHP 8.5 · Laravel 13 · Filament 4 · Node 24 · Vite 6 · Tailwind 4 · DaisyUI 5

---

## Context

- App is not in production — breakage is acceptable, correctness over caution
- No test suite yet (planned separately after this upgrade)
- Success criteria per step: `php artisan` runs, app boots, pages render
- Filament admin panel correctness is deferred — just get it to resolve

---

## Approach: 4 isolated steps

Each step is a self-contained session. Each has a single domain, its own done criteria, and can be committed independently.

---

## Step 1 — PHP 8.5 + Tooling

**Domain:** PHP runtime and dev toolchain only. No framework changes.

### composer.json changes

| Package | From | To |
|---|---|---|
| `php` | `^8.2` | `^8.5` |
| `larastan/larastan` | `^2.9` | `^3.0` |
| `barryvdh/laravel-ide-helper` | `^3.0` | `^3.x` latest |
| `laravel/pint` | `^1.13` | `^1.x` latest |
| `phpunit/phpunit` | `^10.5` | `^11.0` |
| `nunomaduro/collision` | `^8.0` | `^8.x` latest |
| `spatie/laravel-ignition` | `^2.4` | `^2.x` latest |

### Process

1. Update `composer.json` constraints as above
2. Run `composer update`
3. Resolve any package conflicts (known risk: `irazasyed/telegram-bot-sdk` and `resend/resend-laravel` may not declare PHP 8.5 support yet — use `platform` override in `composer.json` if needed as a temporary unblock)
4. Update `phpunit.xml` if PHPUnit 11 requires config schema changes
5. Run `php artisan ide-helper:all` to regenerate helpers
6. Run `vendor/bin/phpstan analyse --memory-limit 1G` and fix all reported issues
7. Run `vendor/bin/pint` to fix any style issues

### Done when

- `php artisan about` shows PHP 8.5
- `vendor/bin/pint --test` exits clean
- `vendor/bin/phpstan analyse` exits clean
- App boots and pages render

---

## Step 2 — Laravel 11 → 13

**Domain:** PHP/Laravel framework and ecosystem packages only. No JS changes.

### composer.json changes

| Package | From | To |
|---|---|---|
| `laravel/framework` | `^11.7` | `^13.0` |
| `laravel/tinker` | `^2.9` | `^2.10` |
| `laravel/fortify` | `^1.21` | `^1.25` |
| `filament/filament` | `^3.2` | `^4.0` |
| `resend/resend-laravel` | `^0.11.0` | latest `^0.x` |
| `irazasyed/telegram-bot-sdk` | `^3.14` | latest `^3.x` |
| Remove | `larastan/larastan` branch alias `11.x-dev` | update `extra.branch-alias` to `13.x-dev` |

Also remove `laracast/cypress` from `require-dev` (Cypress is being dropped in step 3).

### Process

1. Update `composer.json` constraints
2. Run `composer update`
3. Diff `bootstrap/app.php` and `config/` against the Laravel 13 skeleton — apply any structural changes
4. Run `php artisan migrate` (framework migrations)
5. Run `php artisan optimize:clear`
6. Verify `php artisan about` shows Laravel 13.x

### Key breaking change: cache/session prefix

Laravel 13 changes the auto-generated prefix format:

```
# Laravel 11/12
APP_NAME_cache_   →   APP_NAME-cache-
APP_NAME_session  →   APP_NAME-session
```

Not relevant for this app (not in prod, no persistent cache to preserve), but noted for awareness.

### Filament notes

- Bump to `^4.0` and let composer resolve
- `app/Providers/Filament/` and `app/Filament/` may need minimal changes to boot without errors
- Do **not** invest time verifying the Filament admin panel UI — that is a separate future task

### Done when

- `php artisan about` shows Laravel 13.x
- `php artisan serve` starts without errors
- App boots and pages render (Filament panel may be broken — acceptable)

---

## Step 3 — Node 24 + Vite 6 + JS Libraries

**Domain:** JS build toolchain and runtime libraries only. No CSS/Tailwind changes.

### package.json changes

| Package | From | To | Notes |
|---|---|---|---|
| `vite` | `^4.0.0` | `^6.0` | |
| `laravel-vite-plugin` | `^0.8.0` | `^1.0` | Must match Vite 6 |
| `alpinejs` | `^3.13.5` | `^3.14` | No breaking changes |
| `htmx.org` | `^1.9.10` | `^2.0` | Breaking changes — see below |
| `@biomejs/biome` | `1.4.1` | `^1.9` | |
| `sass` | `^1.71.1` | `^1.80` | |
| `autoprefixer` | `^10.4.16` | keep | Removed in step 4 by `@tailwindcss/upgrade` — do not touch here |
| Remove | `cypress` | — | No longer used |

Also remove the `test:cypress` script from `package.json`.

### vite.config.js

No structural changes needed — `laravel-vite-plugin` 1.x is API-compatible with this config.

### htmx 2 migration

htmx 2 has mechanical breaking changes. Strategy to keep token cost low:

1. **Inventory:** `grep -rn "hx-"` scoped to `*.blade.php` in `app/Modules/**/Views/` and `resources/views/`
2. **Mechanical renames** via Serena `replace_content` (regex, no file reads needed):
   - `hx-vars` → `hx-vals`
   - Event names in `hx-on` handlers: camelCase → kebab-case (e.g. `htmx:afterRequest` → `htmx:after-request`)
   - `hx-on="event: ..."` → `hx-on:event="..."`
3. **Read only if needed:** if grep reveals non-mechanical usage, use Serena scoped to that specific file

`htmx.config.withCredentials` and the CSRF header setup in `app.js` are unchanged in htmx 2.

### Done when

- `npm run build` exits clean
- `npm run dev` starts without errors
- Pages load with no JS console errors
- Alpine components and htmx interactions work

---

## Step 4 — Tailwind 4 + DaisyUI 5

**Domain:** CSS pipeline, configuration format, and custom theme. No PHP changes.

### Automated migration

Run the official upgrade tool first:

```bash
npx @tailwindcss/upgrade
```

This handles:
- Updating `tailwindcss` to v4 and adding `@tailwindcss/vite`
- Converting `tailwind.config.js` content paths to CSS `@source` directives
- Replacing `@tailwind base/components/utilities` directives in templates
- Removing `postcss-import` and `autoprefixer` (built into v4)

### Manual: `app.scss` rework

Replace Tailwind directives:

```scss
// Before
@tailwind base;
@tailwind components;
@tailwind utilities;

// After
@import "tailwindcss";
```

Sass variables, `.fp2024` classes, and all custom styles are untouched.

### Manual: `fp2024` custom theme rewrite

The current theme uses a JS spread over the DaisyUI nord theme — that API is gone in DaisyUI 5. Rewrite as a CSS plugin block. All explicit colour values are already defined in `tailwind.config.js`, so this is a format change, not a values lookup.

```css
/* Before (tailwind.config.js — deleted) */
daisyui: {
  themes: ["nord", { fp2024: { ...themes.nord, primary: "#00AACC", ... } }]
}

/* After (in app.scss or a dedicated theme file) */
@plugin "daisyui/theme" {
  name: fp2024;
  default: true;
  color-scheme: light;
  --color-primary: #00AACC;
  --color-secondary: #01B359;
  --color-accent: #00849E;
  --color-neutral: #13525F;
  --color-info: #14B0D0;
  --color-success: #01DB6E;
  /* nord base colours carried over explicitly */
}
```

### CSS variable audit

DaisyUI 5 renames CSS variables (e.g. `--p` → `--color-primary`). Grep blade files for `var(--` to find any direct variable usage and update to new names.

### `tailwind.config.js`

Deleted entirely — all config lives in CSS after the upgrade.

### Done when

- `npm run build` exits clean
- Pages render with correct fp2024 colours (primary teal, secondary green)
- No visual regressions on main layouts
- `tailwind.config.js` is gone

---

## Deferred

- **Filament admin panel** — correctness, UI verification, Filament 4 migration guide
- **Test suite** — to be added as a separate project after the upgrade is complete