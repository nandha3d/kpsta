# KPSTA — Master Engineering Reference & Architecture Rulebook

**Status: NORMATIVE & BINDING.**
This document defines the strict engineering procedure, architectural invariants, and agent execution standards that MUST be followed when modifying, extending, auditing, testing, or documenting the KPSTA project.
Rules are numbered (`API-1`, `AUTH-1`, `DATA-1`, `RULE 01`, …) so they can be cited in code reviews, tests, and design decisions: *"rejected, violates API-1"*.

**Applies To:** All AI coding agents, autonomous agents, developers, code-generation tools, and automated implementation workflows working on this repository.

**Source of Truth:**
- Backend: CodeIgniter 4 (`app/`)
- Mobile App: Flutter application (`mobile-app/`)
- Unified API: `/api/v1/*`
- Feature Specs: `mobile-app/kpsta_flutter_specs/`

---

# PART I — CORE ARCHITECTURE & NORMATIVE RULES

## 0. Governance & Document Rules

| Attribute | Specification |
|---|---|
| **Precedence** | This file > `KPSTA_REST_API_AGENT_SPEC.md` > `00_MASTER_SPEC.md` > Readmes. Where they disagree, this file wins. |
| **Source of truth** | The code is the source of truth for current runtime behavior; this file is the source of truth for **what the behavior MUST be**. If code contradicts a rule here, the code is a bug — fix the code, or amend this file with explicit reason. |
| **Before writing code** | Read this document before implementing any API endpoint, database change, or mobile screen. |
| **Changing this file** | Allowed and expected when architectural rules evolve. Amend it in the same commit as the change it describes. |

### Rules About the Rules
- **DOC-1** — A change that alters an invariant or rule here MUST update this file in the same commit.
- **DOC-2** — A bug fixed twice is a rule that was never written down. Document new invariants discovered during debugging here.
- **DOC-3** — Specifications under `mobile-app/kpsta_flutter_specs/` describe *what features to build*; this file describes *how they must be built*.

---

## 1. Core Architecture & Unified API Principle

### API-1: Unified API — Single Source of Truth (Zero Discrepancy Invariant)
**NON-NEGOTIABLE:** Both the Web Application and the Flutter Mobile Application MUST consume the exact same `/api/v1/*` REST API for all shared data, calculations, validation rules, and mutations.
- **Zero Discrepancy:** There shall be NO divergence in business logic, calculation formulas, data structures, field names, or validation rules between Web and Mobile.
- **Presentation Separation:** Web views (server-rendered or client-side components) and Flutter mobile screens are presentation layers for the same underlying domain API.
- **No Private Backdoors:** Mobile must never scrape HTML; Web must not rely on proprietary un-versioned hidden endpoints when standard `/api/v1/*` endpoints exist.
- **Data Parity:** Any new field, filter, or status added for the web app must simultaneously be supported in the API response for mobile, and vice-versa.

### API-2: Versioning & Base URL
- All mobile and modern web API endpoints MUST reside under `/api/v1/`.
- Unversioned endpoints (e.g. `/api/...` without version) are strictly prohibited.
- Breaking changes require a new version `/api/v2/`.

### API-3: Standard JSON Response Envelope
Every API response MUST use the uniform envelope format:
```json
{
  "success": true,
  "message": "Resource retrieved successfully",
  "data": {},
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 100,
    "total_pages": 5
  },
  "errors": null,
  "code": null
}
```
For errors:
```json
{
  "success": false,
  "message": "Validation failed",
  "data": null,
  "meta": null,
  "errors": {
    "title": ["Title is required"]
  },
  "code": "VALIDATION_ERROR"
}
```

### API-4: HTTP Status Codes
Endpoints must return standard HTTP status codes:
- `200 OK`: Successful retrieval or synchronous update
- `201 Created`: Successful resource creation
- `204 No Content`: Successful deletion
- `400 Bad Request`: Malformed payload or unparseable input
- `401 Unauthorized`: Unauthenticated / missing or expired token
- `403 Forbidden`: Authenticated user lacks permission for action
- `404 Not Found`: Resource does not exist
- `422 Unprocessable Entity`: Form/input validation failure
- `429 Too Many Requests`: Rate limit exceeded
- `500 Internal Server Error`: Unhandled server exception (stack trace never leaked)

