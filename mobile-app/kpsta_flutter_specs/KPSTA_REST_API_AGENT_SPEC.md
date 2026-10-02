# KPSTA REST API --- Agent Implementation Specification

## 0. Mission

Implement a production-ready REST API for the existing KPSTA CodeIgniter
PHP application so that the Flutter mobile application can consume the
same underlying KPSTA data and business rules.

Repository: - GitHub: `https://github.com/nandha3d/kpsta.git`

This specification is for a coding agent. The agent MUST inspect the
repository before changing code and MUST NOT invent database tables,
columns, permissions, business rules, or existing APIs.

## 1. Non-Negotiable Rules

1.  Preserve the existing KPSTA website and existing web admin panel.
2.  Do not replace existing server-rendered pages with the API.
3.  Do not break existing URLs, controllers, forms, AJAX behavior, or
    membership workflows.
4.  Add a clean versioned REST API under `/api/v1`.
5.  Flutter must consume JSON APIs only; it must never scrape HTML
    pages.
6.  Reuse existing models, services, validation rules, database tables,
    permissions, uploads, and business logic wherever practical.
7.  Do not duplicate business rules separately for Flutter.
8.  API responses must be JSON and use consistent HTTP status codes.
9.  Every protected endpoint must enforce authentication and
    authorization.
10. Admin WhatsApp OTP authentication is required for the mobile/admin
    API.
11. Never return passwords, password hashes, OTP values, secrets,
    internal tokens, or sensitive credentials.
12. Never hardcode WhatsApp provider credentials, JWT secrets, OTP
    secrets, URLs, or environment-specific values.
13. Use environment/configuration values for secrets and provider
    settings.
14. Do not expose unrestricted database queries through API parameters.
15. Validate and sanitize every client-controlled input.
16. Use pagination for potentially large collections.
17. Use transactions for multi-step state-changing operations.
18. Add automated API tests for every implemented endpoint group.
19. Update API documentation when an endpoint changes.
20. The implementation is incomplete until tests pass and the API can be
    consumed by Flutter.

## 2. First Step --- Repository Audit

Before coding, inspect:

-   `app/Config/Routes.php`
-   `app/Controllers/`
-   `app/Models/`
-   `app/Config/`
-   `app/Filters/`
-   `app/Database/`
-   `app/Views/`
-   `app/Config/Ci3/`
-   `composer.json`
-   `.env` / `.env.example`
-   existing authentication implementation
-   existing admin permission/group implementation
-   existing membership authentication
-   existing AJAX/JSON endpoints
-   existing upload/file handling
-   existing tests under `tests/`

Create an internal endpoint mapping:

  ---------------------------------------------------------------------------------
  Existing     Existing           Existing DB Existing     New API      Status
  feature      controller/model   table(s)    permission   controller   
  ------------ ------------------ ----------- ------------ ------------ -----------
  News         inspect repo       inspect     inspect repo API News     TODO
                                  repo                                  

  Gallery      inspect repo       inspect     inspect repo API Gallery  TODO
                                  repo                                  

  Membership   inspect repo       inspect     inspect repo API          TODO
                                  repo                     Membership   

  etc.                                                                  
  ---------------------------------------------------------------------------------

Do not assume names. Confirm them from source.

## 3. API Architecture

Target structure:

``` text
Existing KPSTA Application
│
├── Existing Website
├── Existing Admin Panel
├── Existing Membership System
│
└── REST API
    └── /api/v1
        ├── Auth
        ├── Public
        ├── Membership
        ├── Admin
        ├── Media
        └── Notifications
```

Preferred source structure, adapted to the actual repository
conventions:

``` text
app/
├── Controllers/
│   └── Api/
│       └── V1/
│           ├── AuthController.php
│           ├── HomeController.php
│           ├── NewsController.php
│           ├── OrganizationController.php
│           ├── CircularController.php
│           ├── DownloadController.php
│           ├── GalleryController.php
│           ├── LinkController.php
│           ├── ContactController.php
│           ├── MembershipController.php
│           ├── AdminController.php
│           └── ...
├── Filters/
│   └── ApiAuthFilter.php
└── Services/
    └── Api/
        ├── AuthService.php
        ├── OtpService.php
        ├── TokenService.php
        └── ...
```

