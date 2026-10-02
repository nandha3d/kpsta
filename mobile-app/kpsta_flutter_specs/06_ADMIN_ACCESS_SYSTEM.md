# Admin Access, System, Settings & Backup — Detailed Specifications

## ADM-SYS-01 Users
Legacy routes indicate:
- `/admin/aauth/users`
- add/create
- edit/update

Existing browser test demonstrates fields:
- username
- email
- password for creation
- group selector

Mobile must never display an existing user's password. Password creation fields should be handled only if the backend still requires them for non-WhatsApp account management. Prefer account invitation/reset semantics for future API versions.

## ADM-SYS-02 Groups
Legacy `/admin/aauth/group`

Supports list/add/edit/update/publish/search/delete. Show:
- group name
- status
- member count when available
- assigned menus/permissions shortcut

## ADM-SYS-03 Group-to-Menu Permissions
Legacy `/admin/aauth/group_to_menu`

Two screens:
1. group list/selector
2. menu permission tree

Need checkbox/tree behavior for menu access. Server remains authoritative. Save must be transactional on backend: either all intended menu mappings persist or none.

## ADM-SYS-04 Dashboard
Legacy `/admin/home`

Primary operational dashboard. Use cards/tiles for real counts returned by backend. Do not invent analytics. Include quick actions to common publishing modules. Existing `flashNewsSave` endpoint can become an inline dashboard action.

## ADM-SYS-05 Change Password
Legacy `/admin/change_password`

Although admin login is WhatsApp-authenticated, this screen should only appear if backend/API actually supports password changes for an admin. Otherwise omit it from the authenticated menu but retain route handling for legacy compatibility.

## ADM-SYS-06 Settings
Legacy `/admin/settings`

The current controller is a settings entry point; agent must inspect the actual view/model fields and reproduce every editable setting. Use grouped sections, explicit save per section or single save as backend requires.

## ADM-SYS-07 Backup
Legacy `/admin/backup`

README says:
- `download_db` exists
- `kpsta_db` route exists but method not implemented
- `website` route exists but method not implemented

Therefore mobile should expose only actually implemented backup actions. Database backup/download is highly privileged: show confirmation with warning, progress state, and secure download/open behavior. Never put backup URLs into analytics logs.

## ADM-SYS-08 Quick Links
Legacy `/admin/quicklink`

CRUD + delete/publish if supported by controller/model. Fields must be inspected from view/model. Validate URL.

## ADM-SYS-09 Result Links
Legacy `/admin/result_link`

CRUD + delete/publish if supported. Validate URL. Public Results screen consumes published entries.
