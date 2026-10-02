# KPSTA Flutter QA & 100% Completion Checklist

## Pre-development audit
- [ ] Inspect current controllers/models/views for all fields and business rules.
- [ ] Inspect all route declarations, including aliases.
- [ ] Inspect web Playwright tests and map them to app integration tests.
- [ ] Identify endpoints that are read-only vs write.
- [ ] Identify file types and upload constraints from backend validators.
- [ ] Confirm authorization/menu mappings.

## Public parity
- [ ] Home
- [ ] contact
- [ ] service corner
- [ ] service detail
- [ ] memorandums
- [ ] order circular general/HSE/VHSE
- [ ] all download categories/types
- [ ] former leaders
- [ ] state office bearers
- [ ] Adayapaka Sabham
- [ ] gallery + album detail
- [ ] news + pagination/search
- [ ] districts
- [ ] quick links
- [ ] results
- [ ] privacy
- [ ] donation/payment/status/success

## Admin parity
- [ ] WhatsApp login
- [ ] session restore/refresh/logout
- [ ] dashboard
- [ ] users
- [ ] groups
- [ ] group-to-menu
- [ ] news
- [ ] order circulars + categories
- [ ] gallery + images + cover
- [ ] Adayapaka Sabham
- [ ] downloads + categories + file operations
- [ ] notice posters
- [ ] melakal
- [ ] official outlook
- [ ] quick links
- [ ] result links
- [ ] office bearers + designations
- [ ] slider + crop
- [ ] flash news
- [ ] districts + district office bearers
- [ ] reaction gallery
- [ ] membership admin
- [ ] service corner + rules
- [ ] settings
- [ ] implemented backup functionality

## Membership parity
- [ ] login
- [ ] home dashboard
- [ ] membership count filters
- [ ] teacher list
- [ ] teacher add/edit/view/delete
- [ ] bulk add
- [ ] process
- [ ] consolidated views
- [ ] reports
- [ ] PDF
- [ ] membership users
- [ ] main content
- [ ] config
- [ ] change password
- [ ] WhatsNew CRUD/publish/file remove

## Each CRUD resource
- [ ] list
- [ ] pagination
- [ ] search/filter
- [ ] empty
- [ ] loading
- [ ] error/retry
- [ ] add
- [ ] edit
- [ ] validation
- [ ] save success
- [ ] save failure
- [ ] publish/unpublish
- [ ] delete confirmation
- [ ] batch delete if supported
- [ ] permission gates
- [ ] back-state preservation

## File resources
- [ ] file picker
- [ ] image picker
- [ ] preview
- [ ] size/type validation
- [ ] upload progress
- [ ] retry
- [ ] remove
- [ ] failed upload recovery
- [ ] open/download

## Auth/security
- [ ] OTP abuse throttling server-side
- [ ] secure token storage
- [ ] one-refresh retry rule
- [ ] 401 logout behavior
- [ ] 403 behavior
- [ ] sensitive log redaction
- [ ] no secrets in mobile bundle

## Regression
- [ ] current web Playwright suite still passes after API changes
- [ ] mobile integration tests pass
- [ ] Android release build
- [ ] iOS release build if in scope
- [ ] physical-device test with poor network
- [ ] tablet layout test
- [ ] accessibility smoke test
- [ ] deep link smoke test

## Definition of done
A feature is DONE only when code, API, UI, state management, permissions, validation, tests and error/empty states are complete. Do not mark a screen complete because the happy-path UI renders.