If the existing architecture has an established equivalent, follow it
instead of forcing this exact directory structure.

## 4. API Base URL

All endpoints MUST be versioned:

``` text
/api/v1/
```

Examples:

``` text
POST /api/v1/auth/whatsapp/request-otp
POST /api/v1/auth/whatsapp/verify-otp
GET  /api/v1/home
GET  /api/v1/news
GET  /api/v1/news/{id}
```

Do not create unversioned mobile endpoints.

## 5. Content Type

Requests and responses use:

``` http
Content-Type: application/json
Accept: application/json
```

Multipart requests are allowed only for documented file-upload
endpoints.

## 6. Standard Success Response

Use this structure consistently:

``` json
{
  "success": true,
  "message": "News retrieved successfully",
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}
```

For a single object:

``` json
{
  "success": true,
  "message": "News retrieved successfully",
  "data": {
    "id": 1,
    "title": "Example",
    "description": "...",
    "image_url": "...",
    "published_at": "2026-01-01T10:00:00+05:30"
  }
}
```

For actions:

``` json
{
  "success": true,
  "message": "News published successfully",
  "data": {
    "id": 1,
    "status": "published"
  }
}
```

## 7. Standard Error Response

Validation:

``` json
{
  "success": false,
  "message": "Validation failed",
  "code": "VALIDATION_ERROR",
  "errors": {
    "title": [
      "Title is required"
    ]
  }
}
```

Authentication:

``` json
{
  "success": false,
  "message": "Authentication required",
  "code": "AUTHENTICATION_REQUIRED"
}
```

Forbidden:

``` json
{
  "success": false,
  "message": "You do not have permission to perform this action",
  "code": "FORBIDDEN"
}
```

Not found:

``` json
{
  "success": false,
  "message": "Resource not found",
  "code": "NOT_FOUND"
}
```

Server error:

``` json
{
  "success": false,
  "message": "An unexpected error occurred",
  "code": "INTERNAL_SERVER_ERROR"
}
```

Never expose stack traces, SQL statements, filesystem paths, or secrets
in production API responses.

## 8. HTTP Status Codes

Use:

-   `200` successful GET/action
-   `201` successful creation
-   `204` successful deletion where appropriate
-   `400` malformed request
-   `401` unauthenticated
-   `403` unauthorized
-   `404` resource not found
-   `409` state/conflict error
-   `422` validation failure
-   `429` rate limit
-   `500` server error
-   `503` temporary dependency/provider failure

## 9. Authentication

### 9.1 WhatsApp OTP

Required endpoints:

``` text
POST /api/v1/auth/whatsapp/request-otp
POST /api/v1/auth/whatsapp/verify-otp
POST /api/v1/auth/refresh
POST /api/v1/auth/logout
GET  /api/v1/auth/me
```

### Request OTP

Request:

``` json
{
  "phone": "919876543210"
}
```

Rules:

-   Normalize phone number before lookup.
-   Validate country code according to the application requirement.
-   Do not reveal whether an account exists in a way that enables user
    enumeration.
-   Apply IP/device/phone rate limiting.
-   Generate a cryptographically secure OTP.
-   OTP must expire.
-   OTP must have a maximum verification-attempt count.
-   Resend must have a cooldown.
-   Store only a secure representation of OTP where possible.
-   Log request metadata without logging the OTP itself.
-   Send OTP through the configured WhatsApp provider.
-   Provider credentials must come from environment/configuration.

Response:

``` json
{
  "success": true,
  "message": "OTP request processed",
  "data": {
    "expires_in": 300,
    "resend_after": 60
  }
}
```

### Verify OTP

Request:

``` json
{
  "phone": "919876543210",
  "otp": "123456",
  "device_id": "device-identifier"
}
```

On successful verification:

``` json
{
  "success": true,
  "message": "Authentication successful",
  "data": {
    "access_token": "TOKEN",
    "refresh_token": "TOKEN",
    "expires_in": 3600,
    "user": {
      "id": 12,
      "name": "Admin User",
      "phone": "919876543210",
      "role": "admin",
      "permissions": []
    }
  }
}
```