---

## 2. Authentication & Authorization

### AUTH-1: WhatsApp OTP Authentication
- The primary authentication method for the Mobile/Admin API is **WhatsApp OTP**.
- Flow: Phone number submission -> Secure OTP generation & delivery via configured WhatsApp provider -> Verification -> Issue short-lived Access Token + Refresh Token.
- **Security:** Plaintext OTPs, JWT secrets, and provider credentials must never be committed to source control or logged in application logs.
- **Rate Limiting:** OTP requests must be throttled per IP, device, and phone number (cooldown period of 60s, expiry of 300s, max 3 attempts).

### AUTH-2: Server-Authoritative Permissions
- OTP verification alone DOES NOT grant administrator privileges. The system must verify the user's role and group permissions using the existing `aauth` authorization database.
- A regular user receiving an OTP can only access authorized public or member resources; admin endpoints strictly enforce `aauth` group and menu authorization.

### AUTH-3: Secure Session Management
- Mobile API issues Bearer tokens with clear TTL.
- Token refresh rotates or verifies validity; logout explicitly invalidates the active refresh session in `api_tokens`.

---

## 3. Data Integrity & Validation

### DATA-1: Field Consistency & Types
- Field names in JSON responses must use consistent `snake_case`.
- Dates and timestamps must be formatted as ISO 8601 strings (`YYYY-MM-DDTHH:mm:ss+05:30`) or `YYYY-MM-DD` for date-only values.
- IDs must be integers or UUIDs as defined in MySQL schema, never stringified integers randomly.
- Booleans must be strict JSON booleans (`true` / `false`), not `"1"` / `"0"` strings in API responses.

### DATA-2: Input Validation & Sanitization
- All client-provided input must be validated server-side using CodeIgniter validation before persisting.
- Strip dangerous HTML/scripts from public-facing rich text fields; preserve safe formatting.

### DATA-3: Database Transactions
- Any multi-step write operation (e.g., membership approval, gallery album + photos creation, user + role assignment) MUST run inside a database transaction (`$db->transBegin()`, `$db->transCommit()`, `$db->transRollback()`).

---

## 4. File Upload & Media Handling

### FILE-1: Secure Media Management
- File uploads must validate MIME type, file extension, and maximum size server-side.
- File names must be generated using cryptographic hashes (e.g. `md5(uniqid()) . '.' . $ext`) to avoid filesystem traversal attacks and filename collisions.
- Filesystem server paths must NEVER be returned in API responses; only full public URLs (e.g. `https://domain.com/uploads/...`) or relative web paths.

---

## 5. Backward Compatibility & Existing Systems

### LEG-1: Web Application Preservation
- Adding `/api/v1` routes and controllers must not break or alter any existing server-rendered HTML views, routes, controllers, or legacy AJAX workflows.
- Web controllers may gradually be refactored to consume the internal API services or models, ensuring 100% feature parity without breaking existing bookmarks or web sessions.

---

## 6. Directory Layout & Conventions

```text
KPSTA/
├── ENGINEERING_REFERENCE.md      # This Master Rulebook
├── app/
│   ├── Config/
│   │   ├── Routes.php            # Contains isolated /api/v1 route group
│   │   └── Filters.php           # Registers api-auth and api-cors
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── V1/               # All API v1 REST Controllers
│   │   │       ├── BaseApiController.php
│   │   │       ├── AuthController.php
│   │   │       ├── HomeController.php
│   │   │       ├── NewsController.php
│   │   │       ├── CircularController.php
│   │   │       ├── DownloadController.php
│   │   │       ├── GalleryController.php
│   │   │       ├── OrganizationController.php
│   │   │       ├── ServiceController.php
│   │   │       ├── LinkController.php
│   │   │       ├── ContactController.php
│   │   │       ├── DonationController.php
│   │   │       ├── Admin/AdminController.php
│   │   │       └── Membership/MembershipApiController.php
│   ├── Filters/
│   │   ├── ApiAuthFilter.php     # Bearer token & permission filter
│   │   └── CorsFilter.php        # Controlled CORS filter
│   └── Services/
│       └── Api/                  # WhatsApp, OTP, and AuthService
└── mobile-app/                   # Flutter Application
    ├── kpsta_flutter_specs/      # Detailed screen and QA specs
    └── lib/                      # Flutter source consuming /api/v1
```

