@props([
  'title' => null
])

@php
$appName = config('app.name', 'Wonky');
$pageTitle = filled($title) ? "{$title} – {$appName}" : $appName;
@endphp

<!doctype html>
<html lang="de" class="h-full bg-white scroll-smooth overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $pageTitle }}</title> 
<meta name="description" content="{{ config('app.description') }}">
<meta property="og:title" content="{{ $pageTitle }}"> 
<meta property="og:description" content="{{ config('app.description') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Wonky" />
<link rel="manifest" href="/site.webmanifest" />
@vite('resources/css/app.css')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
@livewireStyles
</head>