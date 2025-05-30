<?php


session_start();

include './Backend/Controller/Controller.php';
$controller = new Controller();

$controller->checkCustomerLogin();

if($controller->isAdmin()){
    header('Location: ./Admin.php');
}

else if($controller->isCashier()){
    header('Location: ./Cashier.php');
}

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Retrieve cart items
$cartItems = $_SESSION['cart'];

// Header
include "./Backend/Controller/HeaderVersion2_Logic.php";
$links = ["Home" => "./LandingPage.php", "Profile" => "./User.php",  "Contact" => "./Feedback.php"];
$activeLink = "";
$showButton = true;

?>

<!doctype html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />
  <link rel="stylesheet" href="./Style/cart.css" />
  <title>Shopping Cart</title>

</head>

<body>
  <!-- Header -->
  <?php include "./HeaderVersion2_Nav.php" ?>

  <div class="container py-5">

    <?php if (empty($cartItems)): ?>

      <div class="empty-cart w-100">
        <div class="empty-cart-icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Looks like you have not added anything yet.</p>
        <a href="Products.php">Start Shopping</a>
      </div>

    <?php else: ?>

      <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
          <div class="list-group">
            <?php foreach ($cartItems as $item): ?>
              <div class="list-group-item mb-3 cart-item p-3" data-product-id="<?= $item['productId'] ?>">
                <div class="row align-items-center">
                  <!-- Product image -->
                  <div class="col-md-3 mb-3 mb-md-0">
                    <img src="<?= htmlspecialchars($item['productImage']) ?>" alt="Product Image" class="img-fluid rounded">
                  </div>
                  <!-- Product info -->
                  <div class="col-md-5">
                    <h5 class="mb-1"><?= htmlspecialchars($item['productName']) ?></h5>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($item['productDescription']) ?></p>
                  </div>
                  <!-- Pricing and quantity -->
                  <div class="col-md-4 text-md-end">
                    <h5 class="fw-bold" data-price="<?= $item['productPrice'] ?>">$<?= number_format($item['productPrice'] * $item['quantity'], 2) ?></h5>
                    <div class="d-flex align-items-center justify-content-md-end my-2">
                      <button class="btn btn-outline-secondary btn-sm me-2" data-action="decrease" data-id="<?= $item['productId'] ?>">-</button>
                      <input type="text" value="<?= $item['quantity'] ?>" class="form-control quantity-input me-2">
                      <button class="btn btn-outline-secondary btn-sm" data-action="increase" data-id="<?= $item['productId'] ?>">+</button>
                    </div>
                    <button class="btn btn-danger btn-sm remove-btn" data-id="<?= $item['productId'] ?>">Remove</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Order Summary</h5>
              <ul class="list-group list-group-flush my-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Subtotal
                  <span id="subtotal">$0.00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Shipping
                  <span id="shipping">$5.00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Tax
                  <span id="tax">$0.00</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                  Total
                  <span id="total">$0.00</span>
                </li>
              </ul>
              <a href="Checkout.php" class="btn w-100" style="background-color: var(--color1); color: white;">Proceed to Checkout</a>


            </div>

          </div>
          <a href="wishlist.php" class="btn w-100 wishlist-btn">
            <span class="emoji">🤖</span>AI Suggesstions
          </a> <!-- Button -->

        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Update the order summary
      function updateOrderSummary() {
        let subtotal = 0;
        // Iterate through all cart items to calculate the subtotal
        document.querySelectorAll('.cart-item').forEach(item => {
          const quantity = parseInt(item.querySelector('.quantity-input').value, 10);
          const price = parseFloat(item.querySelector('.fw-bold').dataset.price);
          subtotal += price * quantity;
        });

        // Shipping is static at $5.00
        const shipping = 0.00;

        // Tax is 11% of subtotal
        const tax = subtotal * 0.11;

        // Calculate total
        const total = subtotal + shipping + tax;

        // Update the order summary in the UI
        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('shipping').textContent = `$${shipping.toFixed(2)}`;
        document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;
      }

      // Handle the plus/minus buttons using data-action attributes
      document.querySelectorAll('.btn-sm').forEach(button => {
        button.addEventListener('click', (e) => {
          const input = e.target.closest('.cart-item').querySelector('.quantity-input');
          let currentValue = parseInt(input.value, 10);
          if (isNaN(currentValue) || currentValue < 1) {
            currentValue = 1;
          }
          const action = e.target.dataset.action;
          if (action === 'increase') {
            currentValue++;
          } else if (action === 'decrease' && currentValue > 1) {
            currentValue--;
          }
          input.value = currentValue;

          // Send AJAX request to update the quantity in the cart on the server
          const productId = e.target.dataset.id;
          updateQuantityOnServer(productId, currentValue);

          // Recalculate and update price based on the new quantity
          updatePrice(e.target.closest('.cart-item'), currentValue);

          // Update the order summary after price change
          updateOrderSummary();
        });
      });

      // Function to update quantity on the server
      function updateQuantityOnServer(productId, quantity) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?route=updateCart', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
          if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
              console.log('Cart updated');
            } else {
              console.error('Error:', response.message);
            }
          } else {
            console.error('Error: ' + xhr.statusText);
          }
        };
        xhr.send(`productId=${productId}&quantity=${quantity}`);
      }

      // Function to update price based on quantity
      function updatePrice(cartItem, quantity) {
        const priceElement = cartItem.querySelector('.fw-bold');
        const productPrice = parseFloat(priceElement.dataset.price);
        const totalPrice = (productPrice * quantity).toFixed(2);

        if (quantity < 1) {
          priceElement.textContent = `$${productPrice}`;
        } else {
          priceElement.textContent = `$${totalPrice}`;
        }
      }

      document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', (e) => {
          // Replace any non-digit characters with nothing
          e.target.value = e.target.value.replace(/\D/g, '');
        });

        // On change (when the user leaves the input field), ensure the value is valid
        input.addEventListener('change', (e) => {
          let val = parseInt(e.target.value, 10);
          if (isNaN(val) || val < 1) {
            e.target.value = 1;
          } else {
            e.target.value = val;
          }

          updateOrderSummary();
          // Update the server and price on change
          const productId = e.target.closest('.cart-item').querySelector('.btn-sm').dataset.id;
          updateQuantityOnServer(productId, val);
          updatePrice(e.target.closest('.cart-item'), val);

        });
      });

      // Handle the remove button click event
      document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          const productId = e.target.dataset.id;

          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'index.php?route=removeFromCart', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.send(`productId=${productId}`);

          xhr.onload = function() {
            if (xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              if (response.status === 'success') {
                e.target.closest('.cart-item').remove();
                alert(response.message);

                // Check if any cart items remain
                if (document.querySelectorAll('.cart-item').length === 0) {
                  // Replace entire cart container content with the empty cart message HTML
                  const container = document.querySelector('.container.py-5');
                  container.innerHTML = `
                <div class="empty-cart w-100">
                  <div class="empty-cart-icon">🛒</div>
                  <h3>Your cart is empty</h3>
                  <p>Looks like you have not added anything yet.</p>
                  <a href="Products.php">Start Shopping</a>
                </div>
              `;
                }
                // Update the order summary after removing an item
                updateOrderSummary();
              } else {
                console.error('Error:', response.message);
              }
            } else {
              console.error('Error: ' + xhr.statusText);
            }
          };
        });
      });

      // Initialize order summary on page load
      updateOrderSummary();
    });


    window.addEventListener('storage', function(e) {
      if (e.key === 'cartUpdated') {
        location.reload(); // Refresh cart page when cart is updated elsewhere
      }
    });
  </script>



</body>

</html>