<?php

namespace App\Livewire\Checkout;

use App\Actions\Cart\Update as UpdateCart;
use Livewire\Component;

class DeliveryAddress extends Component
{
    public bool $use_invoice_address = true;

    public string $salutation = '';

    public string $firstname = '';

    public string $lastname = '';

    public string $street = '';

    public string $street_number = '';

    public string $zip = '';

    public string $city = '';

    public string $country = 'Schweiz';

    protected $messages = [
        'firstname.required' => 'Bitte geben Sie Ihren Vornamen ein.',
        'lastname.required' => 'Bitte geben Sie Ihren Nachnamen ein.',
        'street.required' => 'Bitte geben Sie Ihre Strasse ein.',
        'zip.required' => 'Bitte geben Sie Ihre PLZ ein.',
        'city.required' => 'Bitte geben Sie Ihren Ort ein.',
        'country.required' => 'Bitte geben Sie Ihr Land ein.',
    ];

    public function mount(): void
    {
        // Check if we previously set use_invoice_address
        $this->use_invoice_address = session()->get('use_invoice_address', true);

        $address = session()->get('delivery_address', []);

        if (! empty($address)) {
            $this->salutation = $address['salutation'] ?? $this->salutation;
            $this->firstname = $address['firstname'] ?? $this->firstname;
            $this->lastname = $address['lastname'] ?? $this->lastname;
            $this->street = $address['street'] ?? $this->street;
            $this->street_number = $address['street_number'] ?? $this->street_number;
            $this->zip = $address['zip'] ?? $this->zip;
            $this->city = $address['city'] ?? $this->city;
            $this->country = $address['country'] ?? $this->country;
        }
    }

    public function save(): void
    {
        // Store the use_invoice_address preference
        session()->put('use_invoice_address', $this->use_invoice_address);

        if ($this->use_invoice_address) {
            // Clear delivery address if using invoice address
            session()->forget('delivery_address');
        } else {
            // Validate and store delivery address
            $this->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'street_number' => 'nullable|string|max:50',
                'zip' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'salutation' => 'nullable|string|max:50',
            ]);

            session()->put('delivery_address', [
                'salutation' => $this->salutation,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'street' => $this->street,
                'street_number' => $this->street_number,
                'zip' => $this->zip,
                'city' => $this->city,
                'country' => $this->country,
            ]);
        }

        (new UpdateCart)->execute(['order_step' => 3]);

        $this->redirect(route('page.checkout.payment'));
    }

    public function render()
    {
        return view('livewire.checkout.delivery-address');
    }
}
