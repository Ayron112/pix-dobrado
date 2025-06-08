<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to make a POST request using curl
function makeRequest($endpoint, $data) {
    $ch = curl_init("http://localhost:8000/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Start PHP server in the background
echo "Starting PHP server...\n";
exec("php -S localhost:8000 > /dev/null 2>&1 & echo $!");
sleep(2); // Give the server time to start

echo "\nTesting Registration:\n";
echo "--------------------\n";

// Test registration with new user
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
print_r($result);

echo "\nTesting Login:\n";
echo "-------------\n";

// Test login with registered user
$loginData = [
    'email' => 'john@example.com',
    'password' => 'test123'
];

$result = makeRequest('login.php', $loginData);
print_r($result);

echo "\nTesting Invalid Login:\n";
echo "--------------------\n";

// Test login with wrong password
$invalidLoginData = [
    'email' => 'john@example.com',
    'password' => 'wrongpassword'
];

$result = makeRequest('login.php', $invalidLoginData);
print_r($result);

// Kill the PHP server
echo "\nStopping PHP server...\n";
exec("pkill -f 'php -S localhost:8000'");
?>
