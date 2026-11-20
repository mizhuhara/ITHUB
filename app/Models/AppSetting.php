<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /** @var array<string, mixed>|null */
    protected static ?array $runtimeCache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::$runtimeCache === null) {
            try {
                static::$runtimeCache = static::pluck('value', 'key')->all();
            } catch (\Throwable) {
                static::$runtimeCache = [];
            }
        }

        return static::$runtimeCache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): static
    {
        $normalized = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        if (static::$runtimeCache !== null) {
            static::$runtimeCache[$key] = $normalized;
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $normalized,
                'group' => $group,
            ]
        );
    }

    public static function isPremiumFeatureEnabled(): bool
    {
        $val = static::get('premium_feature_enabled', '1');

        return in_array($val, ['1', 'true', true, 1], true);
    }

    public static function setPremiumFeatureEnabled(bool $enabled): void
    {
        static::set('premium_feature_enabled', $enabled ? '1' : '0', 'membership');
    }

    /**
     * @return array<string, mixed>
     */
    public static function getPromoPopupSettings(): array
    {
        return [
            'enabled' => in_array(static::get('promo_popup_enabled', '0'), ['1', 'true', true, 1], true),
            'title' => (string) static::get('promo_popup_title', 'Promo Spesial ITHub'),
            'subtitle' => (string) static::get('promo_popup_subtitle', 'Temukan program dan penawaran terbaru untuk kamu.'),
            'image' => (string) static::get('promo_popup_image', ''),
            'button_text' => (string) static::get('promo_popup_button_text', 'Lihat Promo'),
            'button_link' => (string) static::get('promo_popup_button_link', ''),
            'theme' => (string) static::get('promo_popup_theme', 'light'),
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function setPromoPopupSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::set('promo_popup_'.$key, $value, 'promo');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getMembershipBankSettings(): array
    {
        return [
            'bank_name' => (string) static::get('membership_bank_name', 'Bank Central Asia (BCA)'),
            'account_number' => (string) static::get('membership_bank_account_number', '123-456-7890'),
            'account_name' => (string) static::get('membership_bank_account_name', 'PT IndoTech Ekosistem Digital'),
            'price_monthly' => (int) static::get('membership_price_monthly', 150000),
            'price_yearly' => (int) static::get('membership_price_yearly', 1500000),
            'instructions' => (string) static::get('membership_payment_instructions', 'Silakan transfer sesuai nominal ke rekening bank di atas, lalu lampirkan bukti transfer untuk diverifikasi oleh tim IndoTech.'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getSiteContentSettings(): array
    {
        return [
            // 1. Beranda (Home)
            'home_hero_title' => (string) static::get('home_hero_title', 'IT EKOSISTEM'),
            'home_hero_subtitle' => (string) static::get('home_hero_subtitle', 'Platform terdepan untuk menjelajahi institusi pendidikan unggulan, perusahaan teknologi ternama, komunitas aktif, dan peluang karir digital di Indonesia.'),

            // 2. Pendidikan - Direktori SMK
            'education_smk_title' => (string) static::get('education_smk_title', 'Direktori SMK (Sekolah Menengah Kejuruan)'),
            'education_smk_subtitle' => (string) static::get('education_smk_subtitle', 'Temukan dan terhubung dengan SMK Jurusan IT terbaik di seluruh Indonesia, dengan spesialisasi Rekayasa Perangkat Lunak, Infrastruktur Jaringan, Keamanan Siber, dan Teknologi Kreatif.'),

            // 3. Pendidikan - Perguruan Tinggi (Kampus)
            'campus_title' => (string) static::get('campus_title', 'University (Kampus) Directory'),
            'campus_subtitle' => (string) static::get('campus_subtitle', 'Discover and connect with top universities across Indonesia that offer Information Technology (IT) study programs — Computer Science, Information Systems, Informatics Engineering, and more.'),

            // 4. Industri
            'industry_title' => (string) static::get('industry_title', 'Ekosistem IT di Bali'),
            'industry_subtitle' => (string) static::get('industry_subtitle', 'Jelajahi lanskap teknologi yang sedang berkembang di Bali. Temukan perusahaan inovatif, agensi kreatif, dan startup yang membangun masa depan digital dari Pulau Dewata.'),

            // 5. Komunitas
            'community_title' => (string) static::get('community_title', 'Komunitas Tech Indonesia'),
            'community_subtitle' => (string) static::get('community_subtitle', 'Terhubung, berbagi ilmu, dan berkolaborasi bersama ribuan talenta teknologi di seluruh Nusantara. Temukan ruang yang tumbuh yang relevan dengan kariermu.'),

            // 6. Pusat Pengetahuan (Knowledge Hub)
            'knowledge_title' => (string) static::get('knowledge_title', 'Knowledge Hub'),
            'knowledge_subtitle' => (string) static::get('knowledge_subtitle', 'Stay at the forefront of the digital world. Curated insights, industry trends, and in-depth technical analysis bridging Indonesian talent with global IT excellence.'),

            // 7. Navbar Links
            'nav_home' => (string) static::get('nav_home', 'Beranda'),
            'nav_education' => (string) static::get('nav_education', 'Pendidikan'),
            'nav_industry' => (string) static::get('nav_industry', 'Industri'),
            'nav_career' => (string) static::get('nav_career', 'Karir'),
            'nav_events' => (string) static::get('nav_events', 'Acara'),
            'nav_community' => (string) static::get('nav_community', 'Komunitas'),
            'nav_knowledge' => (string) static::get('nav_knowledge', 'Pusat Pengetahuan'),
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function setSiteContentSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::set($key, (string) $value, 'site_content');
        }
    }
}
