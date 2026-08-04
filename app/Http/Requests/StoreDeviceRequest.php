<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['superadmin', 'admin_lokasi']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * REVISI FUZZY ON-DEVICE: empat field threshold_tma_/threshold_hujan_
     * DIHAPUS - kolomnya sudah tidak ada di tabel devices (lihat migration
     * 2026_08_04_000001), karena status sekarang sepenuhnya dihitung di
     * microcontroller, bukan dari ambang yang diset lewat dashboard ini.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:255', 'unique:devices,device_id'],
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