Do not return passwords or password hashes.

### Token requirements

Use the authentication mechanism already supported safely by the project
if suitable. Otherwise implement a secure token/session design
compatible with the current CodeIgniter version.

Requirements:

-   short-lived access token
-   refresh token
-   refresh token rotation where supported
-   revocation/logout
-   device/session tracking
-   expiry
-   permission checks
-   no secrets in source control
-   no tokens in logs

Do not add a JWT library merely because JWT is familiar. Inspect
`composer.json` and existing authentication first.

## 10. Public API

Implement the following resources after mapping them to actual
repository data.

### Home

``` text
GET /api/v1/home
```

Return the data needed by the Flutter home screen, such as:

-   slider/banner data
-   flash news
-   latest news
-   latest circulars
-   quick links
-   important downloads
-   gallery highlights
-   relevant organization information

Do not duplicate queries unnecessarily.

### Flash News

``` text
GET /api/v1/flash-news
GET /api/v1/flash-news/{id}
```

Supported query parameters where applicable:

``` text
?page=1
&per_page=20
&search=
```

### News

``` text
GET /api/v1/news
GET /api/v1/news/{id}
```

Query:

``` text
?page=1
&per_page=20
&search=
&category=
&year=
```

Only expose filters supported by the real schema.

### Organization

``` text
GET /api/v1/office-bearers
GET /api/v1/districts
GET /api/v1/former-leaders
```

Where district-specific resources exist, support:

``` text
?district_id=
```

Only use actual DB relationships.

### Order & Circular

``` text
GET /api/v1/order-circulars
GET /api/v1/order-circulars/{id}
```

Support verified repository filters/categories.

### Downloads

``` text
GET /api/v1/downloads
GET /api/v1/downloads/{id}
```

Return safe public download URLs.

Do not expose filesystem paths.

### Service Corner

``` text
GET /api/v1/service-corner
GET /api/v1/service-corner/{id}
```

Map to the existing service/rules structure.

### Gallery

``` text
GET /api/v1/galleries
GET /api/v1/galleries/{id}
GET /api/v1/galleries/{id}/images
```

Return public image URLs, not server filesystem paths.

### Quick Links

``` text
GET /api/v1/quick-links
```

### Results

``` text
GET /api/v1/results
GET /api/v1/results/{id}
```

### Contact

``` text
GET /api/v1/contact
```

Return only intended public organization contact information.

## 11. Membership API

The existing membership workflow MUST be inspected in detail before
implementation.

Do not simplify or remove existing states.

Potential endpoints:

``` text
GET  /api/v1/membership/dashboard

GET  /api/v1/membership/profile
PUT  /api/v1/membership/profile

GET  /api/v1/membership/teachers
POST /api/v1/membership/teachers
GET  /api/v1/membership/teachers/{id}
PUT  /api/v1/membership/teachers/{id}

POST /api/v1/membership/teachers/{id}/approve
POST /api/v1/membership/teachers/{id}/reject
POST /api/v1/membership/teachers/{id}/verify

GET /api/v1/membership/reports
GET /api/v1/membership/reports/district
GET /api/v1/membership/reports/education-district
GET /api/v1/membership/reports/sub-district

GET /api/v1/membership/teachers/{id}/pdf
GET /api/v1/membership/teachers/export
```

Before implementing:

1.  Trace the existing membership controllers.
2.  Trace models and DB tables.
3.  Identify all statuses.
4.  Identify valid state transitions.
5.  Identify group/role permissions.
6.  Identify district/education-district/sub-district relationships.
7.  Preserve existing approval/rejection behavior.
8.  Preserve existing PDF generation rules.
9.  Preserve existing filters.
10. Add tests for each state transition.

Do not expose a generic `status` update endpoint that allows users to
bypass workflow rules.

## 12. Admin API

Admin APIs must use the existing permission/group system.

Never assume that every admin user has full permissions.

### Dashboard

``` text
GET /api/v1/admin/dashboard
```

