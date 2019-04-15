<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        $id = $this->route('post')->id;
        return [
            'title' => [
                'required',
                Rule::unique('posts')->where('id', '<>', $id),
            ],
            'body' => 'required',
        ];
    }
}
