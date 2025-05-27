<?php

require_once './Backend/Controller/Controller.php';

$productId = (int) $_GET['id'];
$controller = new Controller();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
  $controller->updateProduct($_POST);
  echo json_encode([
    'status' => 'success',
    'message' => 'Product updated successfully!'
  ]);
  exit;
}

// get the product with the specified id
if (isset($_GET['id'])) {
  $product = $controller->showProductById($_GET['id']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <title>Edit Product</title>

  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />

  <style>
    body {
      background-color: var(--color2);
    }

    .form-section,
    .product-card {
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .form-label {
      font-weight: 500;
    }

    .btn-rounded {
      border-radius: 50px;
    }
  </style>

</head>


<body>

  <div class="container py-5">
    <h2 class="mb-4 text-center">✏️ Admin - Edit Product</h2>

    <div class="form-section mb-5">
      <h4>Edit Product Details</h4>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form id="editProductForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="ajax" value="1">
        <input type="hidden" name="Id" value="<?= $product->getId() ?>">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="Name" class="form-control" value="<?= htmlspecialchars($product->getName()) ?>" required>
          </div>

     
          <div class="col-md-3 mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" name="Price" step="0.01" class="form-control" value="<?= $product->getPrice() ?>" required>
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="Stock" class="form-control" value="<?= $product->getStock() ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Origin</label>
            <input type="text" name="Origin" class="form-control" value="<?= htmlspecialchars($product->getOrigin()) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Barcode</label>
            <input type="text" name="Barcode" class="form-control" value="<?= htmlspecialchars($product->getBarcode()) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Weight (kg)</label>
            <input type="number" step="0.01" name="Weight" class="form-control" value="<?= $product->getWeight() ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="Image" class="form-control">
            <?php if ($product->getImageUrl()): ?>
              <small class="text-muted">Current: <?= basename($product->getImageUrl()) ?></small>
            <?php endif; ?>
          </div>

          <div class="col-12 mb-3">
            <label class="form-label">Description</label>
            <textarea name="Description" class="form-control" rows="3"><?= htmlspecialchars($product->getDescription()) ?></textarea>
          </div>

          <div class="col-12 text-end">
            <button type="submit" class="submit btn-primary btn-rounded px-4">Update Product</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      fetch('', { // same page
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            alert(data.message);

          } else {
            alert("Update failed.");
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error occurred while updating.');
        });
    });
  </script>

</body>

</html>