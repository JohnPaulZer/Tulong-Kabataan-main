<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class ImpactReport extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'impact_reports';

    protected $fillable = [
        'title',
        'description',
        'report_date',
        'photos',
        'donation_ids', // Array of InKindDonation _ids (replaces pivot table)
    ];

    protected $casts = [
        'photos' => 'array',
        'donation_ids' => 'array',
        'report_date' => 'date',
    ];

    public function getImpactReportIdAttribute()
    {
        return (string) ($this->attributes['_id'] ?? $this->getKey());
    }

    /**
     * In MongoDB we store donation references as an array of IDs directly
     * on the document instead of using a pivot table. This method provides
     * a compatible interface for attaching donations.
     */
    public function donationQuery()
    {
        return InKindDonation::whereIn('_id', $this->donation_ids ?? []);
    }

    public function getDonationsAttribute()
    {
        if (array_key_exists('donations', $this->relations)) {
            return $this->relations['donations'];
        }

        $ids = collect($this->donation_ids ?? [])
            ->map(fn ($id) => (string) $id)
            ->filter()
            ->values()
            ->all();

        $donations = empty($ids)
            ? collect()
            : InKindDonation::whereIn('_id', $ids)->get();

        $this->setRelation('donations', $donations);

        return $donations;
    }

    /**
     * Attach donation IDs (replaces pivot table attach).
     */
    public function attachDonations(array $ids): void
    {
        $existing = $this->donation_ids ?? [];
        $this->donation_ids = array_values(array_unique(array_merge($existing, $ids)));
        $this->save();
    }

    /**
     * Detach donation IDs.
     */
    public function detachDonations(array $ids): void
    {
        $this->donation_ids = array_values(array_diff($this->donation_ids ?? [], $ids));
        $this->save();
    }
}
