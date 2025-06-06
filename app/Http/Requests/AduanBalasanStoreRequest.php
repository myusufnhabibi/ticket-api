<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AduanBalasanStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'status' => auth()->user()->id == 'admin' ? 'required|in:onprogress,resolved,rejected' : 'nullable'
        ];
    }
}
