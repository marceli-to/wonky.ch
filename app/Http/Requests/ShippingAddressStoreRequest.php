<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressStoreRequest extends FormRequest
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
            'use_invoice_address' => 'boolean',
            'firstname' => 'required_unless:use_invoice_address,1',
            'name' => 'required_unless:use_invoice_address,1',
            'street' => 'required_unless:use_invoice_address,1',
            'zip' => 'required_unless:use_invoice_address,1',
            'city' => 'required_unless:use_invoice_address,1',
            'country' => 'required_unless:use_invoice_address,1',
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
            'name.required_unless' => 'Name fehlt',
            'firstname.required_unless' => 'Vorname fehlt',
            'street.required_unless' => 'Strasse fehlt',
            'zip.required_unless' => 'PLZ fehlt',
            'city.required_unless' => 'Ort fehlt',
            'country.required_unless' => 'Land fehlt',
        ];
    }
}
