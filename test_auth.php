<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to make a POST request to our endpoints
function makePostRequest($url, $data) {
    $_POST = $data;
    ob_start();
    include $url;
    $response = ob_get_clean();
    return json_decode($response, true);
}

echo "Testing Registration:\n";
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

$result = makePostRequest('register.php', $registerData);
print_r($result);

echo "\nTesting Login:\n";
echo "-------------\n";

// Test login with registered user
$loginData = [
    'email' => 'john@example.com',
    'password' => 'test123'
];

$result = makePostRequest('login.php', $loginData);
print_r($result);

echo "\nTesting Invalid Login:\n";
echo "--------------------\n";

// Test login with wrong password
$invalidLoginData = [
    'email' => 'john@example.com',
    'password' => 'wrongpassword'
];

$result = makePostRequest('login.php', $invalidLoginData);
print_r($result);

?>
