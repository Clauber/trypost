<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <style>body{font-family:system-ui,sans-serif;max-width:720px;margin:3rem auto;padding:0 1rem;line-height:1.6;color:#1f2937}h1{font-size:1.75rem}h2{font-size:1.15rem;margin-top:2rem}</style>
</head>
<body>
    @yield('content')
    <p><small>Contact: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></small></p>
</body>
</html>
