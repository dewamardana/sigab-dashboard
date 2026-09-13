<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->user()->hasAnyRole(['superadmin', 'admin_lokasi'])) {
            return false;
        }

        $device = $this->route('device');

        return in_array($device->location_id, $this->allowedLocationIds());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * REVISI FUZZY ON-DEVICE: empat field threshold_tma_/threshold_hujan_
     * DIHAPUS - lihat catatan yang sama di StoreDeviceRequest.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $device = $this->route('device');

        return [
            'device_id' => ['required', 'string', 'max:255', Rule::unique('devices', 'device_id')->ignore($device->id)],
            'name' => ['nullable', 'string', 'max:255'],
            'location_id' => ['required', 'integer', Rule::in($this->allowedLocationIds())],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sensor_type_ids' => ['nullable', 'array'],
            'sensor_type_ids.*' => ['integer', 'exists:sensor_types,id'],
        ];
    }

    private function allowedLocationIds(): array
    {
        if ($this->user()->hasRole('superadmin')) {
            return Location::pluck('id')->toArray();
        }

        return $this->user()->locations()->pluck('locations.id')->toArray();
    }
}
