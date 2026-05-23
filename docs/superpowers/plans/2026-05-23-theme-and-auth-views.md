# Theme + Auth Views Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the `fp2024` nord-based theme with coordinated navy+gold `fp-light`/`fp-dark` themes and migrate auth views to DaisyUI v5 class names with a new card-based layout.

**Architecture:** Two DaisyUI v5 themes (fp-light extends corporate, fp-dark extends luxury) share identical gold, error and success values. Alpine's existing theme store is updated to reference the new names and fix a pre-existing toggle bug. Auth views are updated file by file with no structural changes to routing or controllers.

**Tech Stack:** DaisyUI v5.5.20, Tailwind CSS v4.3.0, Alpine.js, Laravel Blade, Sass (via Vite + @tailwindcss/postcss)

---

## File Map

| File | Action | What changes |
|---|---|---|
| `vite.config.js` | Modify | Silence Sass `@import` deprecation |
| `resources/scss/app.scss` | Modify | Replace `fp2024` theme block with `fp-light` + `fp-dark` |
| `resources/js/alpine/theme.js` | Modify | New theme names, fix toggle bug |
| `resources/views/components/layouts/app.blade.php` | Modify | Add fixed theme toggle button |
| `app/Modules/Auth/Views/shared/layout.blade.php` | Modify | Card wrapper, aside hidden on mobile |
| `app/Modules/Auth/Views/shared/form-control.blade.php` | Modify | DaisyUI v5 classes, hidden input shortcut |
| `app/Modules/Auth/Views/shared/form.blade.php` | Modify | Remove `form-control` div, `flex flex-col gap-4` |
| `app/Modules/Auth/Views/shared/nav.blade.php` | Modify | Underline tab nav replacing `tabs-bordered` |
| `app/Modules/Auth/Views/shared/tab.blade.php` | Modify | Active/inactive Tailwind classes |
| `app/Modules/Auth/Views/shared/league.blade.php` | Modify | Remove `select-bordered` |
| `app/Modules/Auth/Views/shared/logout.blade.php` | Modify | `btn-base-300` → `btn btn-ghost` |

---

### Task 1: Silence Sass @import deprecation in Vite

**Files:**
- Modify: `vite.config.js`

- [ ] **Step 1: Update vite.config.js**

Replace the entire file content with:

```js
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
	plugins: [
		laravel({
			input: ["resources/scss/app.scss", "resources/js/app.js"],
			refresh: ["./resources", "./app/Modules/**/Views"],
		}),
	],
	css: {
		preprocessorOptions: {
			scss: {
				silenceDeprecations: ["import"],
			},
		},
	},
});
```

- [ ] **Step 2: Verify no build errors**

```bash
npm run build > /tmp/build.log 2>&1 && echo "OK" || grep -i "error" /tmp/build.log | head -10
```

Expected: `OK` with no errors.

- [ ] **Step 3: Commit**

```bash
git add vite.config.js
git commit -m "fix: silence sass @import deprecation warning in vite"
```

---

### Task 2: Replace fp2024 theme with fp-light and fp-dark in app.scss

**Files:**
- Modify: `resources/scss/app.scss`

- [ ] **Step 1: Replace the DaisyUI plugin block and theme definition**

The file currently starts with:
```scss
@import "tailwindcss";
@plugin "daisyui" {
  themes: nord, cupcake, fp2024 --default;
}

@plugin "daisyui/theme" {
  name: fp2024;
  ...
}
```

Replace those two blocks (keep everything after `// my style`) with:

