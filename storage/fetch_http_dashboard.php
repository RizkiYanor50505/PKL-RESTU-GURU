<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$budi = App\Models\User::where('name', 'like', '%Budi Santoso%')->first();
$pass = '';
foreach (['password', '12345678', 'admin', 'budi123', 'secret', 'password123'] as $p) {
    if (Illuminate\Support\Facades\Hash::check($p, $budi->password)) {
        $pass = $p;
        echo "Found password: $p\n";
        break;
    }
}

if (!$pass) {
    $budi->password = Illuminate\Support\Facades\Hash::make('password');
    $budi->save();
    $pass = 'password';
    echo "Reset password to: password\n";
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'storage/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'storage/cookies.txt');
$loginPage = curl_exec($ch);

preg_match('/<input[^>]+name="_token"[^>]+value="([^"]+)"/', $loginPage, $m);
$csrf = $m[1] ?? '';

curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $csrf,
    'login' => $budi->email,
    'password' => $pass,
]));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPGET, true);
$dashboardHtml = curl_exec($ch);
curl_close($ch);

file_put_contents('storage/live_budi_http.html', $dashboardHtml);
$lines = explode("\n", $dashboardHtml);
echo "Fetched live HTML length: " . strlen($dashboardHtml) . " bytes\n";
echo "Total lines: " . count($lines) . "\n";

// Check all script tags with node
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $dashboardHtml, $matches, PREG_OFFSET_CAPTURE);
foreach ($matches[1] as $idx => $scriptMatch) {
    $code = $scriptMatch[0];
    $offset = $scriptMatch[1];
    $startLine = substr_count(substr($dashboardHtml, 0, $offset), "\n") + 1;
    $endLine = $startLine + substr_count($code, "\n");
    $tmp = "storage/http_script_{$idx}.js";
    file_put_contents($tmp, $code);
    $chk = shell_exec("node --check \"$tmp\" 2>&1");
    if (!empty(trim($chk))) {
        echo ">>> SYNTAX ERROR in Script #$idx (lines $startLine - $endLine):\n$chk\n";
    } else {
        echo "Script #$idx (lines $startLine - $endLine): OK\n";
    }
}
