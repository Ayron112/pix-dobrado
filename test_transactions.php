<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start PHP server
echo "Starting PHP server...\n";
exec("php -S localhost:8000 > /dev/null 2>&1 & echo $!");
sleep(2); // Give the server time to start

// Function to make a POST request using curl
function makeRequest($endpoint, $data, $cookies = '') {
    $ch = curl_init("http://localhost:8000/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if ($cookies) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    }
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    
    // Extract headers and body
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    // Get session cookie if present
    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
    $sessionCookie = isset($matches[1]) ? $matches[1] : '';
    
    curl_close($ch);
    return [
        'body' => json_decode($body, true),
        'cookie' => $sessionCookie
    ];
}

// Reset database
echo "Resetting database...\n";
include 'setup_database.php';

echo "\nRegistering test user...\n";
$registerData = [
    'name' => 'John Doe',
    'cep' => '12345-678',
    'cpf' => '123.456.789-01',
    'address' => '123 Test Street',
    'birthdate' => '1990-01-01',
    'cell' => '(11) 98765-4321',
    'email' => 'john@example.com',
    'password' => 'test123',
    'defaultPixKey' => 'john@pix.com'
];

$result = makeRequest('register.php', $registerData);
print_r($result['body']);

echo "\nLogging in...\n";
$loginData = [
    'email' => 'john@example.com',
    'password' => 'test123'
];

$loginResult = makeRequest('login.php', $loginData);
$sessionCookie = $loginResult['cookie'];
print_r($loginResult['body']);

echo "\nTesting deposit...\n";
$depositData = [
    'amount' => '100.00',
    'method' => 'manual'
];

$result = makeRequest('deposit.php', $depositData, $sessionCookie);
print_r($result['body']);

echo "\nTesting withdrawal (should fail due to unconfirmed deposit)...\n";
$withdrawData = [
    'amount' => '50.00',
    'pixKey' => 'john@pix.com'
];

$result = makeRequest('withdraw.php', $withdrawData, $sessionCookie);
print_r($result['body']);

echo "\nConfirming deposit in database...\n";
try {
    $pdo = new PDO('sqlite:database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("UPDATE deposits SET status = 'Confirmado' WHERE id = 1");
    $stmt->execute();
    echo "Deposit confirmed.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTesting withdrawal again (should succeed)...\n";
$result = makeRequest('withdraw.php', $withdrawData, $sessionCookie);
print_r($result['body']);

// Kill the PHP server
echo "\nStopping PHP server...\n";
exec("pkill -f 'php -S localhost:8000'");
?>
