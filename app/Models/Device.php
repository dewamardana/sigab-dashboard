<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'location_id',
        'name',
        'is_active',
        'threshold_tma_siaga',
        'threshold_tma_bahaya',
        'threshold_hujan_siaga',
        'threshold_hujan_bahaya',
        'telegram_chat_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold_tma_siaga' => 'float',
        'threshold_tma_bahaya' => 'float',
        'threshold_hujan_siaga' => 'float',
        'threshold_hujan_bahaya' => 'float',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function sensorTypes(): BelongsToMany
    {
        return $this->belongsToMany(SensorType::class, 'device_sensor_types');
    }
}
