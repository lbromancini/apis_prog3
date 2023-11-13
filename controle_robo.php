<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up your database connection
$servername = "localhost:3306";
$username = "root";
$password = "aluno123";
$database = "controle_robo";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Retrieve the JSON parameter from the POST request
$inputFile = file_get_contents('php://input');
if ($data = json_decode($inputFile, true)) {

    if (json_last_error() === JSON_ERROR_NONE) {
        // Validate and sanitize data here (e.g., using filter_var)

        // Insert data into the 'pontos' table using prepared statements
        $stmt = $conn->prepare("INSERT INTO pontos (nomePonto, angulo_junta1, angulo_junta2, angulo_junta3) VALUES (:nomePonto, :angulo_junta1, :angulo_junta2, :angulo_junta3)");
        $stmt->bindParam(':nomePonto', $data['nomePonto']);
        $stmt->bindParam(':angulo_junta1', $data['angulo_junta1'], PDO::PARAM_INT);
        $stmt->bindParam(':angulo_junta2', $data['angulo_junta2'], PDO::PARAM_INT);
        $stmt->bindParam(':angulo_junta3', $data['angulo_junta3'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response = [
                "success" => true,
                "message" => "Data saved successfully."
            ];
        } else {
            $response = [
                "success" => false,
                "message" => "Failed to save data."
            ];
        }
    } else {
        $response = [
            "success" => false,
            "message" => "Invalid JSON format."
        ];
    }
} else {
    $response = [
        "success" => false,
        "message" => "Input JSON file not found."
    ];
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

// Close the database connection
$conn = null;
?>
