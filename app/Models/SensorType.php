<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SensorType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'unit', 'icon', 'is_core'];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_sensor_types');
    }
}
