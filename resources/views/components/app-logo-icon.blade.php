<?php
$letters = Str::of(config('app.name'))->substr(0, 2)->upper();
?>
<svg {{ $attributes }} viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="App Logo">
    <rect x="2" y="2" width="60" height="60" rx="4" fill="currentColor" stroke="var(--color-accent-content)" stroke-width="6"/>
    <text x="32" y="32" text-anchor="middle" dominant-baseline="central" font-family="ui-sans-serif, system-ui, sans-serif" font-size="26" font-weight="700" fill="var(--color-accent-content)">{{ $letters }}</text>
</svg>