Return useful counts/statistics that can be safely calculated from
existing data.

### News

``` text
GET    /api/v1/admin/news
POST   /api/v1/admin/news
GET    /api/v1/admin/news/{id}
PUT    /api/v1/admin/news/{id}
DELETE /api/v1/admin/news/{id}
POST   /api/v1/admin/news/{id}/publish
```

### Flash News

``` text
GET    /api/v1/admin/flash-news
POST   /api/v1/admin/flash-news
GET    /api/v1/admin/flash-news/{id}
PUT    /api/v1/admin/flash-news/{id}
DELETE /api/v1/admin/flash-news/{id}
POST   /api/v1/admin/flash-news/{id}/publish
```

### Sliders

``` text
GET    /api/v1/admin/sliders
POST   /api/v1/admin/sliders
GET    /api/v1/admin/sliders/{id}
PUT    /api/v1/admin/sliders/{id}
DELETE /api/v1/admin/sliders/{id}
POST   /api/v1/admin/sliders/{id}/publish
```

### Gallery

``` text
GET    /api/v1/admin/galleries
POST   /api/v1/admin/galleries
GET    /api/v1/admin/galleries/{id}
PUT    /api/v1/admin/galleries/{id}
DELETE /api/v1/admin/galleries/{id}

POST   /api/v1/admin/galleries/{id}/images
DELETE /api/v1/admin/galleries/{id}/images/{image_id}
POST   /api/v1/admin/galleries/{id}/cover
```

### Office Bearers

``` text
GET    /api/v1/admin/office-bearers
POST   /api/v1/admin/office-bearers
GET    /api/v1/admin/office-bearers/{id}
PUT    /api/v1/admin/office-bearers/{id}
DELETE /api/v1/admin/office-bearers/{id}
```

### Districts

``` text
GET    /api/v1/admin/districts
POST   /api/v1/admin/districts
GET    /api/v1/admin/districts/{id}
PUT    /api/v1/admin/districts/{id}
DELETE /api/v1/admin/districts/{id}
```

### Downloads

``` text
GET    /api/v1/admin/downloads
POST   /api/v1/admin/downloads
GET    /api/v1/admin/downloads/{id}
PUT    /api/v1/admin/downloads/{id}
DELETE /api/v1/admin/downloads/{id}
```

### Order Circulars

``` text
GET    /api/v1/admin/order-circulars
POST   /api/v1/admin/order-circulars
GET    /api/v1/admin/order-circulars/{id}
PUT    /api/v1/admin/order-circulars/{id}
DELETE /api/v1/admin/order-circulars/{id}
POST   /api/v1/admin/order-circulars/{id}/publish
```

### Quick Links

``` text
GET    /api/v1/admin/quick-links
POST   /api/v1/admin/quick-links
GET    /api/v1/admin/quick-links/{id}
PUT    /api/v1/admin/quick-links/{id}
DELETE /api/v1/admin/quick-links/{id}
```

### Results

``` text
GET    /api/v1/admin/results
POST   /api/v1/admin/results
GET    /api/v1/admin/results/{id}
PUT    /api/v1/admin/results/{id}
DELETE /api/v1/admin/results/{id}
```

The exact fields MUST be derived from the repository.

## 13. File Upload API

Where existing admin modules support uploads, expose controlled
multipart endpoints.

Example:

``` text
POST /api/v1/admin/media/upload
```

Requirements:

-   authenticate
-   authorize
-   validate MIME type
-   validate extension
-   validate file size
-   generate safe filenames
-   prevent executable uploads
-   do not trust client filename
-   store outside executable paths where possible
-   return public URL only when appropriate
-   create thumbnails using existing safe mechanisms
-   never expose local filesystem paths

Example response:

``` json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "id": 123,
    "url": "https://example.com/uploads/example.jpg",
    "filename": "example.jpg"
  }
}
```

## 14. Authorization

Use the repository's existing `aauth`/group/permission model if that is
the active authorization system.

Map API actions to permissions such as:

``` text
news.view
news.create
news.edit
news.delete
news.publish

gallery.view
gallery.create
gallery.edit
gallery.delete

users.view
users.create
users.edit
users.delete

membership.view
membership.create
membership.verify
membership.approve
membership.reject
membership.report
```

