# Low-Level Coding Agent Execution Order

## Phase 1 — Inspect before writing
1. Read `00_MASTER_SPEC.md`.
2. Inspect repository routes, controllers, models, views, existing tests.
3. Identify actual JSON/data requirements from current backend.
4. Create a Flutter project without modifying business rules.
5. Establish theme, navigation, API client, auth, error model and shared widgets.

## Phase 2 — Backend mobile API foundation
1. Add `/api/v1` routing.
2. Add consistent JSON response envelope.
3. Add auth endpoints, including WhatsApp OTP provider abstraction.
4. Add `/auth/me` and permissions.
5. Add public read endpoints.
6. Add admin/membership endpoints incrementally.
7. Write backend API tests.

## Phase 3 — Flutter foundations
Implement:
- app bootstrap
- environment config
- Dio client
- secure token storage
- auth/session state
- go_router guards
- public/admin/membership shells
- common list/form/file/gallery components
- global error handling

## Phase 4 — Public screens
Implement in this order:
Home -> News -> Order Circulars -> Downloads -> Gallery -> Office Bearers/Districts -> Service Corner -> Quick Links/Results -> Former Leaders/Adayapaka -> Contact/Privacy -> Donation.

## Phase 5 — Admin
Implement:
Auth -> Dashboard -> News -> Flash News -> Slider -> Service Corner -> Order Circulars -> Downloads -> Gallery -> Office Bearers/Designations -> Districts -> Quick Links/Results -> Adayapaka -> Reaction Gallery -> Users/Groups/Menu Permissions -> Membership -> Settings -> Backup.

## Phase 6 — Membership
Implement:
Auth -> Dashboard -> Counts -> Teachers -> Teacher detail/edit/bulk -> Reports/PDF -> Users -> Main -> Config -> What's New -> Change Password.

## Phase 7 — QA
After each feature:
- run analyzer
- unit/widget tests
- integration test for critical path
- manual device smoke test
- verify permission-denied state
- verify offline/error state

## Agent constraints
- Do not remove existing web functionality.
- Do not invent fields when repository inspection can answer the question.
- Do not copy desktop Bootstrap markup into Flutter.
- Do not use WebView as a substitute for real Flutter screens except for unavoidable third-party/payment/browser flows.
- Do not put business logic inside widgets.
- Do not store auth secrets in plain local storage.
- Do not silently swallow API errors.
- Do not mark a feature done until its page-level acceptance checklist is satisfied.
