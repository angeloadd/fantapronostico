# Step 1: PHP 8.5 + Tooling — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.
> **Model:** Spawn all subagents with `model: haiku` to minimise token cost. Each task is self-contained so Haiku has sufficient context.

**Goal:** Run the entire app on PHP 8.5 with PHPUnit 11, current dev tooling, and a clean PHPStan + Pint result.

**Architecture:** PHP-only changes. No framework, JS, or CSS changes. Composer resolves updated constraints; IDE helpers are regenerated; static analysis and code style are fixed to baseline.

**Tech Stack:** PHP 8.5, Composer, PHPUnit 11, Larastan 2.x, Laravel Pint, barryvdh/laravel-ide-helper

> ⚠️ **Review gate:** Do NOT commit at any point without first presenting the diff to the user and receiving explicit approval.

---

### Task 1: Bump PHP and PHPUnit constraints in composer.json

**Files:**
- Modify: `composer.json`

- [ ] **Step 1: Update `php` and `phpunit` constraints**

In `composer.json`, change the following two entries:

```json
"require": {
    "php": "^8.5",
    ...
},
"require-dev": {
    ...
    "phpunit/phpunit": "^11.0",
    ...
}
```

Also update the branch alias in `extra`:
```json
"extra": {
    "branch-alias": {
        "dev-master": "11.x-dev"
    },
```
Leave at `11.x-dev` — this reflects Laravel 11, not PHP. Do not change yet.

- [ ] **Step 2: Run composer update**

```bash
composer update --with-all-dependencies 2>&1 | tee /tmp/composer-step1.log
```

- [ ] **Step 3: Verify resolution or fix conflicts**

```bash
grep -E "Problem|Your requirements|could not be resolved|does not match" /tmp/composer-step1.log | head -30
```

Expected: no output (clean resolve).

If `irazasyed/telegram-bot-sdk` or `resend/resend-laravel` fail due to PHP platform mismatch, add a temporary platform override to `composer.json` and re-run:

```json
"config": {
    ...
    "platform": {
        "php": "8.4.99"
    }
}
```

Remove this override once the packages release PHP 8.5 compatible versions.

- [ ] **Step 4: Confirm PHP 8.5 is active**

```bash
php -v
```

Expected output starts with: `PHP 8.5.`

If not on 8.5 locally, switch via your version manager (e.g. `brew link php@8.5 --force` or `phpenv local 8.5`).

---

### Task 2: Update phpunit.xml for PHPUnit 11

**Files:**
- Modify: `phpunit.xml`

- [ ] **Step 1: Update the schema location**

In `phpunit.xml`, change the `xsi:noNamespaceSchemaLocation` attribute:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
```

- [ ] **Step 2: Verify PHPUnit 11 accepts the config**

```bash
vendor/bin/phpunit --version
```

Expected: `PHPUnit 11.x.x`

```bash
vendor/bin/phpunit --list-suites
```

Expected: lists `Unit` and `Feature` suites with no errors.

---

### Task 3: Regenerate IDE helper files

**Files:**
- Regenerated: `_ide_helper.php`, `_ide_helper_models.php`, `.phpstorm.meta.php` (all gitignored)

- [ ] **Step 1: Run the full IDE helper generation**

```bash
php artisan ide-helper:generate && \
php artisan ide-helper:eloquent && \
php artisan ide-helper:meta && \
php artisan ide-helper:models --write 2>&1 | tee /tmp/idehelper.log
```

- [ ] **Step 2: Check for errors**

```bash
grep -i "error\|exception\|failed" /tmp/idehelper.log | head -20
```

Expected: no errors. Warnings about missing classes are acceptable.

---

### Task 4: Run PHPStan and fix all issues

**Files:**
- Modify: any PHP files flagged by PHPStan
- Modify: `phpstan.neon` (if suppressions need updating)

- [ ] **Step 1: Run PHPStan at level 9**

```bash
vendor/bin/phpstan analyse --memory-limit 1G 2>&1 | tee /tmp/phpstan-step1.log
```

- [ ] **Step 2: Review errors**

```bash
cat /tmp/phpstan-step1.log | tail -60
```

- [ ] **Step 3: Fix each reported error**

Fix errors directly in the reported PHP files using Serena `replace_content` for surgical edits. Common PHP 8.5 issues:
- Deprecated implicit nullable parameters: `function foo(Type $x = null)` → `function foo(?Type $x = null)`
- Deprecated string interpolation forms: `"$foo->bar"` → `"{$foo->bar}"`

After fixing, re-run:
```bash
vendor/bin/phpstan analyse --memory-limit 1G 2>&1 | tee /tmp/phpstan-step1.log
grep "errors\|No errors" /tmp/phpstan-step1.log | tail -5
```

Expected: `[OK] No errors`

---

### Task 5: Run Pint and fix all style issues

**Files:**
- Modify: any PHP files flagged by Pint

- [ ] **Step 1: Run Pint in fix mode**

```bash
vendor/bin/pint 2>&1 | tee /tmp/pint-step1.log
```

- [ ] **Step 2: Verify clean**

```bash
vendor/bin/pint --test 2>&1 | tail -5
```

Expected: `Found 0 files with coding style issues.`

---

### Task 6: Boot test

- [ ] **Step 1: Clear and re-optimise**

```bash
php artisan optimize:clear
```

- [ ] **Step 2: Confirm artisan works**

```bash
php artisan about
```

Expected: Laravel version line shows `11.x`, PHP line shows `8.5.x`.

- [ ] **Step 3: Start dev server and manually verify pages render**

```bash
php artisan serve
```

Open the app in a browser. Confirm the main page loads without a 500 error.

---

### Task 7: Request review before committing

- [ ] **Step 1: Show the diff**

```bash
git diff --stat
git diff composer.json phpunit.xml
```

- [ ] **Step 2: Present diff to user and wait for explicit approval before running any `git commit` command.**
