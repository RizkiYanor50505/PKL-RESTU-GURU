<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'integer', 'exists:'.(new Asset)->getTable().',id'],
            'reporter_name' => ['required', 'string', 'max:100'],
            'issue_description' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
