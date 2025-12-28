# WCAG AA Accessibility Audit

**Theme:** nyu-lib
**Date:** 2025-12-27
**Standard:** WCAG 2.1 Level AA

---

## Overview

This document outlines accessibility issues identified in the nyu-lib Grav theme and tracks remediation progress.

---

## Critical Issues

### 1. Missing Skip Link
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.4.1 Bypass Blocks (Level A)
**Location:** `templates/partials/base.html.twig`

**Issue:**
No skip-to-content link exists for keyboard users to bypass the navigation and jump directly to main content.

**Impact:**
Keyboard and screen reader users must tab through all navigation items on every page load.

**Solution:**
Added visually hidden skip link at line 32 that becomes visible on focus. Uses `sr-only focus:not-sr-only` pattern.

---

### 2. Mobile Menu Buttons Missing Accessible Labels
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 4.1.2 Name, Role, Value (Level A)
**Location:** `templates/partials/header.html.twig`

**Issue:**
The `#toggleOpen` and `#toggleClose` buttons contain only SVG icons with no accessible text or aria-label.

**Impact:**
Screen readers announce these as "button" with no indication of their purpose.

**Solution:**
Added `aria-label` to both buttons, `aria-expanded` and `aria-controls` to toggleOpen, `aria-hidden="true"` to decorative SVGs. Also added focus ring styles.

---

### 3. Navigation Lacks Semantic Landmark
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.3.1 Info and Relationships (Level A)
**Location:** `templates/partials/header.html.twig`

**Issue:**
The navigation `<ul>` is not wrapped in a `<nav>` element.

**Impact:**
Screen reader users cannot easily identify or jump to the navigation region.

**Solution:**
Replaced `<div>` wrapper with `<nav aria-label="Main navigation">`. Also improved the logo alt text to be more descriptive.

---

### 4. Accordion Uses Inaccessible Pattern
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.1.1 Keyboard (Level A), 4.1.2 Name, Role, Value (Level A)
**Location:** `css/custom/accordion.css`

**Issue:**
Accordion uses CSS checkbox hack with `display: none` on inputs, making them completely inaccessible to keyboard users and assistive technology.

**Impact:**
- Keyboard users cannot operate accordions
- Screen readers cannot detect accordion state
- No programmatic indication of expanded/collapsed state

**Solution:**
Completely rewrote accordion.css with accessible patterns:
- New `.accordion`, `.accordion-item`, `.accordion-trigger`, `.accordion-panel` classes
- Usage documentation for Alpine.js x-collapse pattern with proper ARIA
- Support for native `<details>`/`<summary>` elements (inherently accessible)
- Legacy `.accordion-wrapper` preserved for backwards compatibility with deprecation note

---

### 5. Hero Background Image Incorrect Alt Text
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.1.1 Non-text Content (Level A)
**Location:** `templates/partials/base.html.twig`

**Issue:**
The hero background image uses `alt="hero-bg"` which is not meaningful. Decorative images should have empty alt text.

**Impact:**
Screen readers announce "hero-bg" which provides no useful information and is distracting.

**Solution:**
Changed to `alt=""` and added `role="presentation"` to properly mark as decorative.

---

## High Priority Issues

### 6. Focus Indicators Removed on Form Inputs
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.4.7 Focus Visible (Level AA)
**Location:** `css/custom/forms.css`

**Issue:**
Focus styles include `focus:ring-transparent focus:outline-hidden focus:shadow-none` which removes visible focus indication.

**Impact:**
Keyboard users cannot see which form element is currently focused.

**Solution:**
Replaced with visible focus styles across all form elements:
- Form inputs: `ring-2 ring-primary-500 ring-offset-2`
- Buttons: Same ring style with proper offset
- Custom checkboxes: Changed from `hidden` to `sr-only` + focus ring on pseudo-element
- Radio buttons: Added focus ring styling
- Also improved `.button` class in `typography.css`

---

### 7. Insufficient Color Contrast
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.4.3 Contrast (Minimum) (Level AA)
**Locations:**
- `text-gray-500` on light backgrounds
- `text-gray-400` for icons and secondary labels
- `opacity-50` on disabled elements

**Issue:**
Several text colors may not meet the 4.5:1 contrast ratio requirement for normal text.

**Impact:**
Users with low vision may have difficulty reading content.

**Solution:**
Updated across multiple templates:
- `content-title.html.twig`: Changed subtitle from `text-gray-500` to `text-gray-600`
- `post-card.html.twig`: Changed summary from `text-gray-500` to `text-gray-600`, tags from `text-gray-600` to `text-gray-700`
- `article-pagination.html.twig`: Changed label from `text-gray-500` to `text-gray-600`, icons from `text-gray-400` to `text-gray-500`
- `pagination.html.twig`: Updated disabled state styling for proper contrast
- `taxonomylist.html.twig`: Updated tag colors from `text-gray-600` to `text-gray-700`

---

### 8. Pagination Icons Missing Accessible Text
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.1.1 Non-text Content (Level A)
**Location:** `templates/partials/pagination.html.twig`

**Issue:**
Previous/Next navigation icons have no accessible text.

**Impact:**
Screen readers cannot convey the purpose of these navigation controls.

**Solution:**
Completely rewrote pagination template with full accessibility:
- Added `<nav aria-label="Pagination">` wrapper
- Previous/Next links have `aria-label` and `<span class="sr-only">` text
- Icons marked with `aria-hidden="true"`
- Disabled states have `aria-disabled="true"`
- Current page marked with `aria-current="page"`
- Page links have `aria-label="Go to page X"`
- Improved focus ring visibility on links

---

### 9. Mobile Menu Keyboard Accessibility
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.1.2 No Keyboard Trap (Level A)
**Location:** `templates/partials/header.html.twig`, `js/components/header-sticky.js`

**Issue:**
Mobile menu implementation lacks:
- Focus trapping when menu is open
- Escape key to close menu
- Focus return to trigger button on close

**Impact:**
Keyboard users may become trapped or lose their place in the document.

**Solution:**
Completely rewrote header using Alpine.js for full accessibility:
- Changed from `<section>` to semantic `<header>` element
- Used Alpine.js `x-trap.noscroll.inert` for proper focus trapping
- Added `@keydown.escape.window` to close menu
- Mobile menu is now a proper dialog with `role="dialog"` and `aria-modal="true"`
- Animated transitions with `x-transition`
- Click on backdrop closes menu
- Dynamic `aria-expanded` state on toggle button
- Removed old vanilla JS menu handling, kept scroll-to-top functionality

---

## Medium Priority Issues

### 10. Article Pagination Links Lack Context
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.4.4 Link Purpose (In Context) (Level A)
**Location:** `templates/partials/article-pagination.html.twig`

**Issue:**
Screen readers only hear "Previous" or "Next" without article title context.

**Solution:**
Added `aria-label="Previous article: {{ prev_page.title }}"` and `aria-label="Next article: {{ next_page.title }}"`. Also added focus ring styling and marked icons as `aria-hidden="true"`.

---

### 11. Search Input Missing Accessible Label
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.3.1 Info and Relationships (Level A), 3.3.2 Labels or Instructions (Level A)
**Location:** `templates/partials/blog/posts-grid.html.twig`

**Issue:**
Search input uses placeholder only, no associated `<label>` element.

**Solution:**
Added `<label for="post-search" class="sr-only">` with `id="post-search"` on input. Also added `role="search"` and `aria-labelledby` on the search container.

---

### 12. Filter Toggle Buttons Missing State
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 4.1.2 Name, Role, Value (Level A)
**Location:** `templates/partials/taxonomylist.html.twig`

**Issue:**
Toggle buttons don't communicate pressed state to assistive technology.

**Solution:**
Added `:aria-pressed="isSelected('{{ tax }}').toString()"`. Also added `role="group" aria-label="Filter by tags"` on container and focus ring styling on all buttons.

---

### 13. Empty Anchor Elements
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 2.4.4 Link Purpose (Level A)
**Location:** `templates/modular.html.twig`

**Issue:**
Empty `<a>` elements used as scroll targets are announced as links with no content.

**Solution:**
Changed `<a id="{{ target }}">` to `<div id="{{ target }}">` for scroll-spy anchors.

---

## Passing Criteria

The following accessibility features are already implemented correctly:

| Feature | Location | WCAG Criterion |
|---------|----------|----------------|
| Language attribute | `base.html.twig:2` | 3.1.1 |
| Main landmark | `base.html.twig:45` | 1.3.1 |
| Current page indication | `navigation.html.twig:7` | 2.4.8 |
| Viewport allows zoom | `base.html.twig:9` | 1.4.4 |
| Heading hierarchy | Various templates | 1.3.1 |
| Pagination relationships | `pagination.html.twig` | 1.3.1 |
| Form label structure | `forms.css` | 1.3.1 |

---

## Automated Testing

### Running Tests

```bash
# Install dependencies (first time only)
cd user/themes/nyu-lib
npm install

# Run accessibility tests against all pages
npm run a11y

# Test a single URL
npm run a11y:single -- http://trilby.local/grav-nyu-lib/

# Test against localhost
npm run a11y:local
```

### Test Configuration

Tests are configured in `.pa11yci.json` and use:
- **Pa11y** with axe-core and HTML CodeSniffer runners
- **WCAG 2.1 AA** standard
- Screenshots saved to `./a11y-results/`

### CI/CD Integration

Add to your CI pipeline:
```yaml
- name: Run accessibility tests
  run: |
    cd user/themes/nyu-lib
    npm ci
    npm run a11y
```

---

### 14. Hero Section Background Image Contrast
**Status:** [x] Completed (2025-12-27)
**WCAG Criterion:** 1.4.3 Contrast (Minimum) (Level AA)
**Location:** `templates/partials/base.html.twig`, `templates/partials/content-title.html.twig`

**Issue:**
Hero section uses a grayscale background image overlay at 20% opacity, causing axe-core to flag contrast issues because it couldn't determine the effective background color.

**Solution:**
- Reduced background image opacity from 20% to 10%
- Added `relative z-10` to content wrapper for proper stacking
- Added explicit `bg-primary-50` background to the title container
- Changed H1 to `text-black` for maximum contrast

---

### 15. Updated pa11y-ci Testing
**Status:** [x] Completed (2025-12-27)
**Location:** `package.json`

**Issue:**
Pa11y-ci 3.1.0 used an older version of axe-core (4.2) which had different/stricter contrast detection that resulted in false positives.

**Solution:**
Updated pa11y-ci from 3.1.0 to 4.0.1 which uses axe-core 4.10 with more accurate contrast detection.

---

## Manual Testing Checklist

After implementing fixes, test with:

- [ ] Keyboard-only navigation (Tab, Shift+Tab, Enter, Space, Escape, Arrow keys)
- [ ] Screen reader (VoiceOver on macOS, NVDA or JAWS on Windows)
- [ ] Browser zoom to 200%
- [ ] Color contrast analyzer (e.g., axe DevTools, WAVE)
- [ ] Reduced motion preference (`prefers-reduced-motion`)

---

## Resources

- [WCAG 2.1 Quick Reference](https://www.w3.org/WAI/WCAG21/quickref/)
- [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [Tailwind CSS Screen Reader Utilities](https://tailwindcss.com/docs/screen-readers)
- [Alpine.js Focus Plugin](https://alpinejs.dev/plugins/focus)
