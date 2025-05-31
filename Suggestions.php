
<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

$controller->checkCustomerLogin();

// Later

// include "./Backend/Controller/HeaderVersion2_Logic.php";
// $links = ["Home"=> "./LandingPage.php", "Cart"=>"./Cart.php","Profile" => "./User.php"];
// $activeLink = "";
// $showButton = true;

$cartItems = $_SESSION['cart'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smart Wishlist</title>

  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />


  <!-- style will be changed later -->

  <style>
    body {
      background: #f9fafb;
      font-family: 'Segoe UI', sans-serif;
    }
    .wishlist-title {
      margin-top: 40px;
      font-weight: 600;
    }
    .product-card, .suggestion-card {
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: 0.3s;
    }
    .product-card:hover, .suggestion-card:hover {
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .section-title {
      margin-top: 30px;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .loading-text {
      font-weight: bold;
      font-size: 1.2rem;
      color: #007bff;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.1rem;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .loading-text .dot {
      animation: pulse 1.5s infinite;
      opacity: 0.3;
    }

    .loading-text .dot:nth-child(2) {
      animation-delay: 0.2s;
    }
    .loading-text .dot:nth-child(3) {
      animation-delay: 0.4s;
    }
    .loading-text .dot:nth-child(4) {
      animation-delay: 0.6s;
    }

    @keyframes pulse {
      0%, 100% {
        opacity: 0.3;
        transform: scale(1);
      }
      50% {
        opacity: 1;
        transform: scale(1.4);
      }
    }


  </style>
</head>
<body>

 <!-- Navbar -->

<div class="container">
  <h1 class="wishlist-title text-center text-primary">🛒 Your Wishlist</h1>
  
  <!-- Cart Items Section -->
  <div class="section-title">🧺 Items In Your Cart</div>
  <div class="row g-3">
    <?php foreach ($cartItems as $item): ?>
      <div class="col-md-4">
        <div class="card product-card p-3 d-flex flex-column" style="height: 150px;">
          <div class="row align-items-center flex-grow-1">
            <!-- Product image -->
            <div class="col-md-4 mb-3 mb-md-0">
              <img src="<?= $item['productImage'] ?>" alt="Product Image" class="img-fluid rounded">
            </div>
            <!-- Product info -->
            <div class="col-md-8">
              <h5 class="mb-1"><?= htmlspecialchars($item['productName']) ?></h5>
              <p class="mb-1 text-muted"><?= htmlspecialchars($item['productDescription']) ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

 


  <div class="section-title">🤖 Suggestions For You</div>

  <div id="suggestions" class="row g-3">
    <!-- Suggestions will be added here by -->


  <div id="loading-spinner" class="loading-text" style="display: none;">
  Loading<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
</div>


  </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", () => {
 
  const suggestionsContainer = document.getElementById("suggestions");
  suggestionsContainer.addEventListener('submit', function (e) {
    
    if (e.target.classList.contains('add-to-cart-form')) {
      e.preventDefault(); // Prevent the form from submitting normally

      const form = e.target;
      const formData = new FormData(form); 

      const xhr = new XMLHttpRequest();
      xhr.open('POST', form.action, true); // POST request to the form action URL

      xhr.onload = function () {
        if (xhr.status === 200) {
         
          const response = JSON.parse(xhr.responseText);
          alert(response.message);

          // Update the localStorage timestamp to track cart changes
          localStorage.setItem('cartUpdated', Date.now());
        } else {
          console.error('Error adding to cart: ', xhr.responseText);
        }
      };

      xhr.onerror = function () {
        console.error('Request failed');
      };

      xhr.send(formData); // Send the form data to the server
    }
  });

  const loadingSpinner = document.getElementById("loading-spinner");

  loadingSpinner.style.display = "block"; // Show spinner

  fetch("index.php?action=getSuggestions")
    .then(response => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then(data => {
      loadingSpinner.style.display = "none"; // Hide spinner

      // Check if the response contains suggestions
      if (data && data.status === "success" && Array.isArray(data.suggestions)) {
        const suggestions = data.suggestions;

        // Clear the suggestions container if no suggestions are found
        if (suggestions.length === 0) {
          suggestionsContainer.innerHTML = "<p>No suggestions available.</p>";
          return;
        }

        suggestionsContainer.innerHTML = ''; 

        // Loop through each suggestion and fetch product details
        suggestions.forEach(item => {
          fetch(`index.php?action=getProductDetails&productId=${item.id}`)
            .then(response => {
              if (!response.ok) throw new Error("Network response was not ok");
              return response.json();
            })
            .then(productDetails => {
              if (productDetails && productDetails.status === "success") {
                const product = productDetails.product;

                // Create a new column for the product card
                const col = document.createElement("div");
                col.className = "col-md-4";
                col.innerHTML = `
                  <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                      <h5 class="card-title">${item.name}</h5>
                      <p class="card-text text-muted">${item.description}</p>
                      <p class="card-text font-weight-bold">$${product.price}</p>

                      <!-- Add to cart form -->
                      <form class="add-to-cart-form" data-product-id="${item.id}" method="POST" action="index.php?action=addToCart">
                        <input type="hidden" name="productId" value="${item.id}">
                        <input type="hidden" name="productName" value="${item.name}">
                        <input type="hidden" name="productPrice" value="${product.price}">
                        <input type="hidden" name="productImage" value="http://localhost/Senior-Project/${product.imageUrl}">
                        <input type="hidden" name="productDescription" value="${product.description}">
                        <input type="number" name="quantity" value="1" min="1"
                               class="form-control mb-2 shadow-sm border-0 rounded-pill text-center quantity-input w-100" />
                        <button type="submit" class="submit btn-primary rounded-pill px-4 shadow-sm w-100">Add to Cart</button>
                      </form>
                    </div>
                  </div>
                `;
                suggestionsContainer.appendChild(col); 
                
               
                setTimeout(() => col.classList.add('visible'), 10);

              } else {
                console.error("Failed to load product details for ID:", item.id);
              }
            })
            .catch(err => {
              console.error("Error fetching product details:", err);
            });
        });
      } else {
        console.error("Invalid data format or empty suggestions", data);
        suggestionsContainer.innerHTML = "<p>Failed to load suggestions. Your cart is currently empty !</p>";
      }
    })
    .catch(err => {
      loadingSpinner.style.display = "none";

      console.error("Error:", err);
      suggestionsContainer.innerHTML = "<p>Error fetching suggestions. Reload the page please</p>";
    });
});

</script>



</body>
</html>


