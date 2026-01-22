@php
$logoSvg = base64_encode(file_get_contents(public_path('img/logo.svg')));
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<style>
  body {
    margin: 0;
    padding: 0;
  }
  .logo-area {
    position: absolute;
    top: 120mm;
    left: 6mm;
    width: 17.5mm;
  }

  .logo-area .logo {
    width: 17.5mm;
    height: auto;
  }
</style>
</head>
<body>
  <!-- Logo -->
  <div class="logo-area">
    <img src="data:image/svg+xml;base64,{{ $logoSvg }}" alt="wonky.ch" class="logo">
  </div>
</body>
</html>
