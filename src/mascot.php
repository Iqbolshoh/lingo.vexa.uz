<?php

declare(strict_types=1);

/**
 * Lingo — the LingoUz owl mascot. A layered inline SVG whose parts (eyes,
 * eyelids, beak, wings, scarf, book) are animated by CSS in mascot.css.
 *
 * States: idle, welcome, thinking, learning, success, celebrate, mistake,
 * encourage, sleeping, speaking. Switch at runtime with Owl.play(el, state).
 */

const MASCOT_STATES = ['idle', 'welcome', 'thinking', 'learning', 'success', 'celebrate', 'mistake', 'encourage', 'sleeping', 'speaking'];
const MASCOT_NAME = 'Lingo';

function mascot(string $state = 'idle', int $size = 120, string $class = '', array $attrs = []): string
{
    static $n = 0;
    $n++;
    $state = in_array($state, MASCOT_STATES, true) ? $state : 'idle';
    $extra = '';
    foreach ($attrs as $k => $v) {
        $extra .= ' ' . htmlspecialchars((string) $k) . '="' . htmlspecialchars((string) $v) . '"';
    }
    return '<span class="owl owl--' . $state . ($class !== '' ? ' ' . htmlspecialchars($class) : '') . '" data-state="' . $state . '"'
        . ' style="--owl-size:' . $size . 'px" role="img" aria-label="' . MASCOT_NAME . '"' . $extra . '>'
        . mascot_svg('o' . $n) . '</span>';
}

