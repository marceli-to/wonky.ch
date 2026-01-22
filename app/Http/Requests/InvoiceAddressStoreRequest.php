<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceAddressStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company' => 'required_if:firstname,null|required_if:name,null',
            'name' => 'required_if:company,null',
            'firstname' => 'required_if:company,null',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'country' => 'required',
            'email' => 'required|email:rfc,dns',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company.required_if' => 'Firma fehlt',
            'name.required_if' => 'Name fehlt',
            'firstname.required_if' => 'Vorname fehlt',
            'street.required' => 'Strasse fehlt',
            'zip.required' => 'PLZ fehlt',
            'city.required' => 'Ort fehlt',
            'country.required' => 'Land fehlt',
            'email.required' => 'E-Mail fehlt',
            'email.email' => 'E-Mail ist nicht gültig',
        ];
    }
}
