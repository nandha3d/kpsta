# Admin Content Modules — Detailed Page Specifications

## ADM-CONT-01 News List
Legacy: `/admin/news`

### UI
- title + item count
- search
- optional page selector
- add button
- table-style list adapted to cards on mobile
- per-item status, heading, publication state, date if returned
- item actions: edit, publish/unpublish, delete
- multi-select + batch delete if permission allows

### Form
Known browser tests prove the form uses:
- `heading`
- rich text `content` (Summernote in web)
- `publish` (Yes/No)

Mobile should use a rich text editor or structured editor compatible with backend HTML. Preserve HTML semantics. Validate required fields.

### Write behavior
Create -> validate -> save -> refresh list -> focus/select new item if useful.
Edit -> load server copy -> modify -> save.
Publish toggles current boolean only after server success.
Delete confirmation uses title.

## ADM-CONT-02 Flash News
Legacy: `/admin/flash_news`

List/form/publish/delete. Support menu type/section behavior returned by the backend. If flash news is home-facing, surface preview and priority/date if the model returns them.

## ADM-CONT-03 Slider
Legacy: `/admin/slider`

Repository implementation confirms:
- description required
- is_publish required
- show_on_home checkbox
- is_heading_bg checkbox
- optional heading_pages list when targeting individual pages
- position
- image upload (`jpg/jpeg/png`)
- 1920x621 crop/thumbnail behavior

### Mobile crop UX
Use image picker -> preview -> constrained 1920:621 crop area -> upload -> server thumbnail generation. Show crop result before save when possible.

## ADM-CONT-04 Service Corner
Legacy: `/admin/service_corner`

Methods include add/edit/publish/delete plus rules management:
- service list
- service create/edit
- publish status
- delete
- rules list/detail/edit/add/delete

The Flutter structure should make Rules a nested sub-resource inside the service detail admin page.

## ADM-CONT-05 Adayapaka Sabham
Legacy: `/admin/adayapaka_sabham`

Supports list, add/edit/update, publish, delete, batch delete, image/file upload/remove, thumbnail/cropping. Preserve URL-vs-file semantics when the backend supports both.

## ADM-CONT-06 Membership Documents/Content
Legacy: `/admin/membership`

Generic document/content CRUD with add/edit/update/publish/delete/file upload/remove. Use the common document form component.
