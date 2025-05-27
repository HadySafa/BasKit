<?php

// Set the content type to JSON
header('Content-Type: application/json');

// Create instance of a controller
require_once __DIR__ . "\../Backend/Controller/Controller.php";
$controller = new Controller();

// Read the raw POST data
$json = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($json, true);

// Check if decoding was successful
if ($data === null) {

    http_response_code(401);
    echo json_encode([
        'message' => 'Invalid JSON received.',
    ]);
    exit;

}

// Access the values
$barcode = $data['Barcode'] ?? null;
$basketId = $data['BasketId'] ?? null;

if ($barcode && $basketId) { // values are ready here

    // add product to basket

    $responseCode = -1;
    $msg = "";
    
    $response = $controller->addProductToBasket($barcode, $basketId) ? "added" : "not added";

    http_response_code(200);
    echo json_encode([
        'message' => $response,
    ]);


} else {

    http_response_code(403);
    echo json_encode([
        'message' => 'Barcode not provided.',
    ]);

}


