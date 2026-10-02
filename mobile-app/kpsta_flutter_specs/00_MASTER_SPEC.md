# KPSTA Flutter Mobile App — Master Implementation Specification

## 1. Source of truth
- Backend repository: `https://github.com/nandha3d/kpsta`
- Default branch: `main`
- Backend: CodeIgniter 4.7.4 / PHP 8.2+ with a CI3 compatibility layer.
- Repository README states: 45 controllers, 35 models, 135 views, 349 explicit CI4 routes derived from 224 CI3 routes.
- Existing browser tests cover public link crawling plus admin CRUD for news, order/circular, organization, users, gallery, downloads, etc.

## 2. Objective
Build a production-quality Flutter mobile application that exposes every meaningful public, admin, and membership capability represented by the web application. Do not omit an action merely because the website implements it in a modal, AJAX request, partial, or route helper.

The Flutter app must be user-friendly rather than visually copying desktop Bootstrap UI. Preserve information architecture, business rules, permissions, validations, data meaning, CRUD semantics, publishing states, uploads, downloads, and reports.

## 3. Roles
1. Guest/public user: browse all public content and initiate public donation/contact flows.
2. Administrator: access admin dashboard and all authorized admin modules.
3. Membership user: access membership dashboard, teacher records, member users/settings, and permitted content.
4. Some membership users have different group/office permissions; permissions must be server-authoritative and the app must hide unauthorized actions while still handling 401/403 robustly.

## 4. Authentication strategy
### Admin login
User explicitly requires WhatsApp authentication login. Implement a backend API contract for WhatsApp OTP/authentication rather than trying to automate the existing web form. Required flow:
- enter WhatsApp-capable mobile number
- request OTP
- verify OTP
- server resolves the corresponding admin identity and role/group/menu permissions
- issue short-lived access token + refresh token (or equivalent server session token)
- persist only secure tokens locally
- logout revokes/invalidates refresh session when supported

Never store OTP, password, or admin secrets locally. Do not bypass authorization by relying only on client-side role flags.

### Existing web authentication compatibility
The repository currently uses Aauth for admin and membership authentication, with session-backed access checks and existing web login routes. The mobile backend/API layer must preserve those account and permission semantics, while providing JSON/mobile endpoints. Do not destroy or rewrite the existing web authentication solely for Flutter.

## 5. Suggested Flutter stack
- Flutter stable, Dart 3+.
- Architecture: feature-first + clean boundaries (presentation/domain/data) without unnecessary ceremony.
- State management: Riverpod or Bloc. Choose one and use consistently.
- Navigation: go_router with authenticated route guards.
- HTTP: Dio with interceptors for auth refresh, standardized errors, request IDs.
- Secure storage: flutter_secure_storage.
- Serialization: json_serializable/freezed or equivalent consistent generated models.
- Local cache: Hive/Isar/Drift; select one, preferably Drift for structured offline metadata if needed.
- Images: cached_network_image.
- Files: file_picker, open_filex, path_provider as needed.
- Charts: fl_chart or equivalent.
- PDF: existing backend-generated PDFs should be downloaded/opened; do not attempt to reproduce PDF generation client-side unless necessary.

## 6. Core UX principles
- Mobile-first, thumb-friendly controls, minimum 44–48 dp interactive targets.
- Use clear page titles and breadcrumbs/context chips for nested admin modules.
- Pull-to-refresh on lists.
- Pagination/infinite scrolling where backend is paginated.
- Search and filter controls must preserve query state when navigating into details and back.
- Skeleton loading rather than blank screens.
- Empty, offline, error, permission-denied, and expired-session states for every data-driven screen.
- Destructive actions require confirmation and state the entity name.
- Publishing toggles must have clear current state and confirmation for accidental changes.
- File upload must show allowed types, progress, upload status, retry and remove-before-submit.
- URLs must open externally/in-app browser with safe handling.
- Dates must be formatted locally but request payloads must use backend-compatible formats.
- HTML/rich content must be rendered safely; sanitize or use a trusted renderer.

