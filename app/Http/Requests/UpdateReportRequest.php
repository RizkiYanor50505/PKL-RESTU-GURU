<?php

namespace App\Http\Requests;

use App\Enums\ReportStatus;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->canHandleReports();
    }

    /**
     * Teknisi yang memproses/menyelesaikan laporan otomatis tercatat
     * sebagai penanggung jawab jika kolom teknisi dikosongkan.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if (
            $user?->isTechnician()
            && blank($this->input('technician_id'))
            && in_array($this->input('status'), [ReportStatus::Diproses->value, ReportStatus::Selesai->value], true)
        ) {
            $this->merge(['technician_id' => $user->id]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(ReportStatus::values())],
            'technician_id' => [
                'nullable',
                'required_if:status,'.ReportStatus::Diproses->value.','.ReportStatus::Selesai->value,
                Rule::exists('users', 'id'),
            ],
            'repair_notes' => ['nullable', 'required_if:status,'.ReportStatus::Selesai->value, 'string', 'max:3000'],
            'replaced_parts' => ['nullable', 'string', 'max:255'],
            'repair_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status penanganan wajib dipilih.',
            'technician_id.required_if' => 'Teknisi penanggung jawab wajib dipilih saat status laporan diproses atau selesai.',
            'technician_id.exists' => 'Teknisi yang dipilih tidak valid di sistem.',
            'repair_notes.required_if' => 'Uraian penanganan wajib diisi saat laporan dinyatakan selesai.',
        ];
    }
}
