<?php

namespace App\Models;

use App\Services\Storage\R2StorageService;
use Illuminate\Support\Facades\Cache;
use MongoDB\Laravel\Eloquent\Model;

class PageMedia extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'page_media';

    protected $fillable = [
        'key',
        'label',
        'page_name',
        'section_name',
        'image_path',
        'image_url',
        'image_type',
        'recommended_size',
        'status',
        'updated_by',
        'updated_by_id',
        'file_size',
        'width',
        'height',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected const CACHE_KEY = 'page_media.public';
    protected const CACHE_TTL = 300;

    public static function definitions(): array
    {
        return [
            'website_logo' => [
                'label' => 'Website Logo',
                'page_name' => 'Global Branding',
                'section_name' => 'Site Identity',
                'recommended_size' => '240 x 120 px',
                'fallback' => 'img/log.png',
            ],
            'header_logo' => [
                'label' => 'Header Logo',
                'page_name' => 'Global Branding',
                'section_name' => 'Main Header',
                'recommended_size' => '240 x 80 px',
                'fallback' => 'img/log.png',
            ],
            'footer_logo' => [
                'label' => 'Footer Logo',
                'page_name' => 'Global Branding',
                'section_name' => 'Main Footer',
                'recommended_size' => '280 x 120 px',
                'fallback' => 'img/log1.png',
            ],
            'site_favicon' => [
                'label' => 'Browser/Favicon Logo',
                'page_name' => 'Global Branding',
                'section_name' => 'Browser Tab',
                'recommended_size' => '64 x 64 px',
                'fallback' => 'img/log2.png',
            ],
            'default_placeholder_image' => [
                'label' => 'Default Placeholder Image',
                'page_name' => 'Error/Empty States',
                'section_name' => 'Generic Fallback',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/camp.jpg',
            ],
            'empty_state_image' => [
                'label' => 'Empty State Image',
                'page_name' => 'Error/Empty States',
                'section_name' => 'No Data States',
                'recommended_size' => '960 x 640 px',
                'fallback' => 'img/camp.jpg',
            ],
            'error_page_image' => [
                'label' => 'Error Page Image',
                'page_name' => 'Error/Empty States',
                'section_name' => 'Error Pages',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/bg2.jpg',
            ],
            'auth_background' => [
                'label' => 'Default Auth Background',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Shared Auth Background',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/backlogin.png',
            ],
            'login_background' => [
                'label' => 'Login Page Background',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Login Page',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/backlogin.png',
            ],
            'login_logo' => [
                'label' => 'Login Page Logo',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Login Form',
                'recommended_size' => '240 x 120 px',
                'fallback' => 'img/log.png',
            ],
            'register_background' => [
                'label' => 'Register Page Background',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Register Page',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/backlogin.png',
            ],
            'register_panel_image_1' => [
                'label' => 'Register Panel Image 1',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Register Carousel',
                'recommended_size' => '900 x 520 px',
                'fallback' => 'img/eyy.png',
            ],
            'register_panel_image_2' => [
                'label' => 'Register Panel Image 2',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Register Carousel',
                'recommended_size' => '900 x 520 px',
                'fallback' => 'img/yagit.png',
            ],
            'register_panel_image_3' => [
                'label' => 'Register Panel Image 3',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Register Carousel',
                'recommended_size' => '900 x 520 px',
                'fallback' => 'img/diss.jpg',
            ],
            'email_verification_background' => [
                'label' => 'Email Verification Background',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Email Verification Page',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/backlogin.png',
            ],
            'reset_password_background' => [
                'label' => 'Reset Password Background',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Reset Password Page',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/backlogin.png',
            ],
            'forgot_password_image' => [
                'label' => 'Forgot Password Modal Image',
                'page_name' => 'Authentication Pages',
                'section_name' => 'Forgot Password',
                'recommended_size' => '900 x 520 px',
                'fallback' => 'img/backlogin.png',
            ],
            'home_hero_background' => [
                'label' => 'Home Hero Background',
                'page_name' => 'Home Page',
                'section_name' => 'Hero',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/bg1.jpg',
            ],
            'home_impact_image' => [
                'label' => 'Home Impact Image',
                'page_name' => 'Home Page',
                'section_name' => 'Impact Section',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/diss.jpg',
            ],
            'home_cta_background' => [
                'label' => 'Home CTA Background',
                'page_name' => 'Home Page',
                'section_name' => 'Call to Action',
                'recommended_size' => '1600 x 900 px',
                'fallback' => 'img/bg2.jpg',
            ],
            'decorative_pattern_image' => [
                'label' => 'Decorative Pattern Image',
                'page_name' => 'Global Branding',
                'section_name' => 'Decorative Backgrounds',
                'recommended_size' => '720 x 720 px',
                'fallback' => 'img/doodle.svg',
            ],
            'about_hero_image' => [
                'label' => 'About Hero Image',
                'page_name' => 'About Page',
                'section_name' => 'Hero',
                'recommended_size' => '1920 x 900 px',
                'fallback' => 'img/bg2.jpg',
            ],
            'about_intro_image' => [
                'label' => 'About Intro Image',
                'page_name' => 'About Page',
                'section_name' => 'How It Started',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/diss.jpg',
            ],
            'about_impact_fallback_image' => [
                'label' => 'About Impact Fallback Image',
                'page_name' => 'About Page',
                'section_name' => 'Impact Reports',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/bg1.jpg',
            ],
            'campaign_featured_banner' => [
                'label' => 'Campaign Page Featured Banner',
                'page_name' => 'Campaign Pages',
                'section_name' => 'Featured Program',
                'recommended_size' => '1400 x 780 px',
                'fallback' => 'img/diss.jpg',
            ],
            'campaign_default_image' => [
                'label' => 'Campaign Default Image',
                'page_name' => 'Campaign Pages',
                'section_name' => 'Campaign Cards and Details',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/camp.jpg',
            ],
            'event_hero_image' => [
                'label' => 'Event Hero Image',
                'page_name' => 'Event Pages',
                'section_name' => 'Events Hero',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/bg1.jpg',
            ],
            'event_default_image' => [
                'label' => 'Event Default Image',
                'page_name' => 'Event Pages',
                'section_name' => 'Event Cards and Details',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/bg2.jpg',
            ],
            'donation_hero_image' => [
                'label' => 'Donation Page Hero Image',
                'page_name' => 'Donation Pages',
                'section_name' => 'In-Kind Donation Hero',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/bg1.jpg',
            ],
            'donation_tracking_hero_image' => [
                'label' => 'Donation Tracking Hero Image',
                'page_name' => 'Donation Pages',
                'section_name' => 'Impact Tracking Hero',
                'recommended_size' => '1920 x 1080 px',
                'fallback' => 'img/bg1.jpg',
            ],
            'donation_default_image' => [
                'label' => 'Donation Default Image',
                'page_name' => 'Donation Pages',
                'section_name' => 'Impact Cards',
                'recommended_size' => '1200 x 800 px',
                'fallback' => 'img/inkind.png',
            ],
            'contact_hero_image' => [
                'label' => 'Contact Page Hero Image',
                'page_name' => 'Contact Page',
                'section_name' => 'Contact Hero',
                'recommended_size' => '1920 x 900 px',
                'fallback' => 'img/bg2.jpg',
            ],
            'email_template_logo' => [
                'label' => 'Email Template Logo',
                'page_name' => 'Email Pages',
                'section_name' => 'Email Header',
                'recommended_size' => '240 x 120 px',
                'fallback' => 'img/log.png',
            ],
            'email_verification_banner' => [
                'label' => 'Email Verification Banner',
                'page_name' => 'Email Pages',
                'section_name' => 'Verification Email',
                'recommended_size' => '1200 x 420 px',
                'fallback' => 'img/backlogin.png',
            ],
            'email_reset_banner' => [
                'label' => 'Password Reset Email Banner',
                'page_name' => 'Email Pages',
                'section_name' => 'Reset Password Email',
                'recommended_size' => '1200 x 420 px',
                'fallback' => 'img/backlogin.png',
            ],
        ];
    }

    public static function definitionFor(string $key): ?array
    {
        $definition = self::definitions()[$key] ?? null;

        return $definition ? array_merge(['key' => $key], $definition) : null;
    }

    public static function adminItems()
    {
        $records = static::query()->get()->keyBy('key');

        return collect(self::definitions())->map(function (array $definition, string $key) use ($records) {
            $record = $records->get($key);

            return self::itemPayload($key, $definition, $record, true);
        })->values();
    }

    public static function groupedAdminItems(): array
    {
        return self::adminItems()
            ->groupBy('page_name')
            ->map(fn ($items) => $items->values())
            ->all();
    }

    public static function publicKeyed(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $records = static::query()
                ->where('status', 'active')
                ->get()
                ->keyBy('key');

            return collect(self::definitions())->mapWithKeys(function (array $definition, string $key) use ($records) {
                $record = $records->get($key);
                $item = self::itemPayload($key, $definition, $record, false);

                return [$key => $item];
            })->all();
        });
    }

    public static function urlFor(string $key, ?string $fallback = null): string
    {
        $items = self::publicKeyed();

        if (!empty($items[$key]['url'])) {
            return $items[$key]['url'];
        }

        $definition = self::definitionFor($key);
        if ($definition && !empty($definition['fallback'])) {
            return asset($definition['fallback']);
        }

        return $fallback ?: asset('img/camp.jpg');
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function itemPayload(string $key, array $definition, ?self $record, bool $includeAdmin): array
    {
        $url = $record?->image_path
            ? (app(R2StorageService::class)->url($record->image_path) ?: $record->image_url)
            : asset($definition['fallback']);

        $payload = [
            'key' => $key,
            'label' => $definition['label'],
            'page_name' => $definition['page_name'],
            'section_name' => $definition['section_name'],
            'recommended_size' => $definition['recommended_size'],
            'url' => $url,
            'image_url' => $url,
            'status' => $record?->status ?: 'default',
            'has_custom_image' => (bool) $record?->image_path,
            'image_type' => $record?->image_type,
            'width' => $record?->width,
            'height' => $record?->height,
            'file_size' => $record?->file_size,
            'updated_at' => optional($record?->updated_at)->toIso8601String(),
            'updated_at_human' => $record?->updated_at ? $record->updated_at->format('M d, Y g:i A') : 'Not updated yet',
        ];

        if ($includeAdmin) {
            $payload['updated_by'] = $record?->updated_by;
            $payload['image_path'] = $record?->image_path;
        }

        return $payload;
    }
}
