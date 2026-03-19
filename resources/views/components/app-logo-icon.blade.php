<?php
$letters = Str::of(config('app.name'))->substr(0, 2)->upper();
?>
<svg {{ $attributes }} viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="App Logo">
    <rect x="4" y="4" width="56" height="56" rx="12" stroke="currentColor" stroke-width="2.5"/>
    <text x="32" y="36" text-anchor="middle" dominant-baseline="central" font-family="ui-sans-serif, system-ui, sans-serif" font-size="26" font-weight="700" fill="currentColor">{{ $letters }}</text>
</svg>
