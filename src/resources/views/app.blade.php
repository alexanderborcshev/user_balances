<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="spa-poll-interval" content="{{ env('SPA_POLL_INTERVAL_SEC', 10) }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/js/main.ts','resources/styles/main.scss'])
</head>
<body>
<div id="app"></div>
</body>
</html>
