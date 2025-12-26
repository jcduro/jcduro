<?php

// Aquí pondrás tus números reales (por ahora manuales)
$commits   = 1234;
$projects  = 8;
$mainStack = 'PHP · JS · React · Python';
$today     = date('Y-m-d');

$svg = <<<SVG
<svg width="500" height="160" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#020617"/>
      <stop offset="100%" stop-color="#111827"/>
    </linearGradient>
  </defs>

  <rect width="500" height="160" fill="url(#bg)" rx="18" />

  <text x="24" y="40" fill="#04D9FF" font-size="22"
        font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif">
    jcduro · Developer Stats
  </text>

  <text x="24" y="75" fill="#E5E7EB" font-size="14" font-family="monospace">
    Commits: {$commits}   ·   Projects: {$projects}
  </text>

  <text x="24" y="100" fill="#E5E7EB" font-size="14" font-family="monospace">
    Stack: {$mainStack}
  </text>

  <text x="24" y="130" fill="#6B7280" font-size="11" font-family="monospace">
    Updated: {$today}
  </text>
</svg>
SVG;

if (!is_dir(__DIR__ . '/assets')) {
    mkdir(__DIR__ . '/assets', 0777, true);
}

file_put_contents(__DIR__ . '/assets/stats-jcduro.svg', $svg);
echo "SVG generado en assets/stats-jcduro.svg\n";
