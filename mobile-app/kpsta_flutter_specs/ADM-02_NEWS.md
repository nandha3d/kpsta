# Admin News

## Route
`/admin/news`

## Purpose
Implement this as a real Flutter screen backed by the KPSTA API. Do not substitute a WebView for normal application UI.

## Required elements
- list
- search
- add/edit
- publish/unpublish
- delete
- batch delete
- rich content

## Behavior
Match existing create/edit/delete lifecycle and refresh list after writes.

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
