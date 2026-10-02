# Admin Organization & Media — Detailed Specifications

## ADM-ORG-01 Office Bearers
Legacy: `/admin/office_bearer`

### List
- search/filter if supported
- grouped/section heading
- image/avatar
- name
- designation
- phone/email where admin can see
- publish state
- former/current when represented
- edit/delete/publish

### Form
Repository Playwright test confirms fields including:
- name
- designation
- phone
- email
- image

Also support other fields actually returned by the backend form, especially section heading, level, term/former flags. Agent must inspect `OfficeBearer.php` and view/model before finalizing payload.

## ADM-ORG-02 Designations
Nested under Office Bearers. Supports list/add/edit/update/delete where API exposes delete. Designation should be managed before assigning it to a person.

## ADM-ORG-03 Districts
Legacy: `/admin/district`

Supports district CRUD plus nested district office bearer management.

### District page
- district name/list
- publish state
- open district -> district office bearers

### District office bearer page
- list
- add/edit/update
- image
- designation
- publish
- delete/batch delete

The repository test proves `/admin/district/1` exposes district office bearer CRUD. Do not hardcode district 1; use route/entity ID.

## ADM-MEDIA-01 Gallery Albums
Legacy: `/admin/gallery`

Album list + create/edit/update/delete/publish.
Fields must be sourced from backend form/model. After album save, navigate to album detail.

## ADM-MEDIA-02 Gallery Album Detail / Images
Legacy nested routes `/admin/gallery/:album`

- image list/grid
- upload multiple or one at a time according to backend capability
- make cover
- edit image metadata
- update
- delete
- upload progress
- remove failed/pending file

### Image viewer
Admin preview supports fullscreen and orientation handling.

## ADM-MEDIA-03 Reaction Gallery
Legacy: `/admin/reaction_gallery`

CRUD + image upload + publish + URL-related validation where backend uses it. Use a compact card grid.

## ADM-MEDIA-04 Slider
See content spec; visually this is media/content, but keep navigation under Content if that matches the final information architecture.
