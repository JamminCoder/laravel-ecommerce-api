<?php

$DATABASE = "";
$DB_USERNAME = "";
$DB_PASSWORD = "";


$name = readline('>> Enter username: ');
$email = readline('>> Enter email: ');
$password = readline('>> Enter password: ');

$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['rounds' => 10]);


echo "
A new user with these credentials is about to be created:
Username: $name,
Email: $email,
Password: $password
Hashed password: $hashedPassword
";

$continue = readline('>> Confirm the creation of this user? [y/n] ');

if ($continue !== 'y' && $continue !== 'Y') {
    echo 'Aborting user creation.' . PHP_EOL;
    exit();
}

$db = new PDO("mysql:host=localhost;dbname=$DATABASE", $DB_USERNAME, $DB_PASSWORD);

$stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password);");
$stmt->execute([
    'name' => $name,
    'email' => $email,
    'password' => $hashedPassword
]);