---

# PART II — STRICT PROCEDURAL RULES & AGENT GOVERNANCE

## 7. Purpose & Engineering Principle

The primary objective is:
> **Make the smallest correct change that fully satisfies the requirement while preserving all existing working behavior.**

The agent MUST prioritize:
1. Correctness
2. Existing business behavior
3. Security
4. Data integrity
5. Backward compatibility
6. Testability
7. Maintainability
8. Performance
9. UI/UX quality
10. Implementation speed

Speed MUST NOT override correctness or safety.

---

## 8. Authority Hierarchy

When multiple instructions exist, apply this order:
```text
1. Repository reality (active database schema, routes, models)
2. This Engineering Reference (ENGINEERING_REFERENCE.md)
3. Master project specification (00_MASTER_SPEC.md)
4. Module-specific specification (e.g., KPSTA_REST_API_AGENT_SPEC.md, PUB-*, ADM-*, MEM-*)
5. API specification / OpenAPI
6. Flutter/UI specification
7. QA/acceptance specification
8. Agent assumptions
```
*Note: "Repository reality" does NOT mean blindly preserving an obvious security vulnerability. If the repository conflicts with a requirement, inspect the code, preserve existing behavior unless the requirement explicitly changes it, fix security, and document the conflict.*

---

## 9. Golden Rule — Never Guess

The agent MUST NOT guess:
- database table names or column names
- model names or controller methods
- routes and permissions
- roles and membership statuses
- business rules and state machines
- API response fields
- authentication and upload behavior

If information is unknown:
```text
INSPECT → VERIFY → IMPLEMENT
```
Never:
```text
ASSUME → IMPLEMENT
```

---

## 10. Repository Source of Truth for Existing Behavior

Before modifying an existing feature, inspect the full chain:
```text
Route → Controller → Model / Service → Database → View / API response → Tests
```
Do not inspect only the controller. A controller may contain only part of the business logic.

---

## 11. Mandatory 10-Step Workflow

Every non-trivial task MUST follow:
```text
STEP 1  — Understand the Request
STEP 2  — Inspect Existing Code
STEP 3  — Map Dependencies
STEP 4  — Plan Implementation
STEP 5  — Implement Changes
STEP 6  — Write & Run Tests
STEP 7  — Review & Self-Audit
STEP 8  — Regression Test
STEP 9  — Document Changes
STEP 10 — Report with Evidence
```
No major implementation should jump directly from request to code.

---

## 12. Change Scope Control & No Destructive Changes

The agent MUST follow the principle:
> **Change only what is necessary.**

Do NOT:
- rewrite unrelated controllers
- reformat entire directories
- rename unrelated files
- upgrade dependencies without justification
- redesign the database unnecessarily
- replace existing authentication unnecessarily

For destructive changes (dropping tables/columns, deleting routes, removing workflows):
```text
STOP → Explain impact → Verify dependencies → Create rollback strategy → Implement → Test
```

---

## 13. Database Schema & Migration Rules

Before changing the database structure:
1. Inspect existing schema and current migrations.
2. Inspect models, queries, and repository references.
3. Every new column MUST have a documented purpose.
4. Every migration MUST be reversible where practical.
5. Never silently modify production data.

---

## 14. Data Integrity & Transactions

For multi-step database operations:
```text
BEGIN TRANSACTION
    step 1 (e.g. create teacher record)
    step 2 (e.g. initialize teacher process state)
    step 3 (e.g. record audit log)
COMMIT
```
On any failure:
```text
ROLLBACK
```
Never report success when only part of a transaction succeeded. Apply this strictly to membership approval, donations, user role changes, and file uploads.

---

## 15. Authentication vs. Authorization Distinction

```text
Authentication = Who are you? (Identity verified via WhatsApp OTP)
Authorization  = What are you allowed to do? (Role, group, menu permissions via Aauth)
```
Successful login MUST NOT automatically grant administrator access.
Never grant privileges based solely on phone number ownership.

---

## 16. OTP Security Standards

