# English academy presentation and operating guide — Qatar

- [Management presentation — 18 pages](Hagzz-Academy-Qatar-Management-EN.pdf).
- [Complete illustrated guide — 38 pages](Hagzz-Academy-Qatar-Manual-EN.pdf).
- [Cover preview](cover.png) · [English dashboard preview](dashboard-preview.png).

English copy written for academy owners and operations teams in Qatar. The primary body font is **16 CSS px / 12 pt**, with larger serif headings, maroon accents and warm neutral backgrounds. The deck preserves management-first charts, QAR amounts, role distinctions, the owner-provided grant terms and the Qatar onboarding contact.

## Actual screenshots and photographs

Dashboard and operating screenshots were newly captured from the **English application UI**, using the same local synthetic operational dataset as the Arabic edition. Synthetic player/group labels were temporarily translated for capture and then restored. No production/customer records changed. Snapshot values and their cumulative/October/last-30-day scopes remain unchanged; the [data snapshot](../dashboard-data.json) is retained with the Arabic edition. No chart values were replaced inside image files.

Two real photographs replace generated venue artwork: Stadium 974 in Doha and Maracanã in Brazil. Photographer, original photo and source-copy links are recorded in [PHOTO-CREDITS.md](PHOTO-CREDITS.md) and the PDFs. No stadium partnership or endorsement is implied. Photographs are historical contextual imagery.

Hagzz Partner is described with separate owner/guardian accounts; Hagzz is the training-discovery customer app. No unverified Partner store URL or exact guardian app screen is invented.

## Editable source and validation

`python3 build.py` regenerates both HTML files and page lists; `english.css` and `assets/` are self-contained. Serve the directory over HTTP and render with Chromium, fonts loaded, A4 CSS page size, backgrounds enabled, no browser headers/footers or additional margins. Requires Noto Sans Display and Noto Serif.

Validation: 18 and 38 nonempty A4 pages, all images present, no footer/content overlap. The complete guide has a clickable contents page. Rendered cover/dashboard pages were visually reviewed; PDF link destinations were checked. See `verification.json` for page counts and SHA-256 values.
