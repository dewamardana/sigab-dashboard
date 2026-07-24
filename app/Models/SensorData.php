<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorData extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'tma_cm',
        'hujan_mm',
        'readings',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'readings' => 'array',
        'tma_cm' => 'float',
        'hujan_mm' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function getReading(string $code): mixed
    {
        return match ($code) {
            'tma_cm' => $this->tma_cm,
            'hujan_mm' => $this->hujan_mm,
            default => $this->readings[$code] ?? null,
        };
    }
}
