<?php

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

$categories = $controller->getCategories();

$controller->checkCustomerLogin();


$products = isset($_GET['category']) || isset($_GET['price']) ? $controller->showFilterResults() : $controller->getProducts(); // Show filtered or all products


include "./Backend/Controller/HeaderVersion2_Logic.php";
$links = ["Home" => "./LandingPage.php", "Cart" => "./Cart.php", "Profile" => "./User.php", "Contact" => "./Feedback.php"];
$activeLink = "";
$showButton = true;

?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />


  <title>Products</title>

  <style>
    /* Bump animation for the cart icon */
    @keyframes bump {
      0% {
        transform: scale(1);
      }

      10% {
        transform: scale(1.2);
      }

      30% {
        transform: scale(0.9);
      }

      50% {
        transform: scale(1.1);
      }

      100% {
        transform: scale(1);
      }
    }

    /* When the .bump class is added, run the animation */
    .cart-icon.bump {
      animation: bump 0.5s ease-out;
    }

    @keyframes shrink {
      0% {
        transform: scale(1);
      }

      10% {
        transform: scale(0.8);
      }

      30% {
        transform: scale(1.1);
      }

      50% {
        transform: scale(0.9);
      }

      100% {
        transform: scale(1);
      }
    }

    .cart-icon.shrink {
      animation: shrink 0.5s ease-out;
    }

    #search-form {
      position: relative;
    }

    .suggestions-list {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      z-index: 999;
      background-color: #fff;
      border: 1px solid #ccc;
      border-top: none;
      max-height: 200px;
      overflow-y: auto;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .suggestions-list div {
      padding: 8px 12px;
      cursor: pointer;
    }

    .suggestions-list div:hover {
      background-color: #f0f0f0;
    }
  </style>

</head>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');

    document.getElementById('category-filter').addEventListener('change', function() {
      form.submit();
    });

    document.getElementById('price-filter').addEventListener('change', function() {
      form.submit();
    });
  });
</script>