Do not invent permission names if the existing system already has
equivalent permissions. Create a documented mapping.

## 15. Security

Implement:

### Rate limiting

At minimum protect:

-   OTP request
-   OTP verification
-   login/token endpoints
-   password/credential endpoints if exposed
-   sensitive state-changing endpoints

### CSRF

Determine whether CSRF is relevant to each API authentication mechanism.
Do not blindly copy browser-session CSRF behavior into bearer-token
APIs, but ensure browser/session endpoints remain protected.

### CORS

Allow only configured origins.

Never use unrestricted production CORS unless there is a documented
reason.

### Input validation

Validate:

-   IDs
-   strings
-   URLs
-   dates
-   enums/statuses
-   uploaded files
-   pagination
-   sorting
-   search/filter values

Whitelist sortable fields.

### SQL injection

Use the project's query builder/model parameterization.

Never concatenate untrusted input into SQL.

### Logging

Do not log:

-   OTP
-   passwords
-   access tokens
-   refresh tokens
-   authorization headers
-   secrets

Log safe audit information such as:

-   user ID
-   endpoint
-   action
-   timestamp
-   result
-   request ID
-   IP where appropriate

## 16. Pagination

Default:

``` text
page=1
per_page=20
```

Enforce a maximum such as:

``` text
per_page <= 100
```

unless the repository has a justified different requirement.

Response:

``` json
{
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 250,
    "last_page": 13
  }
}
```

## 17. Filtering and Sorting

Only expose filters that map to real business requirements.

Example:

``` text
GET /api/v1/news?page=1&per_page=20&search=education&year=2026
```

Never dynamically accept arbitrary SQL column names.

Whitelist:

``` text
sort=created_at
order=desc
```

only if these fields are explicitly allowed.

## 18. API Request ID

Add a request/correlation ID to API processing.

If the client sends:

``` http
X-Request-ID: abc123
```

validate it and reuse it when safe.

Otherwise generate one.

Return:

``` http
X-Request-ID: abc123
```

This helps debug Flutter/API issues.

## 19. Caching

Public read-only endpoints may use caching where safe:

-   home
-   news
-   office bearers
-   districts
-   galleries
-   downloads
-   quick links
-   contact

Do not cache personalized/admin/membership responses unless explicitly
designed.

Invalidate relevant cache after admin mutations.

## 20. Database Transactions

Use transactions for operations involving multiple writes, especially:

-   membership approval
-   membership rejection
-   membership verification
-   gallery + image operations
-   content + related records
-   user + role assignment

Rollback on failure.

Do not return success before the transaction commits.

## 21. API Route Organization

Add API routes in a clearly isolated section in the existing routing
configuration.

Preferred:

``` text
/api/v1/auth/...
/api/v1/news/...
/api/v1/gallery/...
/api/v1/admin/...
/api/v1/membership/...
```

Avoid collisions with existing website routes.

## 22. Flutter Contract Requirements

Every API response intended for Flutter must have:

-   stable field names
-   documented nullable fields
-   predictable types
-   ISO 8601 date/time strings
-   absolute media URLs where possible
-   pagination metadata
-   consistent error structure

Do not make Flutter depend on HTML or database field names
unnecessarily.

Example Flutter-safe object:

``` json
{
  "id": 10,
  "title": "Annual Conference",
  "description": "Description",
  "image_url": "https://example.com/uploads/a.jpg",
  "published_at": "2026-09-01T10:00:00+05:30"
}
```

## 23. OpenAPI Documentation

Create an OpenAPI 3.x specification covering all implemented API
endpoints.

Suggested location:

``` text
docs/api/openapi.yaml
```

It must document:

-   servers
-   authentication
-   endpoints
-   parameters
-   request bodies
-   response schemas
-   error schemas
-   pagination
-   file uploads
-   status codes

Do not document endpoints that do not exist.

## 24. Automated Tests

Create API tests for:

### Authentication

