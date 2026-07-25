<?php

namespace App\Helpers;

class PaymentMethodHelper
{
    public static function getMethodsForCountry(?string $countryIso2 = 'SA'): array
    {
        $iso = strtoupper($countryIso2 ?: 'SA');

        $all = [
            'cash' => [
                'id' => 'cash',
                'name_ar' => 'نقدي (Cash)',
                'name_en' => 'Cash',
                'logo' => asset('assetsAdmin/img/payments/cash.svg'),
            ],
            'mada' => [
                'id' => 'mada',
                'name_ar' => 'بطاقة مدى (Mada)',
                'name_en' => 'Mada Card',
                'logo' => asset('assetsAdmin/img/payments/mada.svg'),
            ],
            'stc_pay' => [
                'id' => 'stc_pay',
                'name_ar' => 'STC Pay',
                'name_en' => 'STC Pay',
                'logo' => asset('assetsAdmin/img/payments/stc_pay.svg'),
            ],
            'apple_pay' => [
                'id' => 'apple_pay',
                'name_ar' => 'Apple Pay',
                'name_en' => 'Apple Pay',
                'logo' => asset('assetsAdmin/img/payments/apple_pay.svg'),
            ],
            'instapay' => [
                'id' => 'instapay',
                'name_ar' => 'إينستا باي (InstaPay)',
                'name_en' => 'InstaPay',
                'logo' => asset('assetsAdmin/img/payments/instapay.svg'),
            ],
            'fawry' => [
                'id' => 'fawry',
                'name_ar' => 'فوري (Fawry)',
                'name_en' => 'Fawry',
                'logo' => asset('assetsAdmin/img/payments/fawry.svg'),
            ],
            'naps' => [
                'id' => 'naps',
                'name_ar' => 'بطاقة NAPS قطر',
                'name_en' => 'NAPS Qatar',
                'logo' => asset('assetsAdmin/img/payments/naps.svg'),
            ],
            'fawran' => [
                'id' => 'fawran',
                'name_ar' => 'فوران قطر (Fawran)',
                'name_en' => 'Fawran Qatar',
                'logo' => asset('assetsAdmin/img/payments/fawran.svg'),
            ],
            'mbway' => [
                'id' => 'mbway',
                'name_ar' => 'MB WAY البرتغال',
                'name_en' => 'MB WAY Portugal',
                'logo' => asset('assetsAdmin/img/payments/mbway.svg'),
            ],
        ];

        switch ($iso) {
            case 'SA':
                return [$all['cash'], $all['mada'], $all['stc_pay'], $all['apple_pay']];
            case 'EG':
                return [$all['cash'], $all['instapay'], $all['fawry'], $all['apple_pay']];
            case 'QA':
                return [$all['cash'], $all['naps'], $all['fawran'], $all['apple_pay']];
            case 'PT':
                return [$all['cash'], $all['mbway'], $all['apple_pay']];
            default:
                return [$all['cash'], $all['mada'], $all['apple_pay'], $all['instapay'], $all['fawry'], $all['naps'], $all['fawran'], $all['mbway']];
        }
    }
}
