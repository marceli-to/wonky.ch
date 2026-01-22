<x-mail::message>
  <div class="main text-base">
    <p>Folgende Bestellung ist eingegangen:</p>
    <div class="table">
      <table cellpadding="0" cellspacing="0">
        @foreach($data->items as $item)
          <tr>
            <td><strong>{{ $item->product_name }}</strong></td>
            <td class="currency">Fr.</td>
            <td class="amount text-right">{!! number_format($item->subtotal, 2, '.', "'") !!}</td>
          </tr>
          @if($item->product_label)
            <tr>
              <td colspan="3">{{ $item->product_label }}</td>
            </tr>
          @endif
          @if($item->product_description)
            <tr>
              <td colspan="3">{{ $item->product_description }}</td>
            </tr>
          @endif
          <tr>
            <td>{{ $item->shipping_name ?? ($item->shipping_price > 0 ? 'Versand' : 'Abholung') }}</td>
            <td class="currency">{{ $item->shipping_price > 0 ? 'Fr.' : '' }}</td>
            <td class="amount text-right">{{ $item->shipping_price > 0 ? number_format($item->shipping_price, 2, '.', "'") : '' }}</td>
          </tr>
          @if(!$loop->last)
            <tr>
              <td colspan="3" class="no-border">&nbsp;</td>
            </tr>
          @endif
        @endforeach
        <tr>
          <td>Netto</td>
          <td class="currency">Fr.</td>
          <td class="amount text-right">{!! number_format($data->subtotal, 2, '.', "'") !!}</td>
        </tr>
        <tr>
          <td>MwSt. ({{ config('invoice.tax_rate') }}%)</td>
          <td class="currency">Fr.</td>
          <td class="amount text-right">{!! number_format($data->tax, 2, '.', "'") !!}</td>
        </tr>
        <tr>
          <td><strong>Total</strong></td>
          <td class="currency"><strong>Fr.</strong></td>
          <td class="amount text-right"><strong>{!! number_format($data->total, 2, '.', "'") !!}</strong></td>
        </tr>
        <tr>
          <td colspan="3" class="no-border">&nbsp;</td>
        </tr>
        <tr>
          <td>Zahlung: {{ $data->payment_method === 'creditcard' ? 'Kreditkarte' : 'Rechnung' }} / {{ $data->created_at->format('d.m.Y, H:i') }}</td>
          <td class="currency">Fr.</td>
          <td class="amount text-right">{!! number_format($data->total, 2, '.', "'") !!}</td>
        </tr>
        <tr>
          <td colspan="3" class="no-border">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3"><strong>Rechnungsadresse</strong></td>
        </tr>
        @if ($data->invoice_salutation)
          <tr>
            <td colspan="3">{{ $data->invoice_salutation }}</td>
          </tr>
        @endif
        <tr>
          <td colspan="3">{{ $data->invoice_name }}</td>
        </tr>
        <tr>
          <td colspan="3">{{ $data->invoice_address }}</td>
        </tr>
        <tr>
          <td colspan="3">{{ $data->invoice_location }}</td>
        </tr>
        <tr>
          <td colspan="3">{{ $data->invoice_country }}</td>
        </tr>
        <tr>
          <td colspan="3">{{ $data->invoice_email }}</td>
        </tr>
        @if ($data->invoice_phone)
          <tr>
            <td colspan="3">{{ $data->invoice_phone }}</td>
          </tr>
        @endif
        <tr>
          <td colspan="3" class="no-border">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3"><strong>Lieferadresse</strong></td>
        </tr>
        @if ($data->use_invoice_address)
          @if ($data->invoice_salutation)
            <tr>
              <td colspan="3">{{ $data->invoice_salutation }}</td>
            </tr>
          @endif
          <tr>
            <td colspan="3">{{ $data->invoice_name }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->invoice_address }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->invoice_location }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->invoice_country }}</td>
          </tr>
        @else
          @if ($data->shipping_salutation)
            <tr>
              <td colspan="3">{{ $data->shipping_salutation }}</td>
            </tr>
          @endif
          <tr>
            <td colspan="3">{{ $data->shipping_name }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->shipping_address }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->shipping_location }}</td>
          </tr>
          <tr>
            <td colspan="3">{{ $data->shipping_country }}</td>
          </tr>
        @endif
      </table>
    </div>
    <div class="pt-lg">
      <strong>Wonky Studio</strong><br>Balthasar Bosshard<br>Hermannstrasse 11a<br>Winterthur, Switzerland
    </div>
  </div>
</x-mail::message>
