<?php
$html = file_get_contents('storage/rendered_exact_full.html');
preg_match('/<script\b[^>]*>(.*?)<\/script>\s*<\/body>/is', $html, $m);
if (isset($m[1])) {
    $lines = explode("\n", $m[1]);
    echo "Script 7 total lines: " . count($lines) . "\n";
} else {
    echo "Script 7 not matched\n";
}

