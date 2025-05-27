<?php
require_once './Backend/Controller/Controller.php';


$controller = new Controller();
$search = $_GET['query'] ?? '';
$category = $_GET['category'] ?? '';
$price = $_GET['price'] ?? '';

$products = $controller->searchProducts($search, $category, $price);
$categories = $controller->getCategories(); 


include "./Backend/Controller/HeaderVersion2_Logic.php";
$links = ["Home" => "./LandingPage.php"  ,"Cart" => "./Cart.php" ,"Products" => "./Products.php" ,  "Profile" => "./User.php" ,  "Contact" => "./Feedback.php"  ];
$activeLink = "";
$showButton = true;

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />
</head>

<body style="background-color: var(--color2);">

<?php include "./HeaderVersion2_Nav.php"?>

<div class="container mt-4">
  <?php if (!empty($products)): ?>
    <div class="row">
      <?php foreach ($products as $product): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
          <div class="card h-100 shadow-sm">


          <!-- Link around image -->
          <a href="productDetails.php?id=<?= htmlspecialchars($product['Id']) ?>">
            <img src="http://localhost/Senior-Project/<?= htmlspecialchars($product['ImageUrl']); ?>" 
                 class="card-img-top" 
                 alt="Product Image" 
                 style="height: 100px; object-fit: cover;">
          </a>  


            <div class="card-body d-flex flex-column">

            
            <!-- Link around product name -->
            <h5 class="card-title text-center" style="color: var(--color1);">
              <a href="productDetails.php?id=<?= htmlspecialchars($product['Id']) ?>" class="text-decoration-none text-dark">
                <?= htmlspecialchars($product['Name']) ?>
              </a>
            </h5> 


              <p class="text-success text-center fw-bold">
                $<?= htmlspecialchars($product['Price']) ?>
              </p>

              <!-- Add to cart form -->
            <form class="add-to-cart-form" data-product-id="<?= htmlspecialchars($product['Id']) ?>" method="POST" action="index.php?action=addToCart">
              <input type="hidden" name="productId" value="<?= htmlspecialchars($product['Id']) ?>">
              <input type="hidden" name="productName" value="<?= htmlspecialchars($product['Name']) ?>">
              <input type="hidden" name="productPrice" value="<?= htmlspecialchars($product['Price']) ?>">
              <input type="hidden" name="productImage" value="<?= 'http://localhost/Senior/' . htmlspecialchars($product['ImageUrl']) ?>">
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

  <?php else: ?>
    <p class="text-center mt-5">No products found.</p>
  <?php endif; ?>

</div>



<script>

  // add to cart from Search page
  
document.addEventListener('DOMContentLoaded', () => {
  const addToCartForms = document.querySelectorAll('.add-to-cart-form');

  addToCartForms.forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // Prevent default form submission

      const formData = new FormData(form);
      const xhr = new XMLHttpRequest();
      
      
      xhr.open('POST', form.action, true);
      
      xhr.onload = function () {
        console.log(xhr.responseText);
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          alert(response.message);
          localStorage.setItem('cartUpdated', Date.now());
        }
      };
      
      xhr.send(formData);
    });
  });
});

</script>


</body>
</html>

