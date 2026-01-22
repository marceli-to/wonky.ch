@php
$fontPath = resource_path('sidecar-browsershot/fonts/');
$fontRegular = base64_encode(file_get_contents($fontPath . 'Muoto-Regular.woff2'));
@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<style>
  @font-face {
    font-family: 'Muoto';
    src: url('data:font/woff2;base64,{{ $fontRegular }}') format('woff2');
    font-weight: 400;
    font-style: normal;
  }
  body {
    font-family: 'Muoto', sans-serif;
    font-size: 9pt;
    margin: 0;
    padding: 0;
  }
  footer {
    position: absolute;
    top: 31.1mm;
    left: 6mm;
  }
</style>
</head>
<body>
  <footer>
    @pageNumber / @totalPages
  </footer>
</body>
</html>