OTP implementation MUST include:
- Cryptographically secure 6-digit OTP generation
- Expiration (5 minutes / 300s)
- Maximum attempts (3 attempts)
- Resend cooldown (60 seconds)
- Rate limiting per IP and phone
- Hash-based storage (`password_hash`) — never store plaintext OTPs
- Provider failure handling
- Audit logging without OTP values (Never log `OTP=123456`)
- Never return the OTP in an API response in production

---

## 17. Auth Token Rules

Tokens MUST:
- expire (short-lived access tokens, managed refresh tokens)
- be revocable on logout
- be protected and stored securely (`flutter_secure_storage` on mobile)
- never be logged or exposed in error messages
- rotate on refresh where required

---

## 18. Granular Authorization Rules

Every protected action MUST verify specific permissions.
Never rely solely on `if ($isAdmin)`.
Preferred flow:
```text
User → Role / Group → Permission → Action (e.g., news.create, circulars.edit, membership.approve)
```

---

## 19. API Design & Endpoint Rules

- All new mobile endpoints MUST use `/api/v1/`.
- Never expose HTML pages or server-rendered views as an API.
- Flutter MUST NOT scrape HTML, DOM, or server-rendered tables.
- The API must return structured JSON conforming to `API-3`.

---

## 20. API Security Standards

Every API endpoint MUST consider:
- Authentication & Authorization checks
- Input validation (CodeIgniter validation rules)
- Rate limiting on public and auth endpoints
- CORS configuration
- Parameterized SQL queries (prevent injection)
- Object-level authorization (prevent IDOR)
- Error sanitization (no internal stack traces exposed to client)

---

## 21. Object-Level Authorization (IDOR Prevention)

Do not assume `user_has_permission('member.view')` means the user can view every member across all districts.
Verify ownership, school assignment, or district scope where applicable.

---

## 22. Server-Side Input Validation

Every external input MUST be validated on the server.
Validate IDs, strings, numbers, dates, enums, phone numbers, pagination limits, and file types.
Never trust client input from Flutter or web forms.

---

## 23. SQL Safety

Never build SQL queries by concatenating untrusted input.
Use CodeIgniter 4 Query Builder or prepared/parameterized statements exclusively.
Sorting and filtering field names MUST be explicitly whitelisted.

---

## 24. File Upload Security Standards

- Validate file extension, MIME type, and file size.
- Generate random cryptographic filenames (`md5(uniqid()) . '.' . $ext`).
- Prevent executable or script uploads (`.php`, `.exe`, `.sh`).
- Store files in designated upload directories outside application code.
- Return public URLs, never internal filesystem server paths (`C:\...`).

---

## 25. Flutter Architecture & Implementation Rules

Flutter implementation MUST:
- Use typed models with robust JSON parsing.
- Handle loading, empty, error, unauthorized, and offline states for every screen.
- Support retry actions on network/server errors.
- Validate user input before submission.
- Centralize API base URLs via environment configuration (`ApiConstants.baseUrl`).
- Store tokens securely using `flutter_secure_storage`.
- Use Riverpod for state management and GoRouter for declarative routing with guards.

---

## 26. Error Handling — No Silent Swallowing

Never silently swallow errors:
```dart
// BAD
try { await api.call(); } catch (_) {}
```
Correct behavior:
```text
Catch → Classify → Log safe diagnostic info → Show friendly UI message → Provide Retry action
```

---

## 27. Safe Logging & Credential Redaction

Never log:
- Passwords or password hashes
- Plaintext OTPs
- Access or refresh tokens
- Authorization headers (`Bearer ...`)
- API keys and provider secrets
- Full sensitive personal data

---

## 28. Configuration & Environment Security

Environment-specific values MUST NOT be hardcoded in source code:
- Database credentials
- WhatsApp provider credentials / tokens
- JWT signing secrets
- Allowed CORS origins
Use `.env` and `app/Config/` on backend, and compile-time / runtime environment variables on mobile. Never commit live production credentials to Git.

---

## 29. Dependency Governance Rule

Before adding any dependency:
1. Check existing dependencies (`composer.json` or `pubspec.yaml`).
2. Verify if existing libraries or framework features already fulfill the requirement.
3. Check compatibility with PHP 8.2+ / CodeIgniter 4.7+ and Flutter 3.44+ / Dart 3.12+.
4. Add only if strictly justified.

---

## 30. Version Control & Isolation Rule

Every meaningful change MUST be logically isolated:
- Do not mix feature additions with unrelated formatting or refactoring.
- Maintain clean, descriptive commit messages.

---

## 31. Test-First Engineering Gate

Before declaring any feature complete:
```text
Implementation → Unit / Widget Tests → Integration Tests → Regression Check → Verification
```
A feature is NOT complete simply because "it compiles" or "the page renders."

---

## 32. Test Requirements Checklist

Every module must test:
- **Happy Path:** Valid inputs produce expected outcomes.
- **Validation:** Missing, boundary, or malformed inputs return 422 with clear messages.
- **Authorization:** Unauthenticated requests return 401; unauthorized requests return 403.
- **Failure Resilience:** Database or provider failures return clean 500/503 envelopes without crashes.
- **Security:** SQL injection, IDOR, and rate limiting edge cases tested.

---

## 33. Regression Gates

After any change:
- Run existing CodeIgniter test suite (`tests/test_api_v1.php`).
- Run Flutter test suite (`flutter test`).
- Verify existing web application pages continue to render normally without regressions (`LEG-1`).

---

## 34. UI/UX Engineering Rules

- Thumb-friendly controls: minimum 44–48 dp touch targets.
- Clear visual hierarchy using KPSTA design tokens (`AppColors`).
- Consistent status indicators: Published = positive (green) + check icon; Draft = warning (amber) + clock/pending icon.
- Skeleton loaders or loading spinners rather than empty blank views.
- Confirmation dialogs for all destructive actions stating the specific entity name.

---

## 35. No Placeholder Completion Rule

The agent MUST NOT declare completion with:
```text
TODO / Coming Soon / Placeholder / Mock data / Fake API
```
Production paths MUST consume real API endpoints and persist to real database tables.

---

## 36. Mock Data Containment Rule

Mock data is permitted ONLY for isolated unit tests or early UI prototyping.
Mock data MUST NEVER be deployed into production code or serve as a silent fallback when an API call fails.

---

## 37. Performance & Query Auditing

- Avoid `SELECT *` on large tables without pagination.
- Avoid N+1 queries by joining related tables or eager loading.
- Return thumbnail/image URLs rather than embedded base64 blobs.
- Implement debounce on search fields to avoid flooding the API.

---

## 38. Security Over Convenience

Never weaken security because "testing is difficult" or "Flutter needs it."
Never disable CORS, token verification, or role checks as a temporary shortcut.

---

## 39. No Security by Obscurity

Hidden URLs, unguessable route names, and client-only secrets do NOT constitute security.
Authorization MUST be strictly enforced server-side on every request.

---

## 40. Zero Client-Side Trust Invariant

Never trust client-supplied:
- `role` or `is_admin` flags
- `status` or approval transitions
- `user_id` or ownership fields
All sensitive state transitions must be derived from the authenticated session and validated server-side.

---

## 41. Business State Machines & Workflow Transitions

Features with state machines (e.g. Teacher Membership: `PENDING` -> `CONFIRMED` -> `VERIFIED` -> `APPROVED`) must enforce legal transitions server-side:
- Arbitrary status updates (e.g. jumping from Unverified straight to Approved) must be blocked unless the user holds the requisite state-level approval role.

---

## 42. Membership Domain Rules

Membership is a critical operational system.
- Teacher records join `teacher_details` and `teacher_process`.
- School confirmation, sub-district verification, and state executive approval require audit trails.
- Never simplify or bypass membership verification logic for convenience.

---

## 43. Admin Portal & API Coexistence

The `/api/v1` REST API operates as the unified backend for the mobile app and modern interfaces while existing web admin controllers remain functional.
Both share the same database tables, models, and Aauth permission systems.

---

## 44. WhatsApp Provider Abstraction

WhatsApp integration MUST use provider abstraction (`WhatsAppProviderInterface`):
- Allows swapping between providers (Twilio, Gupshup, Meta Cloud API, local dev logger) without modifying authentication or OTP logic.

---

