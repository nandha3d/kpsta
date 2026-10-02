# Admin Navigation, WhatsApp Authentication & Permission Model

## Goal
Create a dedicated administrator experience in Flutter. It is not a public website with an admin login bolted on; it is a role-protected operational console.

## WhatsApp login
### Screens
- ADM-AUTH-01 Splash/session restore
- ADM-AUTH-02 WhatsApp number entry
- ADM-AUTH-03 OTP verification
- ADM-AUTH-04 Access denied / not an administrator
- ADM-AUTH-05 Session expired

### Number entry
- country code selector defaults to India but supports the server-configured list
- mobile field
- consent/security copy
- send OTP button
- loading/progress
- resend disabled during cooldown

### OTP
- 6-digit input if backend dictates
- automatic focus
- paste support
- countdown
- resend
- change number
- verify
- incorrect/expired/too-many-attempts errors

### Backend contract
Recommended:
`POST /api/v1/auth/whatsapp/request-otp`
`POST /api/v1/auth/whatsapp/verify-otp`
`POST /api/v1/auth/refresh`
`POST /api/v1/auth/logout`
`GET /api/v1/auth/me`
`GET /api/v1/auth/permissions`

Server response after verify must include user summary, role/group IDs, office context and an explicit permission matrix/menu tree. The app must not infer permissions from route names.

## Admin shell
- top app bar: page title, notifications if implemented, avatar/menu
- navigation drawer/modal sheet: grouped modules
- content area
- floating action button only where creation is primary and authorized

## Admin module navigation
### Dashboard
- Home
- Flash news quick edit/save
- summary metrics where data exists

### Security/access
- Users
- Groups
- Group-to-menu permissions

### Content
- News
- Flash News
- Slider
- Service Corner
- Adayapaka Sabham

### Documents
- Order Circulars
- Downloads
- Notice Posters
- Melakal
- Official Outlook
- Membership documents
- Quick Links
- Result Links

### Organization
- Office Bearers
- Designations
- Districts
- District Office Bearers
- Reaction Gallery
- Gallery/Albums/Images

### System
- Membership
- Settings
- Backup (only implemented endpoint(s))

## Permission rules
Each module supports read/create/edit/delete/publish where the server exposes those operations. Do not show a destructive action to a user lacking permission. Also handle the case where the action becomes unauthorized between load and submit.

## Session recovery
- On app start, load secure refresh token.
- call `/auth/me` or refresh endpoint.
- if valid, preload permissions.
- if invalid, clear auth and route to WhatsApp login.
- never route to login after a transient network outage; show retry first.

## Admin global interactions
- Pull to refresh list pages.
- Search fields debounce 300–500 ms where remote.
- Filter sheet for category/type/year/state.
- Page-size setting only if API permits.
- Bulk select for modules with `batch_delete` semantics.
- Confirm publish/unpublish.
- Confirm delete.
- Toast/snackbar plus inline result feedback after writes.

## Admin accessibility
All icon-only buttons need tooltips/semantic labels. Color is never the only status indicator.
