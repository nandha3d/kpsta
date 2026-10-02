# Public Home

## Route
`/`

## Purpose
Implement this as a real Flutter screen backed by the KPSTA API. Do not substitute a WebView for normal application UI.

## Required elements
- hero slider/banner
- flash news ticker/highlight
- latest news
- service corner highlights
- organization/office bearer highlights
- downloads/circular shortcuts
- online links
- contact/donation CTAs

## Behavior
Fetch a composed home payload where possible. Each section must independently support empty/error states. Slider taps must navigate to configured target/content.

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