```scss
@import "tailwindcss";
@plugin "daisyui" {
  themes: corporate, luxury, fp-light --default, fp-dark;
}

@plugin "daisyui/theme" {
  name: fp-light;
  extends: corporate;
  default: true;
  color-scheme: light;

  --color-base-100: oklch(97% 0.008 240);
  --color-base-200: oklch(93% 0.01 240);
  --color-base-300: oklch(90% 0.012 240);
  --color-base-content: oklch(20% 0.065 245);
  --color-primary: oklch(28% 0.085 245);
  --color-primary-content: oklch(97% 0.03 90);
  --color-secondary: oklch(76% 0.165 85);
  --color-secondary-content: oklch(20% 0.065 245);
  --color-accent: oklch(76% 0.165 85);
  --color-accent-content: oklch(20% 0.065 245);
  --color-error: oklch(53% 0.19 20);
  --color-success: oklch(56% 0.17 145);
  --radius-field: 0.5rem;
  --radius-box: 0.75rem;
}

@plugin "daisyui/theme" {
  name: fp-dark;
  extends: luxury;
  color-scheme: dark;

  --color-base-100: oklch(13% 0.055 245);
  --color-base-200: oklch(20% 0.065 245);
  --color-base-300: oklch(28% 0.06 245);
  --color-base-content: oklch(95% 0.008 240);
  --color-primary: oklch(76% 0.165 85);
  --color-primary-content: oklch(20% 0.065 245);
  --color-secondary: oklch(28% 0.06 245);
  --color-secondary-content: oklch(95% 0.008 240);
  --color-accent: oklch(76% 0.165 85);
  --color-accent-content: oklch(20% 0.065 245);
  --color-error: oklch(53% 0.19 20);
  --color-success: oklch(56% 0.17 145);
  --radius-field: 0.5rem;
  --radius-box: 0.75rem;
}

// my style
@import 'fireworks';
```

Keep the rest of the file (`$titleFont`, `$bodyFont`, `body`, `.fp2024`, etc.) unchanged.

- [ ] **Step 2: Verify build**

```bash
npm run build > /tmp/build.log 2>&1 && echo "OK" || grep -i "error" /tmp/build.log | head -10
```

Expected: `OK`.

- [ ] **Step 3: Commit**

```bash
git add resources/scss/app.scss
git commit -m "feat: replace fp2024 theme with fp-light/fp-dark navy+gold themes"
```

---

### Task 3: Update Alpine theme store

**Files:**
- Modify: `resources/js/alpine/theme.js`

The current file has two bugs: `mode` defaults to `"cupcake"`, `themes.light` and `themes.dark` both point to `"nord"`, and `toggle()` has a typo (`"ligh"` instead of `"light"`).

- [ ] **Step 1: Replace theme.js**

```js
export default {
	mode: "fp-light",

	themes: {
		light: "fp-light",
		dark: "fp-dark",
	},

	toggle() {
		this.mode = this.mode === this.themes.dark ? this.themes.light : this.themes.dark;
	},

	isDarkMode() {
		return this.mode === this.themes.dark;
	},

	getIcon() {
		return this.mode === this.themes.dark ? "☽" : "☼";
	},

	init() {
		this.mode = this.themes[
			window.matchMedia?.("(prefers-color-scheme: dark)").matches
				? "dark"
				: "light"
		];
	},
};
```

- [ ] **Step 2: Verify build**

```bash
npm run build > /tmp/build.log 2>&1 && echo "OK" || grep -i "error" /tmp/build.log | head -10
```

Expected: `OK`.

- [ ] **Step 3: Commit**

```bash
git add resources/js/alpine/theme.js
git commit -m "fix: update alpine theme store to fp-light/fp-dark, fix toggle typo"
```

---

### Task 4: Add theme toggle button to main layout

**Files:**
- Modify: `resources/views/components/layouts/app.blade.php`

- [ ] **Step 1: Add fixed toggle button before the closing `</body>`**

Add this block immediately after `<body class="bg-base-300 min-h-screen">`:

```html
<div class="fixed top-4 right-4 z-50" x-data>
    <button
        @click="$store.theme.toggle()"
        class="btn btn-ghost btn-sm btn-circle"
        :aria-label="$store.theme.isDarkMode() ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <span x-show="!$store.theme.isDarkMode()">☽</span>
        <span x-show="$store.theme.isDarkMode()">☼</span>
    </button>
</div>
```

- [ ] **Step 2: Start dev server and verify toggle works**

```bash
npm run dev
```