## 7. Common reusable components
- AppShellPublic
- AppShellAdmin
- AppShellMembership
- AppTopBar
- BottomNavigation where appropriate
- AdminNavigationDrawer / modal sheet
- SectionHeader
- SearchField
- FilterChips / FilterBottomSheet
- PaginatedList
- EmptyState
- ErrorState + retry
- Skeleton loaders
- StatusBadge (published/unpublished/active/inactive)
- ConfirmationDialog
- FormTextField / FormDatePicker / FormDropdown / FormRadio / FormSwitch
- RichTextViewer
- FilePickerField + UploadProgress
- ImagePicker + crop preview for banner-like images
- ImageGalleryGrid + full-screen viewer
- PersonCard / OfficeBearerCard
- DocumentRow with date/category/file/link actions
- StatisticCard
- DashboardChart
- PermissionGate
- Token/session manager
- DeepLinkRouter

## 8. API layer requirements
Do not assume current HTML endpoints are ideal mobile APIs. Add a dedicated JSON API surface backed by the existing models/services. API contracts should be versioned (`/api/v1/...`).

Every API response should use a predictable envelope, for example:
```json
{
  "success": true,
  "data": {},
  "meta": {},
  "message": null,
  "errors": []
}
```
For lists, include pagination metadata. For auth, include access/refresh expiry.

Map legacy AJAX actions such as `add`, `edit`, `update`, `delete`, `publish`, `batch_delete`, `fileupload`, `fileremove` to resource-oriented API actions where possible, while preserving backend semantics.

## 9. Security requirements
- HTTPS only in production.
- No API keys or secrets in Flutter source.
- Certificate pinning is optional and only if operationally maintainable.
- Never trust client role/permission state.
- Handle 401 by refreshing once, then clearing session.
- Handle 403 with permission state rather than redirect loops.
- Validate uploaded MIME/type and size server-side.
- Never construct filesystem paths from client-provided filenames.
- Redact tokens/OTPs/passwords from logs.
- Do not log full request bodies for sensitive endpoints.
- Prevent duplicate writes with idempotency keys where payment/donation endpoints require them.

## 10. Offline/cache behavior
Public read-only pages can cache successfully fetched content for a short configurable TTL. Admin/membership write operations require network. Show stale-data indicators where applicable.

## 11. Testing
Each feature must include:
- unit tests for parsers/validators/use cases
- widget tests for important interactive screens
- integration tests for auth, navigation, list/detail, CRUD, uploads, and deep links
- API contract tests where API is implemented

Map existing Playwright coverage into equivalent mobile integration coverage. Existing web tests are a behavior oracle, not a reason to omit mobile-specific UX testing.

## 12. Completion gate
A page is not complete until all of these work:
- loading
- successful populated state
- empty state
- error state
- offline/no connection state
- retry
- navigation in/out
- authorization behavior
- validation
- write action (where applicable)
- success confirmation
- failure response
- back navigation state preservation
- accessibility labels
- responsive layout on small phones and tablets
- integration test

## 13. Important repository-specific notes
- Public web routes include home, contact, service corner, service details, memorandums, order circular variants, multiple download sections, former leaders, office bearers, teacher/association section (`adayapaka_sabham`), gallery, news, district, quick links, results, privacy, and donation/payment flows.
- Admin includes authentication, dashboard, users/groups/menus, news, order/circulars and categories, gallery/albums/images, Adayapaka Sabham, downloads/categories, notice posters, melakal, official outlook, quick links, result links, office bearers/designations, slider, flash news, districts/district office bearers, reaction gallery, membership content, service corner/rules, settings, backup, and associated AJAX operations.
- Membership includes authentication, home/dashboard, membership counts, teacher CRUD and reports, membership users, main content, change password, config, and WhatsNew.
- Backup routes `admin/backup/kpsta_db` and `admin/backup/website` exist but the repository README says those controller methods are not implemented; represent them as visible/disabled or informational only rather than inventing a working feature.

## 14. Agent working rule
The implementing coding agent MUST read this master file plus the feature page spec before coding a feature. It must inspect the current repository implementation and API before inventing fields. When backend behavior conflicts with a UI assumption, backend behavior wins and the discrepancy must be documented.
