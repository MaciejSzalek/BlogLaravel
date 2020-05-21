<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class PostFromRequest extends FormRequest
{
    public function authorize()
    {
        if($this->user()->can_post()) {
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
          'title' => 'required|unique:posts|max:255',
          //'title' => array('Regex:/^[A-Za-z0-9]+$/'),
          'body' => 'required',
        ];
    }
}