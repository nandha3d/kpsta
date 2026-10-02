# Backend API / Flutter Integration Specification

## Principle
The Flutter client must not depend on HTML scraping or browser-only AJAX fragments. Add stable JSON endpoints backed by existing domain logic.

## Recommended API groups
### Auth
`/api/v1/auth/whatsapp/request-otp`
`/api/v1/auth/whatsapp/verify-otp`
`/api/v1/auth/refresh`
`/api/v1/auth/logout`
`/api/v1/auth/me`
`/api/v1/auth/permissions`

### Public
`/api/v1/home`
`/api/v1/contact`
`/api/v1/services`
`/api/v1/services/{id}`
`/api/v1/memorandums`
`/api/v1/order-circulars?type=general|hse|vhse`
`/api/v1/downloads?type=...`
`/api/v1/former-leaders`
`/api/v1/office-bearers`
`/api/v1/adayapaka-sabham`
`/api/v1/galleries`
`/api/v1/galleries/{id}`
`/api/v1/news`
`/api/v1/districts`
`/api/v1/quick-links`
`/api/v1/results`
`/api/v1/privacy-policy`
`/api/v1/donations`

### Admin
Resource-oriented CRUD plus specialized actions. Minimum patterns:
GET collection
GET entity
POST create
PATCH/PUT update
DELETE delete
POST `/{id}/publish`
POST `/batch-delete`
POST `/upload`
DELETE `/files/{fileId}`
GET `/search` only where specialized search behavior is materially different

### Membership
Auth + dashboard + counts + teacher CRUD/reports + membership users + main + settings + WhatsNew.

## Pagination
Return:
```json
{
  "page": 1,
  "per_page": 20,
  "total": 123,
  "total_pages": 7,
  "has_next": true,
  "has_previous": false
}
```

## Validation errors
Prefer:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "heading": ["Heading is required"]
  }
}
```
Flutter maps each key to the matching field.

## File upload
Use multipart form-data. Response must return a stable file identifier, server filename/path abstraction, MIME, size and public/authorized URL where permitted.

## Rich text
Backend may store HTML. API should return either sanitized HTML or trusted content plus an explicit `content_type`.

## Date handling
Prefer ISO 8601 in JSON. Translate legacy display formats at the API adapter boundary, not throughout UI.

## Authorization
Return 401 for no/invalid authentication, 403 for authenticated-but-forbidden. Avoid leaking whether a protected record exists when the user has no permission.

## WhatsApp provider abstraction
Backend should expose a provider-independent auth service. Provider credentials remain server-side. Request OTP endpoint should not disclose whether a phone number exists in a way that enables account enumeration.

## Legacy compatibility
Do not break current web routes. Mobile API work should be additive until parity is proven.
