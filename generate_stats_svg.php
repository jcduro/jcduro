<?php

$username = 'jcduro';

function getGitHubData($user) {
    $ch = curl_init("https://api.github.com/users/$user/repos?per_page=100");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'stats');
    $data = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $data ?: [];
}

$repos = getGitHubData($username);
$langs = [];
foreach ($repos as $r) {
    $l = $r['language'] ?? 'Other';
    $langs[$l] = ($langs[$l] ?? 0) + 1;
}
arsort($langs);
$top = array_slice($langs, 0, 5, true);
$max = max($top) ?: 1;

// SVG
$svg = '<svg width="600" height="320" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="bg"><stop offset="0%" stop-color="#0f172a"/></linearGradient><linearGradient id="bar" x1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#0ea5e9"/><stop offset="100%" stop-color="#06b6d4"/></linearGradient></defs>';
$svg .= '<rect width="600" height="320" fill="url(#bg)"/>';
$svg .= '<rect x="0" y="0" width="600" height="320" fill="none" stroke="#0ea5e9" stroke-width="2" rx="12"/>';
$svg .= '<text x="300" y="35" fill="#0ea5e9" font-size="24" font-weight="bold" text-anchor="middle">jcduro Developer Stats</text>';
$svg .= '<text x="50" y="65" fill="#E5E7EB" font-size="12">Projects: ' . count($repos) . ' | Languages: ' . implode(', ', array_keys($top)) . '</text>';
$svg .= '<line x1="40" y1="80" x2="560" y2="80" stroke="#1e293b" stroke-width="1"/>';

$pad = 40;
$cH = 170;
$bars = count($top);
$bW = ($600 - $pad * 2 - (($bars - 1) * 16)) / $bars;

$idx = 0;
foreach ($top as $lang => $count) {
    $pct = round(($count / $max) * 100);
    $bH = ($pct / 100) * $cH;
    $x = $pad + $idx * ($bW + 16);
    $y = 100 + ($cH - $bH);
    
    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $bW . '" height="' . $bH . '" rx="6" fill="url(#bar)"/>';
    $svg .= '<text x="' . ($x + $bW/2) . '" y="' . ($y - 6) . '" fill="#E5E7EB" font-size="12" text-anchor="middle">' . $pct . '%</text>';
    $svg .= '<text x="' . ($x + $bW/2) . '" y="' . (100 + $cH + 18) . '" fill="#9CA3AF" font-size="11" text-anchor="middle">' . $lang . '</text>';
    
    $idx++;
}

$svg .= '</svg>';

@mkdir(__DIR__ . '/assets', 0755, true);
file_put_contents(__DIR__ . '/assets/stats-jcduro.svg', $svg);
echo "Done\n";
?>
