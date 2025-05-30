<?php
require_once './Backend/Controller/Controller.php';
$controller = new Controller();
$products = $controller->getProducts(); // Get all products

$controller->checkAdminLogin();

include "./Backend/Controller/HeaderVersion2_Logic.php";
$links = ["Profile" => "./Admin.php"];
$activeLink = "";
$showButton = true;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $isAjax = isset($_POST['ajax']) && $_POST['ajax'] == 1;
  $action = $_POST['action'] ?? null;

  if ($isAjax && $action) {
    if ($action === 'add') {
      $result = $controller->addProduct($_POST, $_FILES);
      echo json_encode(['status' => $result ? 'success' : 'fail']);
      exit;
    } elseif ($action === 'delete') {
      $result = $controller->deleteProduct(['Id' => $_POST['id']]);
      echo json_encode(['status' => $result ? 'success' : 'fail']);
      exit;
    }
  }


  $controller->addProduct($_POST, $_FILES);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
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

    img {
      min-height: 2rem;
      max-height: 2rem;
      min-width: 2rem;
      max-width: 2rem;
    }
  </style>
</head>

<body>

  <?php include './HeaderVersion2_Nav.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4 text-center"><img class="img" src="./Icons/settings.svg" alt=""> Manage Products</h2>

    <!-- Add Product Form -->
    <div class="form-section mb-5">
      <h4>Add New Product</h4>
      <form id="addProductForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="ajax" value="1">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="Name" class="form-control" required>
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" name="Price" step="0.01" class="form-control" required>
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="Stock" class="form-control" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Origin</label>
            <input type="text" name="Origin" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Barcode</label>
            <input type="text" name="Barcode" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Weight (kg)</label>
            <input type="number" step="0.01" name="Weight" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="Image" class="form-control">
          </div>


          <div class="col-12 mb-3">
            <label class="form-label">Description</label>
            <textarea name="Description" class="form-control" rows="3"></textarea>
          </div>

          <div class="col-12 text-end">
            <button type="submit" class="submit btn-primary btn-rounded px-4">Add Product</button>
          </div>

        </div>
      </form>
    </div>

    <!-- Existing Products -->
    <h4 class="mb-3">Product List</h4>
    <div class="row">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
          <div class="product-card p-3">
            <h5><?= htmlspecialchars($product['Name']) ?></h5>
            <p class="mb-1">Price: $<?= htmlspecialchars($product['Price']) ?></p>
            <p class="mb-1">Stock: <?= $product['Stock'] ?></p>
            <p class="mb-1">Barcode: <?= $product['Barcode'] ?></p>
            <div class="d-flex justify-content-between mt-3">
              <a href="EditProduct.php?id=<?= $product['Id'] ?>" class="btn btn-sm btn-outline-success btn-rounded">Edit</a>
              <form method="POST" class="delete-form" data-id="<?= $product['Id'] ?>">
                <button class="btn btn-sm btn-outline-danger btn-rounded">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>


  <script>
    // Add Product AJAX
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      formData.append('action', 'add'); // action for add product

      fetch('', { // Same page
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            alert('Product added successfully!');
            form.reset();
            location.reload();
          } else {
            alert('Failed to add product.');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error occurred while adding product.');
        });
    });


    // Delete Product AJAX

    document.querySelectorAll('.delete-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this product?')) return;

        const productId = this.dataset.id;
        const formData = new FormData();

        formData.append('ajax', 1);
        formData.append('action', 'delete');
        formData.append('id', productId);


        fetch('', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              alert('Product deleted!');
              this.closest('.col-md-4').remove(); // Remove the product from the DOM
            } else {
              alert('Failed to delete product.');
            }
          })
          .catch(err => {
            console.error(err);
            alert('Error deleting product.');
          });
      });
    });
  </script>


</body>

</html>