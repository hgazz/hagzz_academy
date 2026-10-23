# Hagzz Academy onboarding manual — Qatar

[Download the Arabic PDF](Hagzz-Academy-Qatar-Manual-AR.pdf) · [Cover preview](cover.png)

Edition 02: 33-page, A4 Arabic guide for prospective academy customers. Covers partner web setup, locations, coaches, programs, classes, students/guardians, groups, subscriptions, collection, attendance, reports and team access. Product roles follow the owner's clarification: **Hagzz Partner** has separate owner and guardian accounts; **Hagzz** is the customer app for discovering/selecting training.

Includes the owner-provided grant description with Qatar Science & Technology Park, contact +974 7054 2458, WhatsApp QR and the existing website pricing link. No discount percentage or free-month count is invented. No third-party logo is used.

Edition 02 adds a restrained editorial design, warm paper backgrounds, deep green chapter dividers, larger dashboard images, new owner-focused introduction and a single closing contact page. The architectural sports cover is original AI-generated illustration, not a photograph of an actual facility or a claim of ownership.

Dashboard screenshots were captured from the local application with synthetic training data, without modifying production. Mobile onboarding uses clearly labeled illustrative diagrams; no unverified Partner store URL or exact guardian interface is presented as fact. Partner installation is routed through the supplied onboarding contact. The published customer app may differ from candidate builds under review.

## Sources and regeneration

- `Hagzz-Academy-Qatar-Manual.html` + `assets/`: standalone editable print source; no external asset dependencies.
- `build.py` + `content.py` + `premium.css`: regenerate the HTML and page list using Python 3 (standard library only).
- Serve this directory with a local HTTP server. Render in Chromium using A4, zero margins, background graphics enabled, headers/footers disabled, and CSS page size preferred. Load fonts before printing; use installed Noto Sans Arabic. Do not use the file:// scheme where browser policy blocks it.
- Latest PDF validation: 33 nonempty A4 pages, 39 links, no missing images or content/footer overlaps. Inspected rendered PDF pages for Arabic shaping and readability. See `verification.json` for the SHA-256 checksum.

This is the academy volume. Gym/health center and venue/facility manuals are separate future volumes.
