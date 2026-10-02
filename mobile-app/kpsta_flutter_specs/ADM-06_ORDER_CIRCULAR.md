# Admin Order Circular

## Route
`/admin/order-circular/{type}`

## Purpose
Implement this as a real Flutter screen backed by the KPSTA API. Do not substitute a WebView for normal application UI.

## Required elements
- General/HSE/VHSE
- category management
- search
- add/edit
- URL/file upload
- publish/delete/batch delete

## Behavior
Reuse one feature keyed by type. Category CRUD is permission-gated.

## Required states
- loading/skeleton
- populated
- empty
- network error + retry
- unauthorized (401)
- forbidden (403)
- validation failure for writes
- success confirmation
- offline handling appropriate to read/write

## Navigation
- Use typed IDs/enums for route arguments.
- Preserve list filters/search/page state when returning.
- Do not pass mutable entities as the long-term source of truth; reconcile with repository/cache.

## Accessibility
Every icon action needs a semantic label. Do not use color alone for status.

## Completion
Complete only after UI, API integration, permissions, validation, all listed states, and critical interaction tests pass.