Open the app in a browser, click the toggle button, and verify `data-theme` on `<html>` switches between `fp-light` and `fp-dark` in devtools. The page colors should shift noticeably.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/layouts/app.blade.php
git commit -m "feat: add fixed theme toggle button to main layout"
```

---

### Task 5: Update auth layout — card wrapper, remove aside on mobile

**Files:**
- Modify: `app/Modules/Auth/Views/shared/layout.blade.php`

- [ ] **Step 1: Replace the layout**

```html
<main class="flex items-center justify-center min-h-screen">
    <aside class="hidden lg:block lg:basis-3/5 xl:basis-1/2 overflow-hidden self-stretch">
        <img
            class="object-cover object-center w-full h-full xl:object-top"
            src="{{Vite::asset('resources/assets/images/football_player.png')}}"
            alt="Draw of a football player cheering with a cup"
        >
    </aside>
    <section class="w-full flex flex-col justify-center items-center min-h-screen px-6 lg:basis-2/5 xl:basis-1/2">
        <x-partials.logo.large />
        <div class="card bg-base-100 shadow-lg w-full max-w-sm mt-6">
            <div class="card-body">
                {{$slot}}
            </div>
        </div>
    </section>
</main>
```

- [ ] **Step 2: Verify visually**

Open `/login` in the browser. On desktop: image fills the left half, card is on the right with a visible shadow. On mobile (devtools): image gone, card takes full width with horizontal padding.

- [ ] **Step 3: Commit**

```bash
git add app/Modules/Auth/Views/shared/layout.blade.php
git commit -m "feat: auth layout — card wrapper, image hidden on mobile"
```

---

### Task 6: Update form-control component

**Files:**
- Modify: `app/Modules/Auth/Views/shared/form-control.blade.php`

- [ ] **Step 1: Replace the component**

```html
@if(!empty($hidden ?? null))
    <input
        id="{{$name}}"
        name="{{$name}}"
        type="{{$type}}"
        class="hidden"
        value="{{old($name, $value ?? null)}}"
    />
@else
    <div class="flex flex-col gap-1">
        <label for="{{$name}}" class="text-sm font-medium text-base-content/70">{{$label ?? ''}}</label>
        <input
            id="{{$name}}"
            name="{{$name}}"
            type="{{$type}}"
            placeholder="{{!empty($placeholder ?? null) ? $placeholder : null}}"
            class="input w-full border-base-content/20 @error($name) border-error @enderror"
            @checked(!empty($checked ?? null) && $type === 'checkbox')
            @if('password' !== $type) value="{{old($name, $value ?? null)}}" @endif
        />
        @error($name)
            @foreach($errors->get($name) as $error)
                <span class="text-error text-xs mt-1">{{$error}}</span>
            @endforeach
        @enderror
    </div>
@endif
```

Note: `required` is intentionally removed from the input — it was on all inputs including hidden ones. Add it back per-form if needed.

- [ ] **Step 2: Verify visually**

Open `/login`. The email and password fields should have visible labels above them, a clean input with a subtle border, and no broken layout.

- [ ] **Step 3: Commit**

```bash
git add app/Modules/Auth/Views/shared/form-control.blade.php
git commit -m "fix: migrate form-control to daisyui v5, handle hidden inputs separately"
```

---

### Task 7: Update form component

**Files:**
- Modify: `app/Modules/Auth/Views/shared/form.blade.php`

- [ ] **Step 1: Replace the component**

```html
<form action="{{$action}}" method="{{$method}}">
    @method($method)
    @csrf
    <div class="flex flex-col gap-4">
        @foreach($formControls as $formControl)
            <x-auth::shared.form-control
                :name="$formControl['name']"
                :type="$formControl['type']"
                :placeholder="!empty($formControl['placeholder'] ?? null) ? $formControl['placeholder'] : null"
                :prefix="$prefix"
                :value="$formControl['value'] ?? null"
                :checked="!empty($formControl['checked'] ?? null)"
                :hidden="!empty($formControl['hidden'])"
            />
        @endforeach
        @if(isset($btnText) && ! isset($btn))
            <div class="flex flex-col mt-2">
                @if($passwordReset ?? false)
                    <a
                        href="{{route('password.email')}}"
                        class="text-xs text-right text-base-content/50 hover:text-primary pb-2"
                    >{{__('auth.login.request_password_reset')}}</a>
                @endif
                <button class="btn btn-primary w-full fp2024-title">{{$btnText}}</button>
            </div>
        @else
            {{$btn}}
        @endif
    </div>