/** Raw SVG markup; $p prefixes gradient ids so several owls can share a page. */
function mascot_svg(string $p, string $embeddedStyle = ''): string
{
    $confetti = '';
    $colors = ['#f59e0b', '#10b981', '#38bdf8', '#f43f5e', '#a78bfa', '#fbbf24'];
    for ($i = 0; $i < 12; $i++) {
        $x = 28 + ($i * 13) % 150;
        $confetti .= '<rect class="owl-confetti-bit" x="' . $x . '" y="' . (8 + ($i * 7) % 30) . '" width="6" height="9" rx="1.5" fill="' . $colors[$i % 6] . '" style="--d:' . ($i * 0.11) . 's;--r:' . (($i % 2 ? 1 : -1) * (120 + $i * 25)) . 'deg"/>';
    }

    return <<<SVG
<svg class="owl-svg" viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">{$embeddedStyle}
  <defs>
    <radialGradient id="{$p}-body" cx="38%" cy="28%" r="80%">
      <stop offset="0" stop-color="#2fae9a"/><stop offset=".55" stop-color="#0f766e"/><stop offset="1" stop-color="#0b4f4a"/>
    </radialGradient>
    <radialGradient id="{$p}-belly" cx="45%" cy="30%" r="75%">
      <stop offset="0" stop-color="#fffaf0"/><stop offset="1" stop-color="#f3dfbd"/>
    </radialGradient>
    <radialGradient id="{$p}-face" cx="50%" cy="35%" r="70%">
      <stop offset="0" stop-color="#fffdf7"/><stop offset="1" stop-color="#f6e5c6"/>
    </radialGradient>
    <radialGradient id="{$p}-iris" cx="40%" cy="35%" r="70%">
      <stop offset="0" stop-color="#b9773a"/><stop offset="1" stop-color="#6b3a14"/>
    </radialGradient>
    <linearGradient id="{$p}-wing" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#14897c"/><stop offset="1" stop-color="#0a5a53"/>
    </linearGradient>
    <linearGradient id="{$p}-scarf" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#5eead4"/><stop offset="1" stop-color="#0d9488"/>
    </linearGradient>
    <linearGradient id="{$p}-beak" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#fde047"/><stop offset="1" stop-color="#f59e0b"/>
    </linearGradient>
    <linearGradient id="{$p}-book" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#fde68a"/><stop offset="1" stop-color="#f59e0b"/>
    </linearGradient>
  </defs>

  <ellipse class="owl-shadow" cx="100" cy="221" rx="46" ry="6" fill="#0b2e2b" opacity=".18"/>
  <g class="owl-confetti">{$confetti}</g>

  <g class="owl-rig">
    <g class="owl-feet">
      <path d="M76 204c-4 5-9 7-12 6 3-3 4-5 4-8zm8 1c-1 6-4 9-7 10 1-4 1-7 0-9zm7-2c1 6 0 9-2 11-1-4-2-7-3-9z" fill="#f97316"/>
      <path d="M124 204c4 5 9 7 12 6-3-3-4-5-4-8zm-8 1c1 6 4 9 7 10-1-4-1-7 0-9zm-7-2c-1 6 0 9 2 11 1-4 2-7 3-9z" fill="#f97316"/>
    </g>

    <g class="owl-breath">
      <!-- ear tufts -->
      <path d="M52 52c-6-16-4-28 2-36 6 10 14 18 24 22z" fill="url(#{$p}-body)"/>
      <path d="M148 52c6-16 4-28-2-36-6 10-14 18-24 22z" fill="url(#{$p}-body)"/>
      <!-- body -->
      <path d="M100 26c44 0 72 30 72 82 0 56-30 98-72 98s-72-42-72-98c0-52 28-82 72-82z" fill="url(#{$p}-body)"/>
      <path d="M58 50c10-12 26-18 42-18" stroke="#5eead4" stroke-width="4" stroke-linecap="round" fill="none" opacity=".35"/>
      <!-- belly with feather scallops -->
      <path d="M100 112c30 0 46 22 46 50 0 26-20 42-46 42s-46-16-46-42c0-28 16-50 46-50z" fill="url(#{$p}-belly)"/>
      <g fill="none" stroke="#e2c48f" stroke-width="2.2" stroke-linecap="round" opacity=".8">
        <path d="M80 150q5 5 10 0M95 150q5 5 10 0M110 150q5 5 10 0"/>
        <path d="M72 166q5 5 10 0M87 166q5 5 10 0M102 166q5 5 10 0M117 166q5 5 10 0"/>
        <path d="M80 182q5 5 10 0M95 182q5 5 10 0M110 182q5 5 10 0"/>
      </g>

      <!-- book (held in front) -->
      <g class="owl-book">
        <path d="M112 168l30-6 4 30-30 6z" fill="#b45309"/>
        <path d="M110 166l30-6 4 30-30 6z" fill="url(#{$p}-book)"/>
        <path d="M114 170l20-4M115 176l20-4M116 182l14-3" stroke="#b45309" stroke-width="1.6" stroke-linecap="round" opacity=".6"/>
        <path d="M110 166l4 30" stroke="#92400e" stroke-width="2.4" stroke-linecap="round"/>
      </g>

      <!-- wings -->
      <g class="owl-wing owl-wing--l">
        <path d="M36 112c-14 14-18 40-10 62 4 10 12 14 20 10 4-22 6-46 2-70-4-4-8-5-12-2z" fill="url(#{$p}-wing)"/>
        <path d="M32 140c4 10 6 22 6 34M40 130c3 12 4 26 2 40" stroke="#5eead4" stroke-width="1.8" stroke-linecap="round" fill="none" opacity=".35"/>
      </g>
      <g class="owl-wing owl-wing--r">
        <path d="M164 112c14 14 18 40 10 62-4 10-12 14-20 10-4-22-6-46-2-70 4-4 8-5 12-2z" fill="url(#{$p}-wing)"/>
        <path d="M168 140c-4 10-6 22-6 34M160 130c-3 12-4 26-2 40" stroke="#5eead4" stroke-width="1.8" stroke-linecap="round" fill="none" opacity=".35"/>
      </g>

      <!-- scarf -->
      <g class="owl-scarf">
        <path d="M54 112c14 10 30 14 46 14s32-4 46-14l4 12c-14 12-32 18-50 18s-36-6-50-18z" fill="url(#{$p}-scarf)"/>
        <path d="M54 112c14 10 30 14 46 14s32-4 46-14" stroke="#99f6e4" stroke-width="2" fill="none" opacity=".6"/>
        <g class="owl-scarf-tail">
          <path d="M70 128c-4 14-2 30 4 42l14-4c-4-12-4-24-2-34z" fill="url(#{$p}-scarf)"/>
          <path d="M74 166l14-4M73 160l14-4" stroke="#0f766e" stroke-width="1.6" opacity=".5"/>
        </g>
      </g>

      <!-- head features -->
      <g class="owl-head">
        <path d="M100 58c-14-12-46-12-56 10-8 20 6 44 30 44 12 0 20-6 26-12 6 6 14 12 26 12 24 0 38-24 30-44-10-22-42-22-56-10z" fill="url(#{$p}-face)"/>
        <ellipse cx="60" cy="104" rx="9" ry="5" fill="#fb7185" opacity=".28"/>
        <ellipse cx="140" cy="104" rx="9" ry="5" fill="#fb7185" opacity=".28"/>

        <g class="owl-eyes">
          <circle cx="76" cy="82" r="17" fill="#fff"/>
          <circle cx="124" cy="82" r="17" fill="#fff"/>
          <g class="owl-pupils">
            <circle cx="78" cy="83" r="11" fill="url(#{$p}-iris)"/>
            <circle cx="122" cy="83" r="11" fill="url(#{$p}-iris)"/>
            <circle cx="78" cy="83" r="5.5" fill="#1c1208"/>
            <circle cx="122" cy="83" r="5.5" fill="#1c1208"/>
            <circle cx="82" cy="78" r="3.4" fill="#fff"/>
            <circle cx="126" cy="78" r="3.4" fill="#fff"/>
            <circle cx="75" cy="87" r="1.6" fill="#fff" opacity=".8"/>
            <circle cx="119" cy="87" r="1.6" fill="#fff" opacity=".8"/>
          </g>
          <g class="owl-lids" fill="#f6e5c6">
            <ellipse class="owl-lid" cx="76" cy="82" rx="18" ry="18"/>
            <ellipse class="owl-lid" cx="124" cy="82" rx="18" ry="18"/>
          </g>
          <g class="owl-closed" fill="none" stroke="#3b2310" stroke-width="4" stroke-linecap="round">
            <path d="M64 82q12 10 24 0"/>
            <path d="M112 82q12 10 24 0"/>
          </g>
          <g class="owl-happy" fill="none" stroke="#3b2310" stroke-width="4.5" stroke-linecap="round">
            <path d="M64 86q12-14 24 0"/>
            <path d="M112 86q12-14 24 0"/>
          </g>
        </g>

        <g class="owl-glasses" fill="none" stroke="#111827" stroke-width="4.2">
          <circle cx="76" cy="82" r="20"/>
          <circle cx="124" cy="82" r="20"/>
          <path d="M96 80q4-4 8 0" stroke-linecap="round"/>
          <path d="M56 78l-8-4M144 78l8-4" stroke-linecap="round"/>
        </g>
        <path d="M63 70q6-5 13-4M137 70q-6-5-13-4" stroke="#fff" stroke-width="2.4" stroke-linecap="round" fill="none" opacity=".55"/>

        <g class="owl-beak">
          <path class="owl-beak-low" d="M93 104q7 10 14 0z" fill="#d97706"/>
          <path d="M100 92c6 0 10 4 10 8 0 4-6 8-10 12-4-4-10-8-10-12 0-4 4-8 10-8z" fill="url(#{$p}-beak)"/>
          <path d="M96 97q4-2 8 0" stroke="#fff7cc" stroke-width="1.6" stroke-linecap="round" fill="none"/>
        </g>
      </g>
    </g>
  </g>

  <g class="owl-fx owl-fx--think" fill="#94a3b8"><circle cx="160" cy="34" r="4"/><circle cx="172" cy="22" r="5.5"/><circle cx="186" cy="8" r="7"/></g>
  <text class="owl-fx owl-fx--q" x="160" y="44" font-family="Fredoka, system-ui, sans-serif" font-weight="700" font-size="34" fill="#f59e0b">?</text>
  <g class="owl-fx owl-fx--zzz" font-family="Fredoka, system-ui, sans-serif" font-weight="700" fill="#38bdf8">
    <text class="z1" x="150" y="48" font-size="16">z</text><text class="z2" x="162" y="34" font-size="21">z</text><text class="z3" x="176" y="18" font-size="26">Z</text>
  </g>
  <g class="owl-fx owl-fx--spark" fill="#fbbf24">
    <path d="M26 40l3 8 8 3-8 3-3 8-3-8-8-3 8-3z"/><path d="M176 56l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/>
  </g>
</svg>
SVG;
}
