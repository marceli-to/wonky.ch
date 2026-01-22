<?php

namespace App\Livewire\Checkout;

use App\Actions\Cart\Update as UpdateCart;
use Livewire\Component;

class InvoiceAddress extends Component
{
    public string $salutation = 'Herr';

    public string $firstname = 'Marcel';

    public string $lastname = 'Stadelmann';

    public string $street = 'Letzigraben';

    public string $street_number = '149';

    public string $zip = '8047';

    public string $city = 'Zürich';

    public string $country = 'Schweiz';

    public string $email = 'marcel.stadelmann@gmail.com';

    public string $phone = '+41 78 749 74 09';

    protected $rules = [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'street' => 'required|string|max:255',
        'street_number' => 'nullable|string|max:50',
        'zip' => 'required|string|max:20',
        'city' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:50',
        'salutation' => 'nullable|string|max:50',
    ];

    protected $messages = [
        'firstname.required' => 'Bitte geben Sie Ihren Vornamen ein.',
        'lastname.required' => 'Bitte geben Sie Ihren Nachnamen ein.',
        'street.required' => 'Bitte geben Sie Ihre Strasse ein.',
        'zip.required' => 'Bitte geben Sie Ihre PLZ ein.',
        'city.required' => 'Bitte geben Sie Ihren Ort ein.',
        'country.required' => 'Bitte geben Sie Ihr Land ein.',
        'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
        'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        'phone.required' => 'Bitte geben Sie Ihre Telefonnummer ein.',
    ];

    public function mount(): void
    {
        $address = session()->get('invoice_address', []);

        if (! empty($address)) {
            $this->salutation = $address['salutation'] ?? $this->salutation;
            $this->firstname = $address['firstname'] ?? $this->firstname;
            $this->lastname = $address['lastname'] ?? $this->lastname;
            $this->street = $address['street'] ?? $this->street;
            $this->street_number = $address['street_number'] ?? $this->street_number;
            $this->zip = $address['zip'] ?? $this->zip;
            $this->city = $address['city'] ?? $this->city;
            $this->country = $address['country'] ?? $this->country;
            $this->email = $address['email'] ?? $this->email;
            $this->phone = $address['phone'] ?? $this->phone;
        }
    }

    public function save(): void
    {
        $this->validate();

        session()->put('invoice_address', [
            'salutation' => $this->salutation,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'street' => $this->street,
            'street_number' => $this->street_number,
            'zip' => $this->zip,
            'city' => $this->city,
            'country' => $this->country,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        (new UpdateCart)->execute(['order_step' => 2]);

        $this->redirect(route('page.checkout.delivery-address'));
    }

    public function render()
    {
        return view('livewire.checkout.invoice-address');
    }
}
