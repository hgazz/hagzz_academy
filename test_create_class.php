<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $training = App\Models\Training::first();
    echo "Training ID: " . ($training ? $training->id : "NONE") . "\n";

    $translatable = App\Services\TranslatableService::generateTranslatableFields(
        App\Models\TClass::getTranslatableFields(),
        ['title_ar' => 'حصة اختبارية', 'title_en' => 'Test Class']
    );

    echo "Translatable fields:\n";
    print_r($translatable);

    $class = App\Models\TClass::create(array_merge($translatable, [
        'date' => '2026-08-15',
        'training_id' => $training ? $training->id : 1,
        'start_time' => '10:00',
        'end_time' => '11:00',
        'out_comes' => ['ar' => ['نتيجة 1'], 'en' => ['Outcome 1']],
        'bring_with_me' => ['ar' => ['أدوات'], 'en' => ['Tools']],
    ]));

    echo "SUCCESS CLASS ID: " . $class->id . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