-   request OTP
-   invalid phone
-   OTP expiry
-   wrong OTP
-   maximum OTP attempts
-   resend cooldown
-   successful verification
-   token refresh
-   logout
-   revoked token
-   unauthorized access

### Public APIs

For every public resource:

-   success
-   empty dataset
-   invalid ID
-   pagination
-   filters
-   malformed parameters

### Admin APIs

For every CRUD resource:

-   unauthenticated
-   authenticated without permission
-   authorized GET
-   create
-   validation failure
-   update
-   delete
-   publish
-   invalid ID

### Membership

Test every actual state transition discovered in the repository.

### Uploads

Test:

-   valid image
-   invalid MIME
-   invalid extension
-   oversized file
-   malicious filename
-   unauthorized upload

## 25. Backward Compatibility

After implementation, run the existing website/admin test suite.

The following must continue to work:

-   public navigation
-   admin login
-   admin CRUD
-   membership workflows
-   existing AJAX
-   downloads
-   image uploads
-   gallery management

The API must not silently change existing HTML behavior.

## 26. API Versioning

Version 1 is:

``` text
/api/v1
```

Future breaking changes require:

``` text
/api/v2
```

Do not break v1 to introduce a new Flutter feature.

## 27. Environment Configuration

Add only required variables to `.env.example`.

Potential examples:

``` text
API_ENABLED=true

WHATSAPP_OTP_ENABLED=true
WHATSAPP_PROVIDER=
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=
WHATSAPP_TEMPLATE_NAME=
WHATSAPP_TEMPLATE_LANGUAGE=

API_ACCESS_TOKEN_TTL=
API_REFRESH_TOKEN_TTL=
API_OTP_TTL=
API_OTP_MAX_ATTEMPTS=
API_OTP_RESEND_SECONDS=

API_RATE_LIMIT=
API_ALLOWED_ORIGINS=
```

Use names consistent with the existing project's configuration
conventions.

Never commit actual credentials.

## 28. WhatsApp Provider Abstraction

Do not hardwire the whole API to one WhatsApp vendor.

Create an abstraction similar to:

``` text
WhatsAppProviderInterface
        │
        └── sendOtp(phone, otp)
```

Then implement the selected provider.

Provider failures must return a controlled API error.

Never expose provider credentials to Flutter.

## 29. Admin WhatsApp Authorization Rule

Successful OTP verification alone MUST NOT automatically grant
administrator access.

Flow:

``` text
Phone
  ↓
OTP
  ↓
Identity verification
  ↓
Existing user lookup
  ↓
Existing group/role/permission verification
  ↓
API session/token
  ↓
Admin endpoint authorization
```

A normal member must not become an admin merely because they can receive
an OTP.

If the existing system has a specific admin flag/group, use that exact
source of truth.

## 30. API Error Codes

Create stable machine-readable codes, including:

``` text
VALIDATION_ERROR
AUTHENTICATION_REQUIRED
INVALID_OTP
OTP_EXPIRED
OTP_RATE_LIMITED
ACCOUNT_NOT_FOUND
ACCOUNT_DISABLED
FORBIDDEN
NOT_FOUND
CONFLICT
INVALID_STATE_TRANSITION
FILE_INVALID
FILE_TOO_LARGE
PROVIDER_ERROR
RATE_LIMITED
INTERNAL_SERVER_ERROR
```

Do not change an established error code casually after Flutter
integration begins.

## 31. Implementation Order

The coding agent MUST implement in this order:

### Phase 1 --- Audit

1.  Inspect repository.
2.  Map controllers/models/tables.
3.  Map authentication.
4.  Map permissions.
5.  Map uploads.
6.  Map membership workflows.
7.  Document findings.

### Phase 2 --- API Foundation

1.  API routing.
2.  Base controller/helper.
3.  JSON response formatter.
4.  Error formatter.
5.  API request ID.
6.  authentication middleware/filter.
7.  authorization middleware/filter.
8.  pagination helper.
9.  validation conventions.

### Phase 3 --- Authentication

1.  WhatsApp provider interface.
2.  OTP generation.
3.  OTP storage.
4.  OTP expiry.
5.  OTP attempts.
6.  rate limits.
7.  verification.
8.  access token.
9.  refresh token.
10. logout/revocation.
11. `/me`.

