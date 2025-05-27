
<?php

require_once './Backend/Controller/Controller.php';


// Validate ID parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo "Invalid product ID.";
    exit;
}

$productId = (int) $_GET['id'];
$controller = new Controller();
$product = $controller->showProductById($productId);

if (!$product) {
    http_response_code(404);
    echo "Product not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8" />
  <title>Product Details</title>
  <link rel="stylesheet" href="./Style/elements.css" />
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  
</head>
<body style="background-color: var(--color2);">

<!-- Navbar -->


<div class="container my-5">
  <div class="row">
    <!-- Product Image -->
    <div class="col-md-6">
      <img src="<?= 'http://localhost/Senior-Project/' . htmlspecialchars($product->getImageUrl()) ?>" 
           alt="Product Image" class="img-fluid rounded shadow">
    </div>

    <!-- Product Info -->
    <div class="col-md-6">
      <h2><?= htmlspecialchars($product->getName()) ?></h2>
      <h4 class="text-success">$<?= htmlspecialchars($product->getPrice()) ?></h4>
      <!-- <p><strong>Stock:</strong> <?= htmlspecialchars($product->getStock()) ?></p> -->
      <p><strong>Weight:</strong> <?= htmlspecialchars($product->getWeight()) ?> kg</p>
      <p><strong>Origin:</strong> <?= htmlspecialchars($product->getOrigin()) ?></p>
      <!-- <p><strong>Barcode:</strong> <?= htmlspecialchars($product->getBarcode()) ?></p> -->
      <p><?= htmlspecialchars($product->getDescription()) ?></p>

      <!-- Add to Cart Form -->
      <form id="addToCartForm">
        <input type="hidden" name="productId" value="<?= $product->getId() ?>">
        <input type="hidden" name="productName" value="<?= htmlspecialchars($product->getName()) ?>">
        <input type="hidden" name="productPrice" value="<?= htmlspecialchars($product->getPrice()) ?>">
        <input type="hidden" name="productImage" value="<?= 'http://localhost/Senior-Project/' . htmlspecialchars($product->getImageUrl()) ?>">
        <input type="hidden" name="productDescription" value="<?= htmlspecialchars($product->getDescription()) ?>">

        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" class="form-control w-25" id="quantity" name="quantity" value="1" min="1">
        </div>
        <button type="submit" class="submit btn-primary rounded-pill px-4 shadow-sm w-100">Add to Cart</button>
      </form>
    </div>
  </div>
</div>

<script>

  // add to cart from ProductDetails Page

  document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop form from reloading the page

    const formData = new FormData(this);

    fetch('index.php?action=addToCart', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === "success"){
        alert(data.message);

      }
      else{
        alert("Failed");
      }
    });
  });
</script>

<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>