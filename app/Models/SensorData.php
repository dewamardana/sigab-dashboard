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
        'readings',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'readings' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
    
    public function getReading(string $code): mixed
    {
        $value = $this->readings[$code] ?? null;

        if (is_bool($value)) {
            return $value ? 'Aktif' : 'Normal';
        }

        return $value;
    }
}
