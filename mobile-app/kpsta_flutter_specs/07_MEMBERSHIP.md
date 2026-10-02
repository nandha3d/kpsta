# Membership Area — Flutter Specification

## Scope
Membership is a separate authenticated product surface in the same repository. Implement it even if the initial app release is branded as the main association app, because routes/controllers/models form a complete first-class module.

## Screens
| ID | Screen | Legacy route |
|---|---|---|
| MEM-AUTH-01 | Membership Login | `/membership/login` |
| MEM-01 | Membership Dashboard | `/membership/home` |
| MEM-02 | Membership Counts | `/membership/home/membershipcount` |
| MEM-03 | Teachers | `/membership/teacher` |
| MEM-04 | Add Teacher | `/membership/teacher/add` |
| MEM-05 | Edit Teacher | `/membership/teacher/edit/:id` |
| MEM-06 | Teacher Detail | `/membership/teacher/view/:id` |
| MEM-07 | Teacher Processing | `/membership/teacher/process` |
| MEM-08 | Consolidated View | `/membership/teacher/consoliated` |
| MEM-09 | Consolidation Report | `/membership/teacher/consolidation_report` |
| MEM-10 | Designation-wise Report | `/membership/teacher/designation_report` |
| MEM-11 | Teacher PDF | `/membership/teacher/generate_pdf` |
| MEM-12 | Membership Users | `/membership/aauth/users` |
| MEM-13 | Add Membership User | `/membership/aauth/add` |
| MEM-14 | Edit Membership User | `/membership/aauth/edit` |
| MEM-15 | Main Membership Content | `/membership/main` |
| MEM-16 | Add Main Content | `/membership/main/add` |
| MEM-17 | Edit Main Content | `/membership/main/edit/:id` |
| MEM-18 | Membership Settings | `/membership/settings/config` |
| MEM-19 | Change Password | `/membership/settings/change_password` |
| MEM-20 | What's New | `/membership/settings/whats_new` |
| MEM-21 | Add What's New | `/membership/whats_new/add` |
| MEM-22 | Edit What's New | `/membership/whats_new/edit/:id` |

## Membership login
Current web login uses username/password and reCAPTCHA. For Flutter, do not embed reCAPTCHA HTML. Create a mobile-compatible backend auth endpoint while retaining account semantics. WhatsApp login is explicitly required for admin; do not automatically extend that requirement to membership unless the product owner decides so.

## Dashboard
The existing controller loads:
- published What's New
- first 3 published office bearers
- membership dashboard counts

Build a dashboard with those sections plus year/group/office selectors where authorized.

## Membership counts
Filters are data-driven: group, office, year. Preserve current selection in the URL-equivalent app state. Show totals in cards plus a readable table/list. Support changing year and office without losing the rest of the context.

## Teacher management
The Teacher controller is the largest membership screen and must be treated as a major feature, not a simple CRUD form.

### List
- year selector
- group selector
- office/region selector
- search
- view mode selector where backend exposes multiple views
- page size if supported
- pagination
- add teacher when `enable_entry` and permission permit

### Teacher detail
Must include every field returned by API, grouped logically:
- identity
- contact
- school/employment
- designation
- branch/office
- membership metadata
- year/process/status

Never infer a field from the controller name. Inspect the actual Teacher model, create form and view.

### Add/edit
Use sections with validation summary. Save progress indicator. For edit, show last saved server values and unsaved-change warning on back.

### Bulk add
The route `/membership/teacher/add_bulk` exists. Provide a bulk import workflow:
1. download template if API provides it
2. choose CSV/XLSX if backend accepts it
3. preview rows and validation errors
4. submit only after confirmation
5. show per-row result summary

Do not assume file format until backend implementation is inspected.

### Reports
Consolidated/consolidation/designation-wise reports should be filterable and exportable only when backend provides export endpoints. PDF generation should call server and open the returned file.

## Membership users
CRUD user accounts with group/office context. The controller has `getOffice` and `generateUserName`; expose these as dependent form interactions.

## Main membership content
CRUD screen for the `Main` controller. Inspect exact form fields from views/models. Support publication/media/link semantics actually present.

## Config
Current membership Home controller indicates:
- enable_entry toggle
- year
- special authorization for state group
- process entry creation when year changes

Implement as a privileged settings screen. Changing year must have a confirmation explaining that it affects process data creation.

## What's New
CRUD + publish + delete + file remove. Use content/document cards. Published items appear on membership dashboard.

## Permissions
Group/office/year constraints must come from server. If the backend returns `aauthGroupId`, `aauthOfficeId`, or equivalent, treat those as context, not user-editable authority.
