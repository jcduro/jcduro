<?php

// =============== CONFIG BÁSICA ===============

$username = 'jcduro'; // tu usuario GitHub
$today    = date('Y-m-d');

// =============== FUNCIÓN PARA LLAMAR A LA API ===============

function fetchGithubJson(string $url): array {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: jcduro-stats-svg',
            'Accept: application/vnd.github+json',
        ],
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : [];
}

// =============== 1. OBTENER TUS REPOS PÚBLICOS ===============

$repos = fetchGithubJson("https://api.github.com/users/{$username}/repos?per_page=100");

$totalRepos   = count($repos);
$totalStars   = 0;
$languagesMap = [];

foreach ($repos as $repo) {
    $totalStars += (int)($repo['stargazers_count'] ?? 0);

    $lang = $repo['language'] ?? 'Other';
    if (!$lang) {
        $lang = 'Other';
    }

    if (!isset($languagesMap[$lang])) {
        $languagesMap[$lang] = 0;
    }
    $languagesMap[$lang] += 1;
}

// Ordenar lenguajes por cantidad de repos y tomar top 5
arsort($languagesMap);
$topLanguages = array_slice($languagesMap, 0, 5, true);

// Convertir a “porcentaje” para las barras (0–100)
$maxReposLang = $topLanguages ? max($topLanguages) : 1;

$skills = [];
foreach ($topLanguages as $lang => $count) {
    $skills[$lang] = (int) round($count * 100 / $maxReposLang);
}

// =============== 2. VALORES PARA MOSTRAR EN LA TARJETA ===============

$projects  = $totalRepos;
$mainStack = $topLanguages ? implode(' · ', array_keys($topLanguages)) : 'No repos';
$commits   = '—'; // Lo dejamos como "—" por ahora.

// =============== 3. ARMAR EL SVG BONITO ===============

$width         = 600;
$height        = 220;
$paddingLeft   = 32;
$paddingRight  = 32;
$paddingTop    = 70;
$paddingBottom = 40;
$chartHeight   = $height - $paddingTop - $paddingBottom;

$barCount = max(1, count($skills));
$barGap   = 16;
$barWidth = (($width - $paddingLeft - $paddingRight) - ($barGap * ($barCount - 1))) / $barCount;

$barsSvg = '';
$index   = 0;

foreach ($skills as $label => $value) {
    $barHeight = max(0, min(100, $value)) * $chartHeight / 100;

    $x = $paddingLeft + $index * ($barWidth + $barGap);
    $y = $paddingTop + ($chartHeight - $barHeight);

    $labelSafe = htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $barsSvg .= '
      <g>
        <rect x="' . $x . '" y="' . $y . '" width="' . $barWidth . '" height="' . $barHeight . '"
              rx="6" fill="url(#barGradient)" />
        <text x="' . ($x + $barWidth / 2) . '" y="' . ($y - 6) . '"
              fill="#E5E7EB" font-size="11" text-anchor="middle" font-family="monospace">
          ' . $value . '
        </text>
        <text x="' . ($x + $barWidth / 2) . '" y="' . ($paddingTop + $chartHeight + 14) . '"
              fill="#9CA3AF" font-size="11" text-anchor="middle" font-family="monospace">
          ' . $labelSafe . '
        </text>
      </g>
    ';

    $index++;
}

$svg  = '';
$svg .= '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">' . "\n";
$svg .= '  <defs>' . "\n";
$svg .= '    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">' . "\n";
$svg .= '      <stop offset="0%" stop-color="#020617"/>' . "\n";
$svg .= '      <stop offset="100%" stop-color="#020617"/>' . "\n";
$svg .= '    </linearGradient>' . "\n";
$svg .= '    <linearGradient id="borderGradient" x1="0%" y1="0%" x2="100%" y2="0%">' . "\n";
$svg .= '      <stop offset="0%" stop-color="#
