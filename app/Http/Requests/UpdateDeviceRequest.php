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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $device = $this->route('device');

        return [
            'device_id' => ['required', 'string', 'max:255', Rule::unique('devices', 'device_id')->ignore($device->id)],
            'name' => ['nullable', 'string', 'max:255'],
            'location_id' => ['required', 'integer', Rule::in($this->allowedLocationIds())],
            'threshold_tma_siaga' => ['required', 'numeric', 'min:0'],
            'threshold_tma_bahaya' => ['required', 'numeric', 'gt:threshold_tma_siaga'],
            'threshold_hujan_siaga' => ['required', 'numeric', 'min:0'],
            'threshold_hujan_bahaya' => ['required', 'numeric', 'gt:threshold_hujan_siaga'],
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