</form>
```

- [ ] **Step 2: Verify visually**

Open `/login` and `/register`. Inputs should be stacked with consistent spacing. The submit button should be full width, navy in light mode, gold in dark mode.

- [ ] **Step 3: Commit**

```bash
git add app/Modules/Auth/Views/shared/form.blade.php
git commit -m "fix: remove form-control wrapper, use flex gap for form layout"
```

---

### Task 8: Update nav component — underline tab nav

**Files:**
- Modify: `app/Modules/Auth/Views/shared/nav.blade.php`

- [ ] **Step 1: Replace the component**

```html
<div class="w-full">
    <div class="flex border-b border-base-300">
        <x-auth::shared.tab name="login" text="{{__('auth.login.nav')}}"/>
        <x-auth::shared.tab name="register" text="{{__('auth.register.nav')}}"/>
    </div>
    {{$slot}}
</div>
```

- [ ] **Step 2: Commit**

```bash
git add app/Modules/Auth/Views/shared/nav.blade.php
git commit -m "fix: replace tabs-bordered with underline tab nav for daisyui v5"
```

---

### Task 9: Update tab component — active/inactive classes

**Files:**
- Modify: `app/Modules/Auth/Views/shared/tab.blade.php`

- [ ] **Step 1: Replace the component**

```html
<a
    href="{{route($name)}}"
    role="tab"
    @class([
        'flex-1 text-center py-3 text-base transition-colors',
        'border-b-2 border-primary text-primary font-semibold -mb-px' => Route::currentRouteName() === $name,
        'text-base-content/50 hover:text-base-content/80' => Route::currentRouteName() !== $name,
    ])
>
    {{$text}}
</a>
```

- [ ] **Step 2: Verify visually**

Open `/login`. The Login tab should have a navy underline (light mode) or gold underline (dark mode) that connects to the card border. Register tab should be muted. Click Register to verify the active state swaps.

- [ ] **Step 3: Commit**

```bash
git add app/Modules/Auth/Views/shared/tab.blade.php
git commit -m "fix: update tab active/inactive classes for daisyui v5"
```

---

### Task 10: Update league form — remove select-bordered

**Files:**
- Modify: `app/Modules/Auth/Views/shared/league.blade.php`

- [ ] **Step 1: Replace the select class**

Change:
```html
<select class="select select-bordered w-full max-w-xs bg-white mt-6" name="league_id" id="league_id">
```

To:
```html
<select class="select w-full max-w-xs mt-6" name="league_id" id="league_id">
```

(`bg-white` removed — use theme base colors instead of hardcoded white)

- [ ] **Step 2: Commit**

```bash
git add app/Modules/Auth/Views/shared/league.blade.php
git commit -m "fix: remove select-bordered (removed in daisyui v5)"
```

---

### Task 11: Update logout modal — fix btn-base-300

**Files:**
- Modify: `app/Modules/Auth/Views/logout.blade.php`

- [ ] **Step 1: Replace the close button class**

Change:
```html
<button class="btn btn-base-300">Chiudi</button>
```

To:
```html
<button class="btn btn-ghost">Chiudi</button>
```

- [ ] **Step 2: Verify visually**

Open the logout modal. The Logout button should be red (`btn-error`), the Chiudi button should be a subtle ghost button.

- [ ] **Step 3: Final build verification**

```bash
npm run build > /tmp/build.log 2>&1 && echo "OK" || grep -i "error" /tmp/build.log | head -10
```

Expected: `OK`.

- [ ] **Step 4: Commit**

```bash
git add app/Modules/Auth/Views/logout.blade.php
git commit -m "fix: replace btn-base-300 with btn-ghost (removed in daisyui v5)"
```