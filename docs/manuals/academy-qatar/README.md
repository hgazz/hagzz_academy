# Hagzz academy management presentation and operating manual — Qatar

**Edition 03 — management first.**

- [Management presentation, Arabic PDF — 18 pages](Hagzz-Academy-Qatar-Management-AR.pdf): the primary prospect-facing document. Native dashboard screenshots connect each metric/graph to a management question and next action, before introducing roles, onboarding and the grant.
- [Complete operating manual, Arabic PDF — 41 pages](Hagzz-Academy-Qatar-Manual-AR.pdf): the same management section followed by the illustrated operational instructions.
- [Dashboard preview](dashboard-preview.png) · [Cover preview](cover.png).

## Data provenance and scope

All dashboard images come from the **actual local Hagzz application**, not mock UI or graphics with manually replaced values. The academy is the synthetic training tenant. Local demonstration records were added for August, September and October through **23 October 2026**, enabling a meaningful monthly comparison and populated October operations. No production/customer database was changed. These numbers are illustrative, **not actual customer results or a performance guarantee**.

Snapshot highlights: 72 active students, six groups, 72 active subscriptions; cumulative collection QAR 95,000 and outstanding QAR 13,100; October collection QAR 49,600 and expenses QAR 14,100, giving a recorded cash difference of QAR 35,500. Attendance is 438 present/late cases out of 498 cases (88%), not 498 distinct students. Fourteen active subscriptions approach expiry. The document distinguishes cumulative totals, October totals, last-30-day attendance and upcoming expiry dates. Cash difference is not audited accounting profit.

`dashboard-data.json` preserves the controller-generated snapshot. `chart-verification.json` confirms shared monetary axes, expense category and Qatar payment reconciliation. Dashboard defects discovered while preparing the demo were fixed and documented in [maintenance notes](../../maintenance/2026-10-23-dashboard-summary.md); they require no migration or production demo data.

## Product scope and content

Covers partner web setup, locations, coaches, programs, classes, students/guardians, groups, subscriptions, collection, attendance, reports and team access. **Hagzz Partner** has separate owner and guardian accounts; **Hagzz** is the customer app for discovering/selecting training. Mobile onboarding remains clearly labeled flow diagrams; no unverified Partner store URL or exact guardian UI is presented as a verified screenshot.

Includes the owner-provided grant description with Qatar Science & Technology Park, contact +974 7054 2458, WhatsApp QR and existing pricing-page link. No discount percentage/free-month count or third-party logo is invented. The cover is AI-generated abstract sports architecture, not an actual customer venue.

## Regeneration and checks

`build.py`, `content.py`, `management.py`, `premium.css`, `dashboard-data.json` and `assets/` produce the two HTML sources using standard Python 3. Serve the directory over local HTTP; print both HTML files using Chromium, A4, zero margins, background graphics and CSS page size, with fonts loaded and browser headers/footers disabled. Noto Sans Arabic and Noto Kufi Arabic are required. The complete HTML prints to `Hagzz-Academy-Qatar-Manual-AR.pdf`.

Validation: 18 and 41 nonempty A4 pages; 6 and 47 links respectively; no missing images or content/footer overlap. Rendered PDF pages inspected for Arabic readability. Eight native dashboard charts rendered without page errors. Reporting fixes: 15 PHP tests / 105 assertions passed. Checksums are in `verification.json`.

This volume covers academies; gym/health center and venue/facility volumes are separate future work.

## English edition

The [English management presentation (18 pages)](en/Hagzz-Academy-Qatar-Management-EN.pdf) and [complete guide (38 pages)](en/Hagzz-Academy-Qatar-Manual-EN.pdf) use larger type, newly captured English interface screenshots and credited real stadium photography. See [English sources and credits](en/README.md).
