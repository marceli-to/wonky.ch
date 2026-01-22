@php
$fontPath = resource_path('sidecar-browsershot/fonts/');
$fontMedium = base64_encode(file_get_contents($fontPath . 'Muoto-Medium.woff2'));
$fontRegular = base64_encode(file_get_contents($fontPath . 'Muoto-Regular.woff2'));
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Rechnung {{ $order->order_number }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    
    @font-face {
      font-family: 'Muoto';
      src: url('data:font/woff2;base64,{{ $fontMedium }}') format('woff2');
      font-weight: 500;
      font-style: normal;
    }
    
    @font-face {
      font-family: 'Muoto';
      src: url('data:font/woff2;base64,{{ $fontRegular }}') format('woff2');
      font-weight: 400;
      font-style: normal;
    }

    @page :first {
      margin-top: 6mm;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Muoto', sans-serif;
      font-size: 9pt;
      line-height: 14pt;
      font-weight: 400;
      color: #000;
    }

    strong {
      font-weight: normal;
    }

    .font-medium {
      font-weight: 500;
    }

    /* Sender info - top left at 6mm */
    .invoice__sender {
      position: absolute;
      top: 0mm;
      left: 0;
      font-size: 9pt;
      line-height: 1.2;
    }

    /* Website - top right */
    .invoice__website {
      position: absolute;
      top: 8.4mm;
      left: 171.5mm;
      font-size: 15pt;
      font-weight: 400;
    }

    /* Date - below sender info */
    .invoice__date {
      position: absolute;
      top: 25mm;
      left: 42mm;
      font-size: 9pt;
    }

    /* Recipient address - starts at 93mm from top */
    .invoice__recipient {
      position: absolute;
      top: 45mm;
      left: 42mm;
      font-size: 9pt;
    }

    /* Invoice header - starts at 92mm from top */
    .invoice__title {
      position: absolute;
      top: 84mm;
      left: 42mm;
      font-size: 14pt;
      font-weight: 400;
    }

    /* Contact details - bottom left */
    .invoice__contact {
      position: absolute;
      top: 240mm;
      left: 0mm;
      width: 42mm;
    }

    .invoice__contact-block {
      margin-bottom: 3mm;
    }

    /* Main content area */
    .invoice__content {
      position: absolute;
      top: 114mm;
      left: 42mm;
    }

    /* Invoice item table */
    .invoice__item {
      width: 150mm;
      border-collapse: collapse;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice__item + .invoice__item {
      margin-top: 7.5mm;
    }

    .invoice__item-qty {
      border-top: 0.15mm solid black;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice__item-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    .invoice__row {
      display: flex;
      border-bottom: 0.15mm solid black;
    }

    .invoice__row-label {
      display: flex;
      align-items: center;
      width: 110mm;
      padding: 1.5mm 0;
    }

    .invoice__row-currency {
      width: 6mm;
      padding: 1.5mm 0;
    }

    .invoice__row-price {
      width: 26mm;
      padding: 1.5mm 0;
      text-align: right;
    }

    .invoice__row--detail {
      display: flex;
      align-items: center;
      border-bottom: 0.15mm solid black;
      padding: 1.5mm 0;
    }

    /* Totals table */
    .invoice__totals {
      width: 150mm;
      border-collapse: collapse;
      margin-top: 9mm;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice__totals-qty {
      border-top: 0.15mm solid transparent;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice__totals-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    /* Payment table */
    .invoice__payment {
      width: 150mm;
      border-collapse: collapse;
      margin-top: 9mm;
      page-break-inside: avoid;
      break-inside: avoid;
    }

    .invoice__payment-qty {
      border-top: 0.15mm solid transparent;
      width: 8mm;
      vertical-align: top;
      padding: 1.5mm 0 0 0;
    }

    .invoice__payment-content {
      border-top: 0.15mm solid black;
      vertical-align: top;
    }

    .invoice__no-break {
      page-break-inside: avoid;
    }

  </style>
</head>
<body>
  <div class="page">

    <!-- Sender info - top left -->
    <div class="invoice__sender">
      <span class="font-medium">Wonky Studio</span><br>
      Balthasar Bosshard<br>
      Hermannstrasse 11a<br>
      Winterthur, Switzerland
    </div>

    <!-- Website - top right -->
    <div class="invoice__website">
      wonky.ch
    </div>

    <!-- Date -->
    <div class="invoice__date">
      Winterthur, {{ $order->created_at->locale('de')->isoFormat('D. MMMM YYYY') }}
    </div>

    <!-- Recipient address -->
    <div class="invoice__recipient">
      @if ($order->invoice_salutation){{ $order->invoice_salutation }}<br>@endif
      {{ $order->invoice_name }}<br>
      {{ $order->invoice_address }}<br>
      {{ $order->invoice_location }}
    </div>

    <!-- Invoice header -->
    <div class="invoice__title">
      Rechnung Nr. {{ $order->order_number }}
    </div>

    <!-- Contact details - bottom left -->
    <div class="invoice__contact">
      @include('pdf.partials.contact-details')
    </div>

    <!-- Main content -->
    <div class="invoice__content">

      <!-- Items -->
      @foreach($order->items as $index => $item)
        <table cellpadding="0" cellspacing="0" class="invoice__item">
          <tr>
            <td class="invoice__item-qty font-medium">{{ $item->quantity }}</td>
            <td class="invoice__item-content">
              <div class="invoice__row">
                <div class="invoice__row-label font-medium">{{ $item->product_name }}</div>
                <div class="invoice__row-currency">Fr.</div>
                <div class="invoice__row-price">{!! number_format($item->subtotal, 2, '.', "'") !!}</div>
              </div>
              @if($item->product_label)
                <div class="invoice__row--detail">{{ $item->product_label }}</div>
              @endif
              @if($item->product_description)
                <div class="invoice__row--detail">{{ $item->product_description }}</div>
              @endif
              <div class="invoice__row">
                <div class="invoice__row-label">{{ $item->shipping_name ?? ($item->shipping_price > 0 ? 'Versand' : 'Abholung') }}</div>
                <div class="invoice__row-currency">{{ $item->shipping_price > 0 ? 'Fr.' : '' }}</div>
                <div class="invoice__row-price">{{ $item->shipping_price > 0 ? number_format($item->shipping_price, 2, '.', "'") : '' }}</div>
              </div>
            </td>
          </tr>
        </table>
      @endforeach

      <div class="invoice__no-break">
        <!-- Totals -->
        <table cellpadding="0" cellspacing="0" class="invoice__totals">
          <tr>
            <td class="invoice__totals-qty">&nbsp;</td>
            <td class="invoice__totals-content">
              <div class="invoice__row">
                <div class="invoice__row-label">Netto</div>
                <div class="invoice__row-currency">Fr.</div>
                <div class="invoice__row-price">{!! number_format($order->subtotal + $order->shipping, 2, '.', "'") !!}</div>
              </div>
              <div class="invoice__row">
                <div class="invoice__row-label">MwSt.</div>
                <div class="invoice__row-currency">Fr.</div>
                <div class="invoice__row-price">{!! number_format($order->tax, 2, '.', "'") !!}</div>
              </div>
              <div class="invoice__row">
                <div class="invoice__row-label font-medium">Total</div>
                <div class="invoice__row-currency font-medium">Fr.</div>
                <div class="invoice__row-price font-medium">{!! number_format($order->total, 2, '.', "'") !!}</div>
              </div>
            </td>
          </tr>
        </table>

        <!-- Payment -->
        <table cellpadding="0" cellspacing="0" class="invoice__payment">
          <tr>
            <td class="invoice__payment-qty">&nbsp;</td>
            <td class="invoice__payment-content">
              <div class="invoice__row">
                <div class="invoice__row-label">
                  @if($order->payment_method === 'invoice')
                    Zahlung: Rechnung / {{ $order->created_at->addDays(30)->format('d.m.Y') }}
                  @else
                    Zahlung: {{ ucfirst($order->payment_method) }} / {{ $order->paid_at ? $order->paid_at->format('d.m.Y, H:i') : '' }}
                  @endif
                </div>
                <div class="invoice__row-currency">Fr.</div>
                <div class="invoice__row-price">{!! number_format($order->total, 2, '.', "'") !!}</div>
              </div>
            </td>
          </tr>
        </table>
        
      </div>

    </div>

  </div>
</body>
</html>
