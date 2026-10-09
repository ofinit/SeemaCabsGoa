<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/** Audit trail for pricing, GST, platform-fee and business-profile edits. */
class SettingChange extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    public static function record(string $key, mixed $old, mixed $new): void
    {
        if ((string) $old === (string) $new) {
            return;
        }

        static::create([
            'user_id' => Auth::id(),
            'key' => $key,
            'old_value' => $old === null ? null : (string) $old,
            'new_value' => $new === null ? null : (string) $new,
        ]);
    }

    /** Update an `environments` row (creating it if missing) and audit the change. */
    public static function setEnvironment(string $title, mixed $value): void
    {
        $row = Environment::where('title', $title)->first();
        $old = $row?->value;

        if ($row) {
            $row->value = $value;
            $row->save();
        } else {
            Environment::create(['title' => $title, 'value' => $value]);
        }

        static::record($title, $old, $value);
    }
}
