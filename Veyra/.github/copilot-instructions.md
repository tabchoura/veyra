<!-- Guidance for AI coding agents working on the Veyra repo -->
# Copilot instructions — Veyra

This file gives focused, actionable context for an AI coding agent to be productive in this repo. Only document discoverable patterns and commands.

**Repo structure**:
- **Backend:** `Veyra/backend/` — Laravel (PHP 8.2, Laravel 12). Key files: `routes/api.php`, `app/Http/Controllers/` (e.g. `AuthController.php`), `app/Models/User.php`, `database/migrations/`, `composer.json`.
- **Frontend:** `Veyra/frontend/` — Vue 3 + Vite. Key files: `frontend/package.json`, `src/`, `index.html`.

**Architecture & API patterns**:
- API routes are defined in `backend/routes/api.php`. Auth routes are under `/api/auth` and admin routes under `/api/admin` (admin routes require `auth:sanctum`).
- Token auth uses Laravel Sanctum tokens — controllers call `$user->createToken('veyra-token')->plainTextToken`. Frontend should send `Authorization: Bearer <token>`.
- Email verification uses `users.email_verification_token` and `users.email_verified`. Password resets go to `password_resets` (tokens are hashed with `Hash::make()` and verified with `Hash::check()`). See `app/Http/Controllers/AuthController.php` for examples.
- File uploads: logos are stored with `$request->file('logo')->store('logos','public')` and exposed via `asset('storage/' . $user->logo_url)`. Ensure `php artisan storage:link` if exposing uploads during testing.

**Conventions & domain details** (documented from code):
- `User` model fields of interest: `user_type` (`SUPER_USER`, `ADMIN`, ...), `status` (expected `approved` to allow login), `dpp_access`, `quota_dpp`, `language` (`fr` or `en`), `tva_number` (company VAT), `email_verification_token`.
- Responses are JSON with `message` and sometimes `error_type` / `status`. Many user-facing strings are in French; preserve language choices and existing custom messages when editing.
- Password reset flow: a random token (64 chars) is generated, stored hashed in `password_resets`, and sent via `ResetPasswordMail` (see `app/Mail/ResetPasswordMail.php`). Verify expiry logic (60 minutes) before allowing password change.

**Developer workflows & commands**:
- Backend (PowerShell example):
```
cd Veyra/backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```
- Useful composer scripts in `backend/composer.json`:
  - `composer setup` — installs deps, copies `.env`, generates key, migrates and builds frontend assets
  - `composer dev` — runs `php artisan serve`, queue listener, pail, and `npm run dev` concurrently
  - `composer test` — runs `php artisan test`
- Frontend:
```
cd Veyra/frontend
npm install
npm run dev
```

**Files to inspect when changing behavior**:
- API endpoints: `backend/routes/api.php`
- Auth flows and email text: `backend/app/Http/Controllers/AuthController.php`
- Reset email: `backend/app/Mail/ResetPasswordMail.php`
- User schema: `backend/app/Models/User.php` and `backend/database/migrations/*create_users_table.php`
- Composer and npm lifecycle: `backend/composer.json`, `frontend/package.json`

**Integration notes & gotchas**:
- Sanctum: tokens are plain-text on creation. When changing auth, update both token creation and middleware usage in routes.
- Password reset tokens are stored hashed — do not store the raw token in DB. Keep the creation (hashed) + Hash::check verification pattern.
- Email sending: some places use `Mail::raw`, others use a `Mailable`. Respect current pattern when editing tests or adding features.
- Many endpoints return French messages. Preserve message language and keys when modifying responses.

**How to modify code safely**:
- Make minimal, focused patches (use `apply_patch` for edits). Update routes in `routes/api.php` if you add endpoints.
- When changing DB fields, update migrations/seeders and consider adding a new migration rather than editing existing ones.
- Run `composer test` and `npm run dev` (frontend) to sanity-check changes. Use `php artisan migrate --force` in CI only.

**Examples (quick lookups)**:
- Auth register/login flows: `backend/app/Http/Controllers/AuthController.php` — shows registration validation, `tva_number` field, email verification sending, and token creation.
- Routes: `backend/routes/api.php` — shows `/auth/*` routes and admin protected routes.

If anything here is unclear or you need extra specifics (CI, env vars, provider config), ask and I will expand the relevant section.
