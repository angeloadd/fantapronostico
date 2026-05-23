# Theme Design — Navy + Gold Sports Palette

**Date:** 2026-05-23
**Scope:** `resources/scss/app.scss`, `resources/views/components/layouts/app.blade.php`, Alpine theme store

## Context

The current `fp2024` theme extends `nord` and has a cool blue-gray developer-tool feel that doesn't suit a sports prediction app. This spec replaces it with two coordinated themes (`fp-light` / `fp-dark`) derived from the GoalCast design system color philosophy: **navy primary + gold accent**, with gold flipping to primary in dark mode for visibility.

Color values are sourced from `docs/proposal-design.md` and converted to OKLCH.

## Theme Names

| Name | Base | Mode |
|---|---|---|
| `fp-light` | `corporate` | Light (default) |
| `fp-dark` | `luxury` | Dark |

The existing `fp2024` theme is removed and replaced by these two.

## Color Tokens

### `fp-light` (extends `corporate`)

| Token | Value | Source |
|---|---|---|
| `--color-base-100` | `oklch(97% 0.008 240)` | background |
| `--color-base-200` | `oklch(93% 0.01 240)` | secondary |
| `--color-base-300` | `oklch(90% 0.012 240)` | border |
| `--color-base-content` | `oklch(20% 0.065 245)` | foreground |
| `--color-primary` | `oklch(28% 0.085 245)` | primary (navy) |
| `--color-primary-content` | `oklch(97% 0.03 90)` | primary-foreground |
| `--color-secondary` | `oklch(76% 0.165 85)` | accent (gold) |
| `--color-secondary-content` | `oklch(20% 0.065 245)` | accent-foreground |
| `--color-accent` | `oklch(76% 0.165 85)` | accent (gold) |
| `--color-accent-content` | `oklch(20% 0.065 245)` | accent-foreground |
| `--color-error` | `oklch(53% 0.19 20)` | destructive |
| `--color-success` | `oklch(56% 0.17 145)` | success |
| `--radius-field` | `0.5rem` | radius |
| `--radius-box` | `0.75rem` | radius |

### `fp-dark` (extends `luxury`)

| Token | Value | Source |
|---|---|---|
| `--color-base-100` | `oklch(13% 0.055 245)` | dark background |
| `--color-base-200` | `oklch(20% 0.065 245)` | dark card |
| `--color-base-300` | `oklch(28% 0.06 245)` | dark secondary |
| `--color-base-content` | `oklch(95% 0.008 240)` | dark foreground |
| `--color-primary` | `oklch(76% 0.165 85)` | gold (flips to primary) |
| `--color-primary-content` | `oklch(20% 0.065 245)` | dark text on gold |
| `--color-secondary` | `oklch(28% 0.06 245)` | muted navy surface |
| `--color-secondary-content` | `oklch(95% 0.008 240)` | light text |
| `--color-accent` | `oklch(76% 0.165 85)` | gold (consistent) |
| `--color-accent-content` | `oklch(20% 0.065 245)` | dark text on gold |
| `--color-error` | `oklch(53% 0.19 20)` | destructive (shared) |
| `--color-success` | `oklch(56% 0.17 145)` | success (shared) |
| `--radius-field` | `0.5rem` | radius |
| `--radius-box` | `0.75rem` | radius |

Gold (`oklch(76% 0.165 85)`), error and success are identical across both themes for visual continuity.

## app.scss Changes

1. Remove `@plugin "daisyui/theme"` block for `fp2024`
2. Add `@plugin "daisyui"` with `themes: corporate, luxury, fp-light, fp-dark --default`
3. Add two `@plugin "daisyui/theme"` blocks — one for `fp-light`, one for `fp-dark`
4. Remove the old custom color variable overrides

## Toggle Mechanism

The layout already has `x-bind:data-theme="$store.theme.mode"` on `<html>`. No structural changes needed.

### Alpine Store (`resources/js/app.js`)

```js
Alpine.store('theme', {
    mode: 'fp-light',
    toggle() {
        this.mode = this.mode === 'fp-light' ? 'fp-dark' : 'fp-light'
    }
})
```

No `localStorage` — resets to `fp-light` on every page load. Storage can be added later by persisting `mode` in `init()`.

### Toggle Button

A sun/moon icon button placed in the app layout (e.g. top-right corner or navbar) calling `$store.theme.toggle()`. Uses a conditional icon:

```html
<button @click="$store.theme.toggle()" class="btn btn-ghost btn-sm">
    <template x-if="$store.theme.mode === 'fp-light'">
        <!-- moon icon -->
    </template>
    <template x-if="$store.theme.mode === 'fp-dark'">
        <!-- sun icon -->
    </template>
</button>
```

## Impact on Auth Views

The auth views spec (`2026-05-23-auth-views-daisyui5-design.md`) references `fp2024` implicitly via theme variables. No changes needed there — the card and form classes use semantic tokens (`bg-base-100`, `btn-primary`, etc.) that map correctly to both themes.

## Files Affected

| File | Change |
|---|---|
| `resources/scss/app.scss` | Replace `fp2024` theme block with `fp-light` + `fp-dark` blocks |
| `resources/js/app.js` | Update Alpine store default from `fp2024` to `fp-light`, add `toggle()` |
| `resources/views/components/layouts/app.blade.php` | Add toggle button |