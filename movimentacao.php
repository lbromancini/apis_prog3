<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up your database connection
$servername = "localhost:3306";
$username = "root";
$password = "aluno123";
$database = "controle_robo";

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection
$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Retrieve and decode the request parameter
$stringParam = file_get_contents('php://input');
$jsonParamRequest = json_decode($stringParam, true);

// Determine if the parameter is a JSON array or object
if ($stringParam[0] == '[') {
    $jsonParam = $jsonParamRequest[0];
} else {
    $jsonParam = $jsonParamRequest;
}

$json = array();

if (!empty($jsonParam)) {
    // Prepare the WHERE clause
    $whereClause = ' WHERE ';
    foreach ($jsonParam as $field => $value) {
        if ($value != '' && $value != '0') {
            $whereClause .= "$field = '$value' AND ";
        }
    }
    $whereClause = rtrim($whereClause, ' AND ');

    // Prepare the SQL statement for selecting data from the 'pontos' table
    $consulta = "SELECT nomePonto, angulo_junta1, angulo_junta2, angulo_junta3 FROM pontos $whereClause";

    // Set the content type to JSON
    header('Content-Type: application/json');

    $result = $con->query($consulta);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $json[] = $row;
            }
        } 
    }
} 
$result->free_result();
$con->close();

// Send the JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($json);

?>
