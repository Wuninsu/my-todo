# TodoFlow — Phased Development Plan

Tracking document for the offline-first Todo app (Laravel 13 + Livewire 4 + Bootstrap 5 + NativePHP Mobile).

_Last updated: 2026-07-11 (Phase 0–4 complete — core CRUD, Todo UX, Admin panel, Profile & account settings)_

## How to use this file
Check items off as they're completed (`[ ]` → `[x]`). Keep phases in order — later phases (sync, mobile) depend on the data model and CRUD flows built in earlier ones.

---

## Current State Snapshot

**Built:**
- Auth scaffolding — [Login.php](app/Livewire/Auth/Login.php), [Register.php](app/Livewire/Auth/Register.php), and [Logout.php](app/Livewire/Actions/Logout.php) (functional; logout was missing entirely until this session — the route was commented out and pointed at a class that didn't exist yet)
- Full offline-first DB schema already migrated: `users`, `devices`, `todo_lists`, `todos`, `tags`, `tag_todos`, `todo_changes`, `sync_logs` — all carrying `uuid`, `version`, `is_synced`, `client_updated_at`, `device_uuid`, `deleted_at_client`
- Eloquent models with relationships for all of the above
- Admin shell — [UserIndex.php](app/Livewire/Main/Admin/UserIndex.php) is functional (search/filter/paginate); `UserEdit`/`UserView` scaffolded
- App shell — layouts, sidebar, header, mobile-nav partials, landing page
- NativePHP Android build scaffolding present under `nativephp/`

**Now built (Phase 0 + Phase 1, this session):**
- [Dashboard.php](app/Livewire/Main/Dashboard.php) is real: lists the authenticated user's todos, quick-add, create/edit modal (title, description, priority, due date, list, comma-separated tags), delete (soft delete), status cycle (`todo` → `doing` → `done`), favorite toggle
- [SidebarLists.php](app/Livewire/Main/SidebarLists.php) — nested Livewire component powering the sidebar: real per-user lists with open-todo counts, create/rename/delete, default-list protection (can't delete, todos reassign to it on delete of another list), cross-component sync via `list-selected`/`filter-selected` Livewire events (no page reload)
- Sidebar filters (Today/Upcoming/Completed/Favorites) and search wired to the same event bus, all handled in `Dashboard::render()`
- Every registered user (via [Register.php](app/Livewire/Auth/Register.php)) and every seeded user (`UserSeeder`) now gets a default "My Tasks" list automatically
- Fixed a latent bug: `Todo::tags()` / `Tag::todos()` used Laravel's default pivot-table guess (`tag_todo`) but the actual migrated table is `tag_todos` — now explicit
- Factories added: `TodoFactory`, `TodoListFactory`, `TagFactory` (plus `uuid`/`version` defaults added to `UserFactory`, which was missing them)
- 16 Pest feature tests covering CRUD, ownership isolation, list deletion reassignment, and a full HTTP route render (layout + sidebar + nested component + dashboard together) — all passing
- **Known limitation:** no browser/visual verification was done — this Windows dev environment has neither `chromium-cli` nor an installed Playwright browser. Verification relied on Pest's real Blade-rendering test pipeline (`$this->get('/dashboard')` renders the actual layout, sidebar, and nested components server-side) rather than a screenshot. Worth an eyeball pass in an actual browser before calling Phase 1 fully done.

**Still not built:**
- Manual drag/reorder via `position` (currently just defaults to 0 — new todos don't get positioned relative to existing ones)
- No sync engine — `todo_changes` and `sync_logs` tables exist but nothing writes or reads them
- No API routes at all (`routes/web.php` has no `routes/api.php` companion)
- No client-side offline storage (still literally a TODO — see Phase 6)
- `app/Models/User.php` / `database/seeders/UserSeeder.php` have pre-existing uncommitted whitespace-only changes from before this session, left untouched

---

## Phase 0 — Housekeeping ✅
_Clear the decks before building on top of the current state._

- [x] ~~Commit or discard the pending `app/Models/User.php` whitespace change~~ — left as-is (harmless, pre-existing, not this session's to resolve)
- [x] Add `database/factories/TodoFactory.php`, `TodoListFactory.php`, `TagFactory.php` (needed for tests going forward)
- [x] Confirm `.env` local DB works end-to-end: `php artisan migrate:fresh --seed`
- [x] Replace the two Pest `ExampleTest.php` stubs with a real smoke test (auth login works, dashboard loads for an authenticated user) — added `DashboardTest.php`, `DashboardRouteTest.php`, `SidebarListsTest.php`; enabled `RefreshDatabase` in `tests/Pest.php` (was commented out)

## Phase 1 — Core Todo CRUD (web, single device, no sync yet) ✅
_Make the app actually do the one thing it's for._

- [x] `TodoList` Livewire component: sidebar list switcher (`SidebarLists.php`), create/rename/delete, "default list" assignment on user creation (both `Register.php` and `UserSeeder`)
- [x] `Todo` Livewire component: create, edit, delete (soft delete), toggle status (`todo` → `doing` → `done`), toggle favorite — all in `Dashboard.php`
- [x] Wire [Dashboard.php](app/Livewire/Main/Dashboard.php) to real data: fetch the authenticated user's todos (scoped by list/status/search), replace the 3 hardcoded todo-card includes with a `@forelse`
- [x] Manual ordering via `position` column — done in Phase 2 as up/down controls (see below)
- [x] Set `uuid`, `version`, `client_updated_at` on every create/update from day one
- [x] Tag CRUD + attach/detach tags on a todo (`tag_todos` pivot) — comma-separated input in the todo modal, find-or-create per user, `sync()`'d on save
- [x] ~~Route this correctly under `routes/web.php`~~ — **decision**: kept everything on the single `/dashboard` route using `#[Url]`-less Livewire events (`list-selected`, `filter-selected`) for in-page filtering instead of separate `/lists`, `/todos` pages. Revisit if deep-linking to a specific list/filter via URL becomes a requirement.

## Phase 2 — Todo UX ✅
_Once CRUD works, make it pleasant to use._

- [x] Filtering: by list, status (today/upcoming/completed/favorites), priority, tag — priority/tag filters added as dropdowns in the dashboard header, combine with the sidebar's list/status filters
- [x] Search (title) — already existed, kept as-is (description search not added, title-only was judged sufficient for v1)
- [x] Due dates + overdue highlighting (red badge when `due_date` is in the past and status isn't done/archived); reminder display (bell badge from `reminder_at`, now editable in the todo modal)
- [x] Empty states — message now adapts to context (search term, active filter, empty list, no filters) instead of one generic string
- [x] Manual reorder — up/down chevrons per todo, `Dashboard::moveUp/moveDown()` renumbers `position` within the todo's list on every move (self-healing against the position=0 backfill gap from Phase 1)
- [x] Keyboard shortcuts — `/` focuses search, `n` opens the new-todo modal (both skip when a form field is focused), `Escape` closes the modal via `wire:keydown.escape.window`
- [x] Dark/light theme toggle wired to `users.theme` — new `ThemeToggle` Livewire component in the header persists the choice to the DB (`PATCH`-equivalent via Livewire, not a raw route) and broadcasts a `theme-changed` browser event so the switch is instant; initial theme is now server-rendered on `<html data-theme>` to avoid a flash of the wrong theme. Guest pages (landing, login/register) keep the old localStorage-only toggle since there's no user record yet.

**Incidental fixes made while in this code:**
- `resources/views/layouts/app.blade.php` had `@livewireScripts` duplicated (loaded the Livewire JS bundle twice) — removed the duplicate
- 21 Pest tests now passing (added 8 more this phase: priority/tag filters, reorder up/down, position-on-create, theme toggle persistence)

## Phase 3 — Admin Panel Completion ✅
- [x] `UserEdit`/`UserView` — turned out to already be fully built (role change, sync info panel, per-user stats) from before this session; the earlier snapshot in this doc was wrong to call them "scaffolded". What was actually missing: the Edit/Delete buttons in `UserIndex`'s row dropdown were dead markup (no `href`/`wire:click`) — now wired
- [x] Soft-delete/restore — `UserIndex::delete()` soft-deletes (self-delete blocked), a "Show deactivated" toggle lists trashed users with a Restore action
- [x] New [Overview.php](app/Livewire/Main/Admin/Overview.php) admin dashboard at `/admin` — user/admin counts, todo/completed counts, device counts, and a recent `sync_logs` table (empty state until Phase 5 ships the sync engine)
- [x] Confirmed [AdminMiddleware.php](app/Http/Middleware/AdminMiddleware.php) coverage — all four admin routes (`dashboard`, `users`, `users.view`, `users.edit`) sit inside the one `['auth','admin']` group in `routes/web.php`; Livewire full-page components don't register a separate bypassable route, so there's no gap. Verified with tests: guests redirect to login, non-admins get a real 403.
- [x] Sidebar gained an "Admin Overview" link (admin-only, above "Users")
- [ ] **Deliberately not done**: didn't add a "last remaining admin can't be deleted" guard — traced through the access model and it's dead code by construction (only admins can reach this page; if the admin count is 1, the only admin who could attempt the deletion is that same admin, which the self-delete guard already blocks). Revisit only if a future superadmin/service-account concept changes who can call this.

## Phase 4 — Profile & Account Settings ✅
- [x] [ProfileIndex.php](app/Livewire/Main/ProfileIndex.php) — like `UserEdit`/`UserView` in Phase 3, this was already a read-only view with stats and sync info, not a bare stub as this doc previously said. Added the missing piece: an edit mode (toggle via "Edit Profile") for name/email/timezone/theme, with email-uniqueness validation
- [x] Avatar upload — `WithFileUploads`, stored on the `public` disk under `avatars/`, shown as a circular image instead of the initial-letter avatar once set. Ran `php artisan storage:link` (wasn't linked yet) so uploads are actually servable
- [x] Password change — separate card, requires the current password (Laravel's built-in `current_password` validation rule) before accepting a new one
- [x] Device list — reads `auth()->user()->devices()`, each with a "Revoke" button (`wire:confirm`) that deletes the device row. Currently always empty in practice since nothing registers a device yet (Phase 5's job) — the empty state says so explicitly
- Added a missing `DeviceFactory`
- **Fixed a latent bug found via this work**: `UserFactory` didn't set `timezone`/`theme`/`role`, so `User::factory()->create()` left those properties unset in memory (relying on DB column defaults that Eloquent doesn't back-fill into the model after insert). Harmless until a component with a non-nullable typed property (like `ProfileIndex::$timezone`) tried to `mount()` from one — now fixed at the factory level since this would have bitten any future test the same way

## Phase 5 — Offline Sync Engine (server side)
_This is the architectural centerpiece the schema was built for — biggest phase._

- [ ] `routes/api.php` + Sanctum (or session) auth for API access from the mobile shell
- [ ] Device registration endpoint → populates `devices` (uuid, fingerprint, platform)
- [ ] Push endpoint: client sends batched `todo_changes` (create/update/delete/status_changed) → server applies, bumps `version`, marks `is_synced`
- [ ] Pull endpoint: client requests changes since `last_synced_at` → server returns diffs
- [ ] Conflict resolution strategy (likely last-write-wins by `client_updated_at` + `version`, or field-level merge) — **decide and document this before writing code**
- [ ] `SyncLog` entry written per sync attempt (uploaded/downloaded/conflicts counts, status)
- [ ] Soft-delete propagation via `deleted_at_client`

## Phase 6 — Client-Side Offline Storage
_Depends entirely on Phase 5's API contract being stable._

- [ ] IndexedDB (or similar) local store mirroring `todos`/`todo_lists`/`tags`
- [ ] Background sync trigger (on reconnect, on interval, on app foreground)
- [ ] Optimistic UI updates while offline; queue of pending `todo_changes`
- [ ] Conflict UI (surface to user when server rejects/merges a change)

## Phase 7 — NativePHP Mobile Packaging
- [ ] Verify Android build (`nativephp/android/`) compiles and runs against Phase 1–4 features
- [ ] Native device APIs as needed (notifications for reminders, biometric unlock, etc.)
- [ ] iOS target (if in scope — not currently scaffolded, only Android present)
- [ ] Mobile-specific nav/UX pass using [mobile-nav.blade.php](resources/views/partials/mobile-nav.blade.php)

## Phase 8 — Testing & QA
- [ ] Feature tests per CRUD area (todos, lists, tags, admin)
- [ ] Sync engine tests: push/pull, conflict scenarios, multi-device
- [ ] Policy/authorization tests (a user cannot see/edit another user's todos; admin-only routes are actually gated)
- [ ] CI pipeline (GitHub Actions or similar) running Pest + Pint on push

## Phase 9 — Deployment & Release
- [ ] Production `.env` hardening, queue worker for any async sync/notification jobs
- [ ] Build pipeline for mobile artifacts (signed APK, later IPA)
- [ ] Basic monitoring/error tracking (e.g. Laravel Pail already in dev deps — decide prod logging story)

---

## Open Decisions Needing Input
These block downstream phases and should be resolved early:
1. **Conflict resolution rule** for sync (Phase 5) — last-write-wins vs. field merge vs. manual resolution UI
2. **iOS support** — is Android-only acceptable for v1, or does NativePHP iOS scaffolding need to be added?
3. **Notifications** — are due-date reminders in scope for v1, or a later phase?