<body style="background-color: var(--color2);">

  <!-- Navbar -->

  <?php include "./HeaderVersion2_Nav.php" ?>

  <div class="container my-4">
    <div class="row g-3">
      <!-- Search Form -->
      <div class="col-md-4">
        <form method="GET" id="search-form">
          <input type="search" class="form-control" placeholder="Search products..." aria-label="Search" id="search-input" autocomplete="off">
          <div id="suggestions" class="suggestions-list"></div>

        </form>
      </div>

      <!-- Filter Form -->
      <form action="Products.php" method="GET" id="filterForm" class="col-md-8 d-flex gap-3">

        <input type="hidden" name="query" value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>">

        <!-- Category Filter -->
        <select id="category-filter" name="category" class="form-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat['id']) ?>"
              <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>

          <?php endforeach; ?>
        </select>

        <!-- Price Filter -->
        <select id="price-filter" name="price" class="form-select" onchange="this.form.submit()">
          <option value="lowest" <?= (isset($_GET['price']) && $_GET['price'] == 'lowest') ? 'selected' : '' ?>>Lowest Price</option>
          <option value="20-50" <?= (isset($_GET['price']) && $_GET['price'] == '20-50') ? 'selected' : '' ?>>$20 - $50</option>
          <option value="highest" <?= (isset($_GET['price']) && $_GET['price'] == 'highest') ? 'selected' : '' ?>>Highest Price</option>

        </select>

        <a href="Products.php" class="btn btn-secondary btn-sm" style="padding: 0.5px 10px; font-size: 12px;" onclick="clearFilters()">Clear Filters</a>
      </form>
    </div>
  </div>


  <div class="container">
    <div class="row">
      <!-- Loop through the products and display them -->
      <?php foreach ($products as $product): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
          <div class="card h-100">

            <!-- Link around image -->
            <a href="productDetails.php?id=<?= htmlspecialchars($product['Id']) ?>">
              <img src="<?= $product['ImageUrl'] ?>"
                class="card-img-top"
                alt="Product Image"
                style="height: 300px; object-fit: cover;">
            </a>

            <div class="card-body d-flex flex-column">

              <!-- Link around product name -->
              <h5 class="card-title text-center" style="color: var(--color1);">
                <a href="productDetails.php?id=<?= htmlspecialchars($product['Id']) ?>" class="text-decoration-none text-dark">
                  <?= htmlspecialchars($product['Name']) ?>
                </a>
              </h5>

              <p class="text-success text-center fw-bold">$<?= htmlspecialchars($product['Price']) ?></p>

              <!-- Add to cart form -->
              <form class="add-to-cart-form" data-product-id="<?= htmlspecialchars($product['Id']) ?>" method="POST" action="index.php?action=addToCart">
                <input type="hidden" name="productId" value="<?= htmlspecialchars($product['Id']) ?>">
                <input type="hidden" name="productName" value="<?= htmlspecialchars($product['Name']) ?>">
                <input type="hidden" name="productPrice" value="<?= htmlspecialchars($product['Price']) ?>">
                <input type="hidden" name="productImage" value="<?= 'http://localhost/Senior-Project/' . htmlspecialchars($product['ImageUrl']) ?>">
                <input type="hidden" name="productDescription" value="<?= htmlspecialchars($product['Description']) ?>">

                <input type="number" name="quantity" value="1" min="1"
                  class="form-control mb-2 shadow-sm border-0 rounded-pill text-center quantity-input w-100" />

                <button type="submit" class="submit btn-primary rounded-pill px-4 shadow-sm w-100">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const quantityInputs = document.querySelectorAll(".quantity-input");

      quantityInputs.forEach(input => {
        // Prevent typing non-numeric characters
        input.addEventListener("input", (e) => {
          e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Validate value on focus out (blur or change)
        input.addEventListener("change", (e) => {
          let value = parseInt(e.target.value, 10);

          if (isNaN(value) || value < 1) {
            e.target.value = 1;
          }
        });

        // prevent typing 'e', '+', '-', etc.
        input.addEventListener("keydown", (e) => {
          if (["e", "+", "-", ".", ","].includes(e.key)) {
            e.preventDefault();
          }
        });
      });
    });



    // Add to Cart AJAX

    document.addEventListener('DOMContentLoaded', () => {
      const addToCartForms = document.querySelectorAll('.add-to-cart-form');

      addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault(); // Prevent default form submission

          const formData = new FormData(form);
          const xhr = new XMLHttpRequest();


          xhr.open('POST', form.action, true);

          xhr.onload = function() {

            console.log(xhr.responseText);
            if (xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              alert(response.message);

              if (response.status === 'error') {
                window.location.href = 'Login.php';
                return;
              }


              localStorage.setItem('cartUpdated', Date.now());
            }
          };



          xhr.send(formData);
        });
      });
    });


    // Search Bar 

    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('suggestions');

    searchInput.addEventListener('input', async () => {
      const query = searchInput.value.trim();
      if (!query) {
        suggestionsBox.innerHTML = '';
        return;
      }

      const response = await fetch(`index.php?action=getSearchSuggestions&query=${encodeURIComponent(query)}`);
      const responseText = await response.text();

      try {
        const suggestions = JSON.parse(responseText);
        suggestionsBox.innerHTML = '';
        suggestions.forEach(item => {
          const div = document.createElement('div');
          div.textContent = item.Name;
          div.addEventListener('click', () => {
            searchInput.value = item.Name;
            suggestionsBox.innerHTML = '';
            searchForm.submit();
          });
          suggestionsBox.appendChild(div);
        });
      } catch (err) {
        console.error("JSON Parse Error:", err.message);
      }

    });

    document.addEventListener('click', (e) => {
      if (!searchForm.contains(e.target)) {
        suggestionsBox.innerHTML = '';
      }
    });

    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query) {
        window.location.href = `Search.php?query=${encodeURIComponent(query)}`;
      }
    });




    // function for the cart icon counter, to be fixed later 

    // Ensure the counter is loaded correctly when the page reloads:
    // window.onload = function() {
    //   let counter = parseInt(localStorage.getItem('cartCounter')) || 0;
    //   console.log('Loaded counter:', counter); 
    //   updateCartCount(counter);  
    // };
  </script>

</body>

</html>