<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_URL=" . config('app.url') . "\n";
echo "SESSION_DRIVER=" . config('session.driver') . "\n";
echo "SESSION_DOMAIN=" . (config('session.domain') ?? 'null') . "\n";
echo "SESSION_SECURE_COOKIE=" . (config('session.secure_cookie') ? 'true' : 'false') . "\n";
echo "SESSION_SAME_SITE=" . (config('session.same_site') ?? 'null') . "\n";
echo "SESSION_PATH=" . config('session.path') . "\n";

echo "---\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=fruitshop_new', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
    $exists = (bool) $stmt->fetchColumn();
    echo "sessions_table=" . ($exists ? 'yes' : 'no') . "\n";
    if ($exists) {
        $count = $pdo->query('SELECT COUNT(*) FROM sessions')->fetchColumn();
        echo "sessions_count=" . $count . "\n";
    }
} catch (Exception $e) {
    echo "db_error=" . $e->getMessage() . "\n";
}

echo "---\n";

$user = App\Models\User::where('email', 'clarencejohn@02@gmail.com')->first();
if ($user) {
    echo "user_exists=yes\n";
    echo "user_email=" . $user->email . "\n";
    echo "user_role=" . $user->role . "\n";
    echo "passmatch=" . (Illuminate\Support\Facades\Hash::check('password123', $user->password) ? 'yes' : 'no') . "\n";
} else {
    echo "user_exists=no\n";
}

echo "---\n";

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: PHP\r\n",
        'timeout' => 10,
    ],
];
$context = stream_context_create($opts);
$page = @file_get_contents('http://127.0.0.1:8000/login', false, $context);
echo "login_get_status=" . ($http_response_header[0] ?? 'none') . "\n";
