<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

try {
    $html = view("Academy.pages.expenses.index", [
        "expenses" => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
        "categories" => collect([]),
        "coaches" => collect([]),
        "branches" => collect([]),
        "totalRevenue" => 0,
        "totalExpenses" => 0,
        "netProfit" => 0,
        "profitMargin" => 0,
        "revenueStreams" => [
            "subscriptions" => ["collected" => 0],
            "trainings" => ["collected" => 0],
            "venues" => ["collected" => 0],
            "camps" => ["collected" => 0]
        ],
        "academyCurrencyCode" => "SAR",
        "academyCurrencySymbol" => "SAR"
    ])->render();
    echo "SUCCESS: " . strlen($html);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\nFILE: " . $e->getFile() . "\nLINE: " . $e->getLine() . "\n";
}
