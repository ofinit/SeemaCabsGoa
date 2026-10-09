<?php

namespace App\Models;

use App\Support\Gstin;
use Illuminate\Database\Eloquent\Model;

/**
 * Legal details of an invoicing entity: 'supplier' (Seema Holidays) or
 * 'platform' (OfinIT Solutions Pvt. Ltd.). Edited in admin; snapshotted onto
 * every invoice at issue time.
 */
class BusinessProfile extends Model
{
    public const SUPPLIER = 'supplier';
    public const PLATFORM = 'platform';

    protected $fillable = [
        'legal_name', 'trade_name', 'gstin', 'pan', 'address_line_one', 'address_line_two',
        'city', 'state_name', 'state_code', 'pincode', 'email', 'phone', 'signatory_name',
        'bank_details', 'invoice_prefix',
    ];

    public static function supplier(): ?self
    {
        return static::where('role', self::SUPPLIER)->first();
    }

    public static function platform(): ?self
    {
        return static::where('role', self::PLATFORM)->first();
    }

    /** Fields that must be filled before this entity can issue a tax document. */
    public function missingForInvoicing(): array
    {
        $missing = [];
        foreach (['legal_name' => 'Legal name', 'address_line_one' => 'Address', 'state_code' => 'State code', 'invoice_prefix' => 'Invoice prefix'] as $field => $label) {
            if (blank($this->{$field})) {
                $missing[] = $label;
            }
        }
        if (!Gstin::isValid($this->gstin)) {
            $missing[] = 'Valid GSTIN';
        }

        return $missing;
    }

    public function isReadyForInvoicing(): bool
    {
        return $this->missingForInvoicing() === [];
    }

    public function addressLines(): array
    {
        return array_values(array_filter([
            $this->address_line_one,
            $this->address_line_two,
            trim(implode(' - ', array_filter([$this->city, $this->pincode]))),
            $this->state_name ? $this->state_name . ($this->state_code ? ' (' . $this->state_code . ')' : '') : null,
        ]));
    }

    public function snapshot(): array
    {
        return [
            'legal_name' => $this->legal_name,
            'trade_name' => $this->trade_name,
            'gstin' => $this->gstin,
            'pan' => $this->pan,
            'address' => $this->addressLines(),
            'state_name' => $this->state_name,
            'state_code' => $this->state_code,
            'email' => $this->email,
            'phone' => $this->phone,
            'signatory_name' => $this->signatory_name,
            'bank_details' => $this->bank_details,
        ];
    }
}