### Phase 4 --- Public APIs

Implement:

1.  home
2.  flash news
3.  news
4.  organization
5.  districts
6.  former leaders
7.  order circulars
8.  downloads
9.  service corner
10. galleries
11. quick links
12. results
13. contact

### Phase 5 --- Membership APIs

Implement only after completely mapping the existing membership
workflow.

### Phase 6 --- Admin APIs

Implement CRUD and publishing endpoints according to existing
permissions.

### Phase 7 --- Media

Implement secure upload/delete/update behavior.

### Phase 8 --- Documentation

Generate/update:

``` text
docs/api/openapi.yaml
docs/api/API_README.md
```

### Phase 9 --- Tests

Run:

-   API tests
-   existing admin tests
-   existing frontend tests
-   static analysis if configured
-   PHP syntax checks

### Phase 10 --- Flutter Contract Verification

Verify that every Flutter-required screen has an API source.

Create a matrix:

  ---------------------------------------------------------------------------------------
  Flutter screen API                         Auth           Data complete  Tested
  -------------- --------------------------- -------------- -------------- --------------
  Home           `/api/v1/home`              Public         YES/NO         YES/NO

  News           `/api/v1/news`              Public         YES/NO         YES/NO

  Gallery        `/api/v1/galleries`         Public         YES/NO         YES/NO

  Admin          `/api/v1/admin/dashboard`   Admin          YES/NO         YES/NO
  dashboard                                                                

  Membership     `/api/v1/membership/...`    Member/Admin   YES/NO         YES/NO
  ---------------------------------------------------------------------------------------

## 32. Definition of Done

The API is complete only when:

-   [ ] `/api/v1` routes work.
-   [ ] JSON response format is consistent.
-   [ ] Authentication works.
-   [ ] WhatsApp OTP works with the configured provider.
-   [ ] OTP is rate limited and expires.
-   [ ] Tokens can be refreshed/revoked.
-   [ ] Admin authorization is enforced.
-   [ ] Public content endpoints work.
-   [ ] Membership endpoints preserve existing workflow.
-   [ ] Admin CRUD endpoints work.
-   [ ] Upload endpoints are secure.
-   [ ] Pagination works.
-   [ ] Validation works.
-   [ ] Errors are standardized.
-   [ ] OpenAPI documentation exists.
-   [ ] Automated tests exist.
-   [ ] Existing website tests still pass.
-   [ ] Existing admin tests still pass.
-   [ ] No credentials are committed.
-   [ ] No sensitive information is exposed.
-   [ ] Flutter can consume every required screen's data without HTML
    scraping.

## 33. Agent Behavior Requirements

The agent MUST:

-   inspect before editing
-   make small, reviewable changes
-   reuse existing business logic
-   avoid unnecessary dependencies
-   avoid database schema changes unless required
-   explain any required migration before applying it
-   never delete existing functionality merely to simplify API
    implementation
-   test after each major module
-   report files changed
-   report endpoints implemented
-   report tests executed
-   report remaining gaps

The agent MUST NOT:

-   invent tables
-   invent columns
-   invent permissions
-   invent membership statuses
-   bypass existing authorization
-   expose database records wholesale
-   expose passwords/hashes/tokens/OTP
-   return filesystem paths
-   use HTML scraping
-   hardcode provider credentials
-   disable security controls to make tests pass
-   replace existing web authentication without explicit requirement
-   declare completion when endpoints are only stubs

## 34. Final Agent Report

At completion produce:

``` text
REST API IMPLEMENTATION REPORT

Repository:
KPSTA

API Base:
 /api/v1

Implemented:
- Authentication: ...
- Public APIs: ...
- Membership APIs: ...
- Admin APIs: ...
- Media APIs: ...

Files created:
...

Files modified:
...

Database migrations:
...

Environment variables:
...

Tests:
...

OpenAPI:
...

Known limitations:
...

Flutter readiness:
...

Remaining TODO:
...
```

The agent must provide concrete evidence for each completed module
rather than simply saying "done".
