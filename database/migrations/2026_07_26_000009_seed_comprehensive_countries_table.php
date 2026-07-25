<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('countries')) {
            return;
        }

        $allCountries = [
            ['iso2' => 'EG', 'currency' => 'EGP', 'ar' => '🇪🇬 جمهورية مصر العربية', 'en' => 'Egypt'],
            ['iso2' => 'SA', 'currency' => 'SAR', 'ar' => '🇸🇦 المملكة العربية السعودية', 'en' => 'Saudi Arabia'],
            ['iso2' => 'AE', 'currency' => 'AED', 'ar' => '🇦🇪 الإمارات العربية المتحدة', 'en' => 'United Arab Emirates'],
            ['iso2' => 'QA', 'currency' => 'QAR', 'ar' => '🇶🇦 دولة قطر', 'en' => 'Qatar'],
            ['iso2' => 'KW', 'currency' => 'KWD', 'ar' => '🇰🇼 دولة الكويت', 'en' => 'Kuwait'],
            ['iso2' => 'OM', 'currency' => 'OMR', 'ar' => '🇴🇲 سلطنة عمان', 'en' => 'Oman'],
            ['iso2' => 'BH', 'currency' => 'BHD', 'ar' => '🇧🇭 مملكة البحرين', 'en' => 'Bahrain'],
            ['iso2' => 'JO', 'currency' => 'JOD', 'ar' => '🇯🇴 المملكة الأردنية الهاشمية', 'en' => 'Jordan'],
            ['iso2' => 'LB', 'currency' => 'LBP', 'ar' => '🇱🇧 الجمهورية اللبنانية', 'en' => 'Lebanon'],
            ['iso2' => 'ES', 'currency' => 'EUR', 'ar' => '🇪🇸 إسبانيا', 'en' => 'Spain'],
            ['iso2' => 'TR', 'currency' => 'TRY', 'ar' => '🇹🇷 تركيا', 'en' => 'Turkey'],
            ['iso2' => 'GB', 'currency' => 'GBP', 'ar' => '🇬🇧 المملكة المتحدة (بريطانيا)', 'en' => 'United Kingdom'],
            ['iso2' => 'FR', 'currency' => 'EUR', 'ar' => '🇫🇷 فرنسا', 'en' => 'France'],
            ['iso2' => 'DE', 'currency' => 'EUR', 'ar' => '🇩🇪 ألمانيا', 'en' => 'Germany'],
            ['iso2' => 'IT', 'currency' => 'EUR', 'ar' => '🇮🇹 إيطاليا', 'en' => 'Italy'],
            ['iso2' => 'NL', 'currency' => 'EUR', 'ar' => '🇳🇱 هولندا', 'en' => 'Netherlands'],
            ['iso2' => 'PT', 'currency' => 'EUR', 'ar' => '🇵🇹 البرتغال', 'en' => 'Portugal'],
            ['iso2' => 'GR', 'currency' => 'EUR', 'ar' => '🇬🇷 اليونان', 'en' => 'Greece'],
            ['iso2' => 'US', 'currency' => 'USD', 'ar' => '🇺🇸 الولايات المتحدة الأمريكية', 'en' => 'United States'],
            ['iso2' => 'CA', 'currency' => 'CAD', 'ar' => '🇨🇦 كندا', 'en' => 'Canada'],
            ['iso2' => 'RU', 'currency' => 'RUB', 'ar' => '🇷🇺 روسيا', 'en' => 'Russia'],
            ['iso2' => 'JP', 'currency' => 'JPY', 'ar' => '🇯🇵 اليابان', 'en' => 'Japan'],
            ['iso2' => 'BR', 'currency' => 'BRL', 'ar' => '🇧🇷 البرازيل', 'en' => 'Brazil'],
            ['iso2' => 'AR', 'currency' => 'ARS', 'ar' => '🇦🇷 الأرجنتين', 'en' => 'Argentina'],
            ['iso2' => 'MA', 'currency' => 'MAD', 'ar' => '🇲🇦 المملكة المغربية', 'en' => 'Morocco'],
            ['iso2' => 'TN', 'currency' => 'TND', 'ar' => '🇹🇳 الجمهورية التونسية', 'en' => 'Tunisia'],
            ['iso2' => 'DZ', 'currency' => 'DZD', 'ar' => '🇩🇿 الجمهورية الجزائرية', 'en' => 'Algeria'],
            ['iso2' => 'IQ', 'currency' => 'IQD', 'ar' => '🇮🇶 الجمهورية العراقية', 'en' => 'Iraq'],
            ['iso2' => 'LY', 'currency' => 'LYD', 'ar' => '🇱🇾 دولة ليبيا', 'en' => 'Libya'],
            ['iso2' => 'SD', 'currency' => 'SDG', 'ar' => '🇸🇩 جمهورية السودان', 'en' => 'Sudan'],
            ['iso2' => 'SY', 'currency' => 'SYP', 'ar' => '🇸🇾 الجمهورية العربية السورية', 'en' => 'Syria'],
            ['iso2' => 'PS', 'currency' => 'ILS', 'ar' => '🇵🇸 دولة فلسطين', 'en' => 'Palestine'],
            ['iso2' => 'YE', 'currency' => 'YER', 'ar' => '🇾🇪 الجمهورية اليمنية', 'en' => 'Yemen'],
            ['iso2' => 'CN', 'currency' => 'CNY', 'ar' => '🇨🇳 الصين', 'en' => 'China'],
            ['iso2' => 'KR', 'currency' => 'KRW', 'ar' => '🇰🇷 كوريا الجنوبية', 'en' => 'South Korea'],
            ['iso2' => 'ZA', 'currency' => 'ZAR', 'ar' => '🇿🇦 جنوب إفريقيا', 'en' => 'South Africa'],
            ['iso2' => 'CY', 'currency' => 'EUR', 'ar' => '🇨🇾 قبرص', 'en' => 'Cyprus'],
            ['iso2' => 'CH', 'currency' => 'CHF', 'ar' => '🇨🇭 سويسرا', 'en' => 'Switzerland'],
            ['iso2' => 'AT', 'currency' => 'EUR', 'ar' => '🇦🇹 النمسا', 'en' => 'Austria'],
            ['iso2' => 'GE', 'currency' => 'GEL', 'ar' => '🇬🇪 جورجيا', 'en' => 'Georgia'],
            ['iso2' => 'RS', 'currency' => 'RSD', 'ar' => '🇷🇸 صربيا', 'en' => 'Serbia'],
            ['iso2' => 'HR', 'currency' => 'EUR', 'ar' => '🇭🇷 كرواتيا', 'en' => 'Croatia'],
            ['iso2' => 'CZ', 'currency' => 'CZK', 'ar' => '🇨🇿 التشيك', 'en' => 'Czech Republic'],
            ['iso2' => 'PL', 'currency' => 'PLN', 'ar' => '🇵🇱 بولندا', 'en' => 'Poland'],
            ['iso2' => 'HU', 'currency' => 'HUF', 'ar' => '🇭🇺 المجر', 'en' => 'Hungary'],
            ['iso2' => 'BE', 'currency' => 'EUR', 'ar' => '🇧🇪 بلجيكا', 'en' => 'Belgium'],
            ['iso2' => 'TH', 'currency' => 'THB', 'ar' => '🇹🇭 تايلاند', 'en' => 'Thailand'],
            ['iso2' => 'MY', 'currency' => 'MYR', 'ar' => '🇲🇾 ماليزيا', 'en' => 'Malaysia'],
            ['iso2' => 'SG', 'currency' => 'SGD', 'ar' => '🇸🇬 سنغافورة', 'en' => 'Singapore'],
            ['iso2' => 'ID', 'currency' => 'IDR', 'ar' => '🇮🇩 إندونيسيا', 'en' => 'Indonesia'],
            ['iso2' => 'UZ', 'currency' => 'UZS', 'ar' => '🇺🇿 أوزبكستان', 'en' => 'Uzbekistan'],
            ['iso2' => 'KZ', 'currency' => 'KZT', 'ar' => '🇰🇿 كازاخستان', 'en' => 'Kazakhstan'],
            ['iso2' => 'AU', 'currency' => 'AUD', 'ar' => '🇦🇺 أستراليا', 'en' => 'Australia'],
            ['iso2' => 'NZ', 'currency' => 'NZD', 'ar' => '🇳🇿 نيوزيلندا', 'en' => 'New Zealand'],
            ['iso2' => 'IN', 'currency' => 'INR', 'ar' => '🇮🇳 الهند', 'en' => 'India'],
            ['iso2' => 'PK', 'currency' => 'PKR', 'ar' => '🇵🇰 باكستان', 'en' => 'Pakistan'],
            ['iso2' => 'AZ', 'currency' => 'AZN', 'ar' => '🇦🇿 أذربيجان', 'en' => 'Azerbaijan'],
            ['iso2' => 'SE', 'currency' => 'SEK', 'ar' => '🇸🇪 السويد', 'en' => 'Sweden'],
            ['iso2' => 'NO', 'currency' => 'NOK', 'ar' => '🇳🇴 النرويج', 'en' => 'Norway'],
            ['iso2' => 'DK', 'currency' => 'DKK', 'ar' => '🇩🇰 الدنمارك', 'en' => 'Denmark'],
            ['iso2' => 'FI', 'currency' => 'EUR', 'ar' => '🇫🇮 فنلندا', 'en' => 'Finland'],
            ['iso2' => 'IE', 'currency' => 'EUR', 'ar' => '🇮🇪 أيرلندا', 'en' => 'Ireland'],
            ['iso2' => 'RO', 'currency' => 'RON', 'ar' => '🇷🇴 رومانيا', 'en' => 'Romania'],
            ['iso2' => 'BG', 'currency' => 'BGN', 'ar' => '🇧🇬 بلغاريا', 'en' => 'Bulgaria'],
            ['iso2' => 'SK', 'currency' => 'EUR', 'ar' => '🇸🇰 سلوفاكيا', 'en' => 'Slovakia'],
            ['iso2' => 'SI', 'currency' => 'EUR', 'ar' => '🇸🇮 سلوفينيا', 'en' => 'Slovenia'],
            ['iso2' => 'MX', 'currency' => 'MXN', 'ar' => '🇲🇽 المكسيك', 'en' => 'Mexico'],
            ['iso2' => 'CL', 'currency' => 'CLP', 'ar' => '🇨🇱 تشيلي', 'en' => 'Chile'],
            ['iso2' => 'CO', 'currency' => 'COP', 'ar' => '🇨🇴 كولومبيا', 'en' => 'Colombia'],
            ['iso2' => 'PE', 'currency' => 'PEN', 'ar' => '🇵🇪 بيرو', 'en' => 'Peru'],
            ['iso2' => 'UY', 'currency' => 'UYU', 'ar' => '🇺🇾 أوروغواي', 'en' => 'Uruguay'],
            ['iso2' => 'KE', 'currency' => 'KES', 'ar' => '🇰🇪 كينيا', 'en' => 'Kenya'],
            ['iso2' => 'NG', 'currency' => 'NGN', 'ar' => '🇳🇬 نيجيريا', 'en' => 'Nigeria'],
            ['iso2' => 'GH', 'currency' => 'GHS', 'ar' => '🇬🇭 غانا', 'en' => 'Ghana'],
            ['iso2' => 'SN', 'currency' => 'XOF', 'ar' => '🇸🇳 السنغال', 'en' => 'Senegal'],
            ['iso2' => 'CI', 'currency' => 'XOF', 'ar' => '🇨🇮 ساحل العاج', 'en' => 'Ivory Coast'],
            ['iso2' => 'CM', 'currency' => 'XAF', 'ar' => '🇨🇲 الكاميرون', 'en' => 'Cameroon'],
            ['iso2' => 'MU', 'currency' => 'MUR', 'ar' => '🇲🇺 موريشيوس', 'en' => 'Mauritius'],
            ['iso2' => 'SC', 'currency' => 'SCR', 'ar' => '🇸🇨 سيشل', 'en' => 'Seychelles'],
            ['iso2' => 'MV', 'currency' => 'MVR', 'ar' => '🇲🇻 جزر المالديف', 'en' => 'Maldives'],
            ['iso2' => 'LK', 'currency' => 'LKR', 'ar' => '🇱🇰 سريلانكا', 'en' => 'Sri Lanka'],
            ['iso2' => 'VN', 'currency' => 'VND', 'ar' => '🇻🇳 فيتنام', 'en' => 'Vietnam'],
            ['iso2' => 'PH', 'currency' => 'PHP', 'ar' => '🇵🇭 الفلبين', 'en' => 'Philippines'],
            ['iso2' => 'HK', 'currency' => 'HKD', 'ar' => '🇭🇰 هونغ كونغ', 'en' => 'Hong Kong'],
            ['iso2' => 'TW', 'currency' => 'TWD', 'ar' => '🇹🇼 تايوان', 'en' => 'Taiwan'],
            ['iso2' => 'IS', 'currency' => 'ISK', 'ar' => '🇮🇸 أيسلندا', 'en' => 'Iceland'],
            ['iso2' => 'MT', 'currency' => 'EUR', 'ar' => '🇲🇹 مالطا', 'en' => 'Malta'],
            ['iso2' => 'LU', 'currency' => 'EUR', 'ar' => '🇱🇺 لوكسمبورغ', 'en' => 'Luxembourg'],
            ['iso2' => 'MC', 'currency' => 'EUR', 'ar' => '🇲🇨 موناكو', 'en' => 'Monaco'],
            ['iso2' => 'AD', 'currency' => 'EUR', 'ar' => '🇦🇩 أندورا', 'en' => 'Andorra'],
            ['iso2' => 'SM', 'currency' => 'EUR', 'ar' => '🇸🇲 سان مارينو', 'en' => 'San Marino'],
            ['iso2' => 'BA', 'currency' => 'BAM', 'ar' => '🇧🇦 البوسنة والهرسك', 'en' => 'Bosnia and Herzegovina'],
            ['iso2' => 'AL', 'currency' => 'ALL', 'ar' => '🇦🇱 ألبانيا', 'en' => 'Albania'],
            ['iso2' => 'MK', 'currency' => 'MKD', 'ar' => '🇲🇰 مقدونيا الشمالية', 'en' => 'North Macedonia'],
            ['iso2' => 'ME', 'currency' => 'EUR', 'ar' => '🇲🇪 الجبل الأسود (مونتينيغرو)', 'en' => 'Montenegro'],
            ['iso2' => 'MD', 'currency' => 'MDL', 'ar' => '🇲🇩 مولدوفا', 'en' => 'Moldova'],
            ['iso2' => 'ARM', 'currency' => 'AMD', 'ar' => '🇦🇲 أرمينيا', 'en' => 'Armenia'],
        ];

        foreach ($allCountries as $c) {
            $namePayload = json_encode(['ar' => $c['ar'], 'en' => $c['en']], JSON_UNESCAPED_UNICODE);
            
            DB::table('countries')->updateOrInsert(
                ['iso2' => $c['iso2']],
                [
                    'name' => $namePayload,
                    'currency_code' => $c['currency'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