## 45. External Service Failure & Fault Tolerance

If an external provider (WhatsApp webhook, SMS gateway, payment gateway) is unreachable:
- Return a controlled error response (`503 Service Unavailable` or `500`).
- Do not leak third-party credentials or API exceptions to the client.
- Avoid leaving orphaned database records.

---

## 46. Cross-Document Consistency

All specification files under `mobile-app/kpsta_flutter_specs/` are part of one unified engineering system.
Changes to authentication, models, or APIs must be reflected across related spec documents and OpenAPI schemas.

---

## 47. Stop Conditions & Blocker Reporting Format

The agent MUST stop and request clarification when:
- Required database structure or column meanings cannot be verified.
- Business rules or state transitions conflict.
- Destructive schema modifications are requested without migration approval.

Blocker report format:
```text
BLOCKER
Area: <Module / Feature>
Problem: <Clear concise explanation>
Evidence: <File path, line number, or schema details>
Impact: <What cannot safely proceed>
Required Decision: <Choice A vs Choice B>
```

---

## 48. Evidence-Based Completion Standard

Completion claims MUST provide concrete evidence:
- Routes and controllers created
- Tests executed and pass rate
- Regression status
- Known limitations or environment prerequisites

---

## 49. The 20 Absolute Rules

```text
RULE 01: NEVER GUESS WHEN THE REPOSITORY CAN BE INSPECTED.
RULE 02: NEVER BREAK EXISTING FUNCTIONALITY TO IMPLEMENT A NEW FEATURE.
RULE 03: NEVER BYPASS AUTHENTICATION OR AUTHORIZATION.
RULE 04: NEVER TRUST CLIENT-SUPPLIED SECURITY OR BUSINESS STATE.
RULE 05: NEVER COMMIT SECRETS OR CREDENTIALS TO SOURCE CONTROL.
RULE 06: NEVER EXPOSE PASSWORDS, HASHES, OTPs, TOKENS, OR SECRETS IN LOGS OR RESPONSES.
RULE 07: NEVER CREATE DATABASE STRUCTURE WITHOUT INSPECTING EXISTING SCHEMA.
RULE 08: NEVER DECLARE SUCCESS WITHOUT RUNNING AUTOMATED TESTS.
RULE 09: NEVER USE MOCK DATA AS A SILENT PRODUCTION FALLBACK.
RULE 10: NEVER MAKE DESTRUCTIVE CHANGES WITHOUT IMPACT ANALYSIS AND ROLLBACK PLANS.
RULE 11: NEVER IMPLEMENT ONLY THE UI WHEN THE BACKEND CONTRACT DOES NOT EXIST.
RULE 12: NEVER IMPLEMENT ONLY THE API WHEN EXISTING BUSINESS RULES ARE NOT UNDERSTOOD.
RULE 13: NEVER IGNORE RELATED SPECIFICATION FILES.
RULE 14: WHEN UNCERTAIN ABOUT BUSINESS BEHAVIOR, STOP AND REPORT THE CONFLICT.
RULE 15: BUSINESS DATA AND PERMISSIONS MUST REMAIN 100% CONSISTENT ACROSS WEB, API, AND FLUTTER.
RULE 16: SECURITY MUST NEVER BE DISABLED TO MAKE A TEST OR FEATURE PASS.
RULE 17: A PASSING BUILD IS NOT PROOF OF A CORRECT IMPLEMENTATION.
RULE 18: A WORKING SCREEN IS NOT PROOF OF A COMPLETE FEATURE.
RULE 19: A WORKING API RESPONSE IS NOT PROOF OF CORRECT AUTHORIZATION.
RULE 20: COMPLETION REQUIRES CONCRETE EVIDENCE.
```

---

## 50. Engineering Mantra & Final Agent Principle

```text
INSPECT → UNDERSTAND → MAP → PLAN → IMPLEMENT → VALIDATE → TEST → REGRESSION CHECK → DOCUMENT → REPORT
```
Never:
```text
GUESS → CODE → CLAIM DONE
```

**Correctness over speed.**
**Evidence over assumption.**
**Compatibility over unnecessary rewrites.**
**Security over convenience.**
**Tests over claims.**
**Explicit decisions over silent guesses.**
