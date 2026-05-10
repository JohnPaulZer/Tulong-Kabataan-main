<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * In-process cache TTL in seconds.
     */
    protected const CACHE_TTL = 300;

    /**
     * Fallback defaults used if a key has not been created/seeded yet.
     */
    protected static array $defaults = [
        'site.announcement.enabled'  => ['value' => false, 'type' => 'bool',   'group' => 'general'],
        'site.announcement.title'    => ['value' => '',    'type' => 'string', 'group' => 'general'],
        'site.announcement.message'  => ['value' => '',    'type' => 'string', 'group' => 'general'],
        'site.announcement.variant'  => ['value' => 'info', 'type' => 'string', 'group' => 'general'],

        'user.registration.enabled'  => ['value' => true, 'type' => 'bool', 'group' => 'features'],
        'user.google_login.enabled'  => ['value' => true, 'type' => 'bool', 'group' => 'features'],
        'user.chatbot.enabled'       => ['value' => true, 'type' => 'bool', 'group' => 'features'],
        'user.campaigns.public'      => ['value' => true, 'type' => 'bool', 'group' => 'features'],
        'user.events.public'         => ['value' => true, 'type' => 'bool', 'group' => 'features'],
        'user.inkind.public'         => ['value' => true, 'type' => 'bool', 'group' => 'features'],

        'site.maintenance.enabled'   => ['value' => false, 'type' => 'bool',   'group' => 'maintenance'],
        'site.maintenance.message'   => ['value' => 'We are performing scheduled maintenance. Please check back soon.', 'type' => 'string', 'group' => 'maintenance'],
    ];

    public static function all_keyed(): array
    {
        return Cache::remember('site_settings.all', self::CACHE_TTL, function () {
            $rows = static::query()->get(['key', 'value', 'type']);
            $out = [];
            foreach ($rows as $row) {
                $out[$row->key] = self::castValue($row->value, $row->type);
            }
            // Fill in defaults for any missing keys
            foreach (self::$defaults as $k => $meta) {
                if (!array_key_exists($k, $out)) {
                    $out[$k] = $meta['value'];
                }
            }
            return $out;
        });
    }

    public static function get(string $key, $default = null)
    {
        $all = self::all_keyed();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        if (array_key_exists($key, self::$defaults)) {
            return self::$defaults[$key]['value'];
        }
        return $default;
    }

    public static function isTrue(string $key): bool
    {
        return (bool) self::get($key, false);
    }

    public static function set(string $key, $value, ?string $type = null, ?string $group = null): void
    {
        $type = $type ?? self::$defaults[$key]['type'] ?? 'string';
        $group = $group ?? self::$defaults[$key]['group'] ?? 'general';

        $stored = self::serializeValue($value, $type);

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'group' => $group]
        );

        Cache::forget('site_settings.all');
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::set($key, $value);
        }
    }

    protected static function castValue($raw, string $type)
    {
        return match ($type) {
            'bool'  => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'int'   => (int) $raw,
            'json'  => json_decode($raw ?? 'null', true),
            default => (string) ($raw ?? ''),
        };
    }

    protected static function serializeValue($value, string $type): ?string
    {
        return match ($type) {
            'bool'  => $value ? '1' : '0',
            'int'   => (string) (int) $value,
            'json'  => json_encode($value),
            default => $value === null ? null : (string) $value,
        };
    }
}
