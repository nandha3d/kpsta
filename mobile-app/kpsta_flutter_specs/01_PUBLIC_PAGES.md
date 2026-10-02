# Public Pages — Flutter Specification

Implement every public route as a mobile screen. Legacy route aliases can converge into one Flutter destination.

## Screen inventory
| ID | Screen | Legacy route(s) | Core data/actions |
|---|---|---|---|
| PUB-01 | Home | `/`, `/index` | hero/slider, flash/latest news, service corner, key links/content |
| PUB-02 | Site Visitor/visitor stats | `/site_visitors` | visitor information if API exposes it; otherwise preserve navigation only when useful |
| PUB-03 | Contact | `/contact` | contact details + contact form submission |
| PUB-04 | Service Corner | `/service_corner` | service list/cards |
| PUB-05 | Service Corner Detail | `/service_corner_details/:id` | service detail, rules/details if returned |
| PUB-06 | Memorandums | `/memorandums` | grouped document list + open/download |
| PUB-07 | Order Circular — General | `/order-circular/general` | categories, search, grouped-by-month/year documents |
| PUB-08 | Order Circular — HSE | `/order-circular/hse` | same with HSE type |
| PUB-09 | Order Circular — VHSE | `/order-circular/vhse` | same with VHSE type |
| PUB-10 | Forms / Melakal | `/melakal`, `/download/forms` | documents, category/search/pagination |
| PUB-11 | Act & Rules | `/download/act_rules` | downloadable documents |
| PUB-12 | Softwares | `/download/softwares` | downloadable software/resources |
| PUB-13 | Fonts | `/download/fonts` | downloadable font resources |
| PUB-14 | Academic Corner | `/download/academic_corner` | academic documents |
| PUB-15 | Notice Posters | `/notice_poster` | poster documents/resources |
| PUB-16 | Official Outlook | `/official_outlook` | official outlook documents |
| PUB-17 | Former Leaders | `/former-leaders` | historical office bearer/person cards |
| PUB-18 | State Office Bearers | `/office_bearer` | grouped active state office bearers |
| PUB-19 | Adayapaka Sabham | `/adayapaka_sabham` | published content/resources |
| PUB-20 | Gallery Albums | `/gallery` | album grid/list |
| PUB-21 | Gallery Album Detail | `/gallery/:album` | images, fullscreen viewer |
| PUB-22 | News | `/news` | paginated/searchable news listing |
| PUB-23 | News pagination | `/news/:page` | same screen, page state |
| PUB-24 | District Directory | `/district` | grouped district office bearers |
| PUB-25 | Quick Links | `/quicklink`, `/online-links` | external/online links |
| PUB-26 | Results | `/results` | result links/resources |
| PUB-27 | Privacy Policy | `/privacy-policy` | legal content |
| PUB-28 | Donation | `/donation` | donation form and payment initiation |
| PUB-29 | Donation Payment | `/donation/pay` | payment state/redirect initiation |
| PUB-30 | Donation Payment Status | `/donation/payment-status` | polling/status resolution |
| PUB-31 | Donation Success | `/donation/success/:token` | success receipt/details |

## Public shell
Header should expose association identity, menu/search, and a persistent quick action area. Use a modal drawer for the full menu. Footer content should be converted into a compact mobile section with contact/legal/quick links.

## Home requirements
The homepage must preserve the meaning of website modules rather than rendering a generic news app. Prioritize:
1. hero slider/banners
2. flash news or urgent content
3. latest news
4. service corner highlights
5. prominent downloads/circular links
6. state office bearer highlights where present
7. quick links/online services
8. donation/contact actions

Slider content must support image, text/description, publish status, home visibility and optional heading-page targeting based on the admin data model.

## List/detail document UX
Document lists support search, category filtering, pagination, publication date, title/description, and file/link opening. PDFs should open using an in-app document viewer or external viewer after successful download. External URLs should open using a safe browser flow.

## Gallery UX
Album card shows cover image, album name and image count when API supplies it. Album detail uses a masonry/grid layout, tap-to-open fullscreen, swipe, zoom, share/download only when the backend/file policy permits.

## Office Bearer UX
Use person cards with image, designation/section, phone/email where public, and clear hierarchy. Never expose fields that the public backend does not return.

## News UX
List cards show heading/title, date, optional image, excerpt. Detail screen renders rich content safely. Preserve pagination state. Search if supported by backend; do not fake client-side search over a single page.

## Donation UX
Donation screen must clearly separate amount/details from payment progress. Do not mark successful until server payment status confirms success. Support retry and cancellation. Use idempotency key per payment attempt.

## Contact UX
Fields must match backend validation. Show inline field errors. Submit button transitions to progress and success state. Prevent accidental duplicate submission.

## Error/empty states
All public lists must show human-readable empty states; documents must distinguish "no documents" from "download failed".

## Deep linking
Support external links into news detail, gallery album, circular type/category and donation success where technically possible.
