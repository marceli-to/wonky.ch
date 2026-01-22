<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="it">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>

@media only screen and (max-width: 600px) {
  .main {
    padding: 0 16px;
  }

  .table td.currency {
    width: 25px !important;
  }

  .table td.quantity {
    width: 70px !important;
  }

  .table td.amount {
    width: 45px !important;
  }

}
</style>
</head>
<body>
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<table class="content text-base" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;" align="left">
<table class="inner-body" width="570" cellpadding="0" cellspacing="0" role="presentation" align="left">
  {{ $header ?? '' }}
<tr>
<td class="content-cell">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
</td>
</tr>
{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>