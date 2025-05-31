<?php


session_start();



include './Backend/Controller/Controller.php';
$controller = new Controller();

$controller->checkCustomerLogin();

if ($controller->isAdmin()) {
    header('Location: ./Admin.php');
} else if ($controller->isCashier()) {
    header('Location: ./Cashier.php');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Retrieve cart items
$cartItems = $_SESSION['cart'];

print_r($_SESSION['cart']);


// Header -- must be modified
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
                                            <input type="text" readonly value="<?= $item['quantity'] ?>" class="form-control quantity-input me-2">
                                        </div>
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


            // Initialize order summary on page load
            updateOrderSummary();
        });


        setInterval(() => {

            location.reload(); // Refresh cart page when cart is updated elsewhere

        }, 1500);
    </script>



</body>

</html>