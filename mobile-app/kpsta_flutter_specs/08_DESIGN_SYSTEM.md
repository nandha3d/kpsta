# Flutter Design System & UX Rules

## Visual direction
Professional association/government-administration style with modern mobile polish. Avoid overly playful UI. Use strong typography, clear hierarchy, restrained cards, generous spacing and consistent status indicators.

## Color system
Do not hardcode colors throughout features. Centralize theme tokens. Derive primary/secondary colors from the association's existing branding assets after inspecting the current site. Provide light theme first; support dark theme only if current product requirement confirms it.

## Typography
- readable body text
- high-contrast headings
- numeric dashboard metrics use tabular-friendly font settings where available
- avoid all-caps long labels

## Components
Every repeated UI element must be a shared widget, not duplicated ad hoc.

## Forms
- label above field
- helper/error text below
- required indicator
- keyboard-aware scrolling
- save bar or floating save action for long forms
- unsaved changes guard

## Lists
On phones, replace desktop tables with cards/list rows but preserve all information and actions. For dense admin lists, use two-line rows and an overflow action sheet.

## Filters
Open complex filter sets in bottom sheets. Show active filters as removable chips on the list screen.

## File actions
Use a unified document row:
icon + title + date/category + status + open/download menu.

## Status
Published = positive status plus icon/text. Draft/unpublished = neutral/attention status plus icon/text. Never communicate state via color alone.

## Notifications
Use snackbars for immediate write confirmation, inline errors for form issues, and banners for persistent system/offline conditions.

## Accessibility
- semantic labels for icons
- dynamic text compatibility
- sufficient contrast
- screen reader order follows visual hierarchy
- avoid gestures as the only way to reach critical actions

## Performance
- paginate large district/member lists
- cache images
- cancel stale search requests
- avoid rebuilding large grids unnecessarily
- use isolates for large local parsing jobs such as CSV previews

## Navigation
Nested screens must preserve context. Example: Admin -> Order Circular -> General -> Category filtered -> Edit document -> Save -> return to filtered list with position preserved when possible.
