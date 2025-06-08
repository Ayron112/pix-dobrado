<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize or reset the database
include 'setup_database.php';

// Start the PHP server
echo "Starting PHP server on http://localhost:8000\n";
echo "Use Ctrl+C to stop the server\n";

// Change to the project directory and start the server
chdir(__DIR__);
$command = "php -S localhost:8000";
passthru($command);
?>
