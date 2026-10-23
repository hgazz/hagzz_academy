# Dashboard checks during Qatar academy presentation

Populating a **local synthetic training tenant** exposed reporting defects. No customer/production records were created or changed.

- Expense categories used an inferred aggregate attribute name that did not match the SQL expression alias, returning empty categories despite recorded expenses. An explicit `total_expenses` alias preserves academy filtering and `COALESCE(base_amount, amount)` currency conversion.
- Qatar's payment-method list omitted bank transfers. Include `bank_transfer`/`bank` so recorded transfer collections appear in the country breakdown.
- Financial chart revenue, expenses and net income had independently scaled monetary axes, visually distorting their comparison. All three now share bounds; application booking counts retain the separate count axis.
- The booking series is labeled as app bookings, rather than subscriber counts. The attendance donut total is labeled as attendance records, not a percentage, and its subtitle states the actual last-30-day scope.

Validation: `php vendor/bin/phpunit tests/Feature/DashboardFinancialSummaryTest.php tests/Feature/PartnerWorkflowRegressionTest.php` — **15 tests, 105 assertions passed**. New tests verify category totals using base currency, tenant isolation, and Qatar transfer inclusion/reconciliation.

Browser validation on the local synthetic dataset: eight charts, no page errors; all three monetary axes `[0, 49600, 5 ticks]`; October collection/expense/net series `49600 / 14100 / 35500`; category total `28100`; Qatar payment-method total `95000`. The latter two are cumulative, not October-only. Demo player data and chart images are explicitly labeled illustrative, not customer performance evidence.

No migrations or production seed commands are required for these fixes. Deploying code does not deploy the local demonstration records.
