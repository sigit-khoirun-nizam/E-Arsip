<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:archives,code,' . $this->archive->id,
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'letter_type_id' => 'nullable|exists:letter_types,id',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'status' => 'required|in:active,inactive,permanent',
            'shelf_code' => 'nullable|string|max:50',
            'is_confidential' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode arsip wajib diisi.',
            'code.unique' => 'Kode arsip sudah digunakan.',
            'title.required' => 'Judul arsip wajib diisi.',
            'file_path.mimes' => 'Format file tidak valid.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}
