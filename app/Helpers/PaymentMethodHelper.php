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
                'logo' => asset('assetsAdmin/img/payments/cash.png'),
            ],
            'mada' => [
                'id' => 'mada',
                'name_ar' => 'بطاقة مدى (Mada)',
                'name_en' => 'Mada Card',
                'logo' => asset('assetsAdmin/img/payments/mada.png'),
            ],
            'stc_pay' => [
                'id' => 'stc_pay',
                'name_ar' => 'STC Pay',
                'name_en' => 'STC Pay',
                'logo' => asset('assetsAdmin/img/payments/stc_pay.png'),
            ],
            'apple_pay' => [
                'id' => 'apple_pay',
                'name_ar' => 'Apple Pay',
                'name_en' => 'Apple Pay',
                'logo' => asset('assetsAdmin/img/payments/apple_pay.png'),
            ],
            'sadad_ksa' => [
                'id' => 'sadad_ksa',
                'name_ar' => 'سداد (Sadad KSA)',
                'name_en' => 'Sadad KSA',
                'logo' => asset('assetsAdmin/img/payments/sadad_ksa.png'),
            ],
            'instapay' => [
                'id' => 'instapay',
                'name_ar' => 'إينستا باي (InstaPay)',
                'name_en' => 'InstaPay',
                'logo' => asset('assetsAdmin/img/payments/instapay.png'),
            ],
            'fawry' => [
                'id' => 'fawry',
                'name_ar' => 'فوري (Fawry)',
                'name_en' => 'Fawry',
                'logo' => asset('assetsAdmin/img/payments/fawry.png'),
            ],
            'card_bank' => [
                'id' => 'card_bank',
                'name_ar' => 'بطاقة بنكية (Visa/Master)',
                'name_en' => 'Bank Card',
                'logo' => asset('assetsAdmin/img/payments/card_bank.png'),
            ],
            'wallet' => [
                'id' => 'wallet',
                'name_ar' => 'محفظة إلكترونية',
                'name_en' => 'E-Wallet',
                'logo' => asset('assetsAdmin/img/payments/wallet.jpg'),
            ],
            'bank_transfer' => [
                'id' => 'bank_transfer',
                'name_ar' => 'تحويل بنكي',
                'name_en' => 'Bank Transfer',
                'logo' => asset('assetsAdmin/img/payments/bank_transfer.jpg'),
            ],
            'naps' => [
                'id' => 'naps',
                'name_ar' => 'بطاقة الخصم (NAPS قطر)',
                'name_en' => 'NAPS Qatar',
                'logo' => asset('assetsAdmin/img/payments/naps.jpg'),
            ],
            'fawran' => [
                'id' => 'fawran',
                'name_ar' => 'فوران قطر (Fawran)',
                'name_en' => 'Fawran Qatar',
                'logo' => asset('assetsAdmin/img/payments/fawran.jpg'),
            ],
            'sadad_qa' => [
                'id' => 'sadad_qa',
                'name_ar' => 'سداد قطر (Sadad QA)',
                'name_en' => 'Sadad Qatar',
                'logo' => asset('assetsAdmin/img/payments/sadad_qa.png'),
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
                return [$all['cash'], $all['mada'], $all['stc_pay'], $all['apple_pay'], $all['sadad_ksa']];
            case 'EG':
                return [$all['cash'], $all['instapay'], $all['fawry'], $all['card_bank'], $all['wallet'], $all['bank_transfer']];
            case 'QA':
                return [$all['cash'], $all['naps'], $all['fawran'], $all['sadad_qa'], $all['card_bank'], $all['apple_pay']];
            case 'PT':
                return [$all['cash'], $all['mbway'], $all['apple_pay']];
            default:
                return [$all['cash'], $all['card_bank'], $all['apple_pay']];
        }
    }
}
