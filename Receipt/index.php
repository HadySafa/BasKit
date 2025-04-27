<?php
require 'vendor/autoload.php'; // Load Composer's autoloader
require_once('../Backend/Controller/Controller.php');

use Dompdf\Dompdf;
use Dompdf\Options;

$controller = new Controller();

// Check if user requesting the page is logged in
$controller->checkLoggedIn();

// Get the id requested (by customer or admin)
$submittedId = '';
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['Id'])) {
    $submittedId = $_GET['Id'];
}

// If Id is not numeric or Id is a negative number
if (!is_numeric($submittedId) || $submittedId < 0) {
    // rediret to another page
    exit();
}

$order = '';
$products = '';
// Id is valid
if ($controller->isCustomer()) {
    // customer
    $order = $controller->getOrderByIdUserId($submittedId);
    $products = $controller->getOrdersProducts($submittedId);
}

// display the message returned from the controller if order not found or an error occured
if (!is_array($order) || !is_array($products)) {
    exit();
}


function formatDate($date)
{
    $date = new DateTime($date);
    return $date->format('l, F j, Y \a\t g:i A');
}

function displayProducts($products)
{
    $tableBody = '';
    foreach ($products as $product) {

        $name = $product["PRODUCTNAME"];
        $quantity = $product["Quantity"];
        $price = $product["SellingPrice"];

        $tableBody =  $tableBody . "
            <tr>
                <td>$name</td>
                <td>$quantity</td>
                <td>$$price</td>
            </tr>
            ";
    }
    return $tableBody;
}

function calculateTotal($products)
{
    $total = 0;
    foreach ($products as $product) {
        $total += $product['SellingPrice'];
    }
    return $total;
}

// Create HTML content for the receipt
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .header { text-align: center; margin-bottom: 30px; }
        h1 { color: #333; }
        .receipt-details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; }
        .footer { margin-top: 50px; text-align: center; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RECEIPT</h1>
        <p>BasKit</p>
    </div>
    
    <div class="receipt-details">
        <p><strong>Order </strong>#' . $order['Id'] . '</p>
        <p><strong>Order Date</strong> ' . formatDate($order['Timestamp']) . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>' .
    displayProducts($products)
    . '</tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2">Total</td>
                <td>$' . calculateTotal($products) . '</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Thank you for reaching us!</p>
    </div>
</body>
</html>
';

// Configure DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true); // Allow external images/styles
$options->set('defaultFont', 'Arial'); // Set default font

// Initialize DomPDF
$dompdf = new Dompdf($options);

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A5', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF
$dompdf->stream("receipt.pdf", [
    "Attachment" => false
]);
