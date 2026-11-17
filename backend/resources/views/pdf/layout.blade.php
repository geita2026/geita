<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 34px 40px 60px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1b1b22; line-height: 1.45; }
  .head { border-bottom: 3px solid #b8860b; padding-bottom: 10px; margin-bottom: 18px; }
  .brand { font-size: 20px; font-weight: bold; letter-spacing: .5px; }
  .brand small { display: block; font-size: 9px; font-weight: normal; color: #666; letter-spacing: 1.5px; text-transform: uppercase; }
  .doc { float: right; text-align: right; }
  .doc h1 { margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 2px; color: #b8860b; }
  h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #666; margin: 18px 0 6px; }
  table { width: 100%; border-collapse: collapse; }
  .grid th { background: #f3efe4; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; padding: 7px 8px; border-bottom: 1px solid #d9d2bd; }
  .grid td { padding: 7px 8px; border-bottom: 1px solid #ece8dc; vertical-align: top; }
  .num { text-align: right; white-space: nowrap; }
  .meta td { padding: 2px 0; vertical-align: top; }
  .meta .k { color: #777; width: 130px; }
  .totals { width: 46%; margin-left: 54%; margin-top: 10px; }
  .totals td { padding: 5px 8px; }
  .totals .grand td { border-top: 2px solid #1b1b22; font-weight: bold; font-size: 13px; }
  .box { border: 1px solid #d9d2bd; background: #fbf9f3; padding: 10px 12px; margin-top: 16px; }
  .pill { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .8px; background: #eee; }
  .paid { background: #d9f2e1; color: #17643a; } .due { background: #fde4e1; color: #a02a1c; }
  .foot { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding-top: 5px; }
  .muted { color: #777; } .clear { clear: both; }
</style>
</head>
<body>
  <div class="head">
    <div class="doc"><h1>@yield('title')</h1><div class="muted">@yield('number')</div></div>
    <div class="brand">GeMEMIS<small>Geita Mineral Exhibition Management Information System</small></div>
    <div class="clear"></div>
  </div>
  @yield('content')
  <div class="foot">
    Generated {{ $generatedAt->format('d M Y H:i') }} by {{ $generatedBy }} · This is a system-generated document; verify it against the reference number in GeMEMIS.
  </div>
</body>
</html>
