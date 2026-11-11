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

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting !== null ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
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
}
