<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'platform',
        'status',
        'logged_in_at',
        'logged_out_at',
    ];

    protected $casts = [
        'logged_in_at'  => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse a basic device/browser/platform from User-Agent string.
     */
    public static function parseUserAgent(string $ua): array
    {
        $device = 'desktop';
        if (preg_match('/mobile/i', $ua)) {
            $device = 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $ua)) {
            $device = 'tablet';
        }

        $browser = 'Unknown';
        foreach ([
            'Edg'     => 'Edge',
            'OPR'     => 'Opera',
            'Chrome'  => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari'  => 'Safari',
        ] as $key => $name) {
            if (str_contains($ua, $key)) {
                $browser = $name;
                break;
            }
        }

        $platform = 'Unknown';
        foreach ([
            'Windows' => 'Windows',
            'Mac'     => 'macOS',
            'Linux'   => 'Linux',
            'Android' => 'Android',
            'iPhone'  => 'iOS',
            'iPad'    => 'iOS',
        ] as $key => $name) {
            if (str_contains($ua, $key)) {
                $platform = $name;
                break;
            }
        }

        return compact('device', 'browser', 'platform');
    }
}
