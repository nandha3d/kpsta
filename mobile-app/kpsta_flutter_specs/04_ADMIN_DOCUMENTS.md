# Admin Document Modules — Detailed Specifications

## Common document entity
Where the backend shares document semantics, represent a common model:
- id
- description/title
- date where applicable
- category_id/category name
- content/type/menu type
- upload type: URL or uploaded file
- path/url
- file name/internal identifier if exposed
- publish state
- created/updated timestamps where exposed

Never assume every document module has every field; feature adapters must map the actual API response.

## ADM-DOC-01 Order Circular
Routes cover:
- `/admin/order-circular`
- categories list/add/edit/update
- per-type routes for general/HSE/VHSE
- add/edit/update/delete/publish/search
- fileupload/fileremove
- batch delete

### UX
Order Circular landing screen has type tabs/cards: General, HSE, VHSE. Each type screen has category filter + search + paginated list. Category management is a separate admin page or modal depending on permission.

### Form
- date
- description
- upload type URL/file
- URL or file field according to upload type
- category
- publish state where backend requires it

Keep draft values when file upload is in progress. A failed upload must not silently clear the form.

## ADM-DOC-02 Downloads
The `Download` controller supports multiple menu types through one implementation:
- forms
- act_rules
- softwares
- fonts
- academic_corner
- notice_poster
- official_outlook
- melakal

### Navigation
Create one Downloads management area with a menu-type selector. Each type gets its own list state, filters and CRUD.

### Categories
Download category management has add/edit/update operations. Some types use dynamic category selection (web tests show Select2/tag-like behavior for Melakal). Mobile uses a searchable multi-select/single-select appropriate to actual cardinality returned by API.

### File upload
Support picker, progress, removal and server filename/id. Display file type and size. Validate client-side for better UX but trust server validation.

## ADM-DOC-03 Notice Poster / Official Outlook / Melakal
These are specialized views over the Download controller. The Flutter app should make the specialization obvious in navigation while sharing implementation components.

## ADM-DOC-04 Membership Admin
Supports the same document CRUD conventions under `/admin/membership`.
