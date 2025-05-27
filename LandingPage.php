
<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

$buttonText = "";
if ($controller->isCustomerLogin()) {
    $buttonText = "Logout";
} else {
    $buttonText = "Login";
}

if($controller->isAdmin()){
    header('Location: ./Admin.php');
}

else if($controller->isCashier()){
    header('Location: ./Cashier.php');
}


$name = $controller->getUserName();



// handle logout request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['Status'])) {
        if ($_POST['Status'] == "Logout") {
            $controller->logout();
            // redirect after logout
            header("Location: LandingPage.php");
            exit;
        } else {
            $controller->goToLogin();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Shop smarter with SmartShop: the future of online and smart in-store shopping.">
  <meta name="keywords" content="smartshop, ecommerce, online grocery, smart basket">
  <title>SmartShop - Shop Smarter</title>
  <link rel="icon" href="favicon.ico">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link href="./Style/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="./Style/elements.css" />

 
  <style>

    html {
      scroll-behavior: smooth;
    }


    body {
      font-family: 'Inter', sans-serif;
      scroll-behavior: smooth;
    }


    .suggestions-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 9999;
  }

  .suggestions-list div {
    padding: 8px;
    cursor: pointer;
  }

  .suggestions-list div:hover {
    background-color: #f1f1f1;
  }


    #product-slider {
      scroll-snap-type: x mandatory; 
      display: flex;
      gap: 15px;
    }

    .card {
      scroll-snap-align: start; 
    }

    .bestseller-section {
      padding: 50px 0;
      position: relative;
    }

    .product-slider-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      overflow: hidden; 
      position: relative;
    }




    .product-slider {
      display: flex;
      transition: transform 0.5s ease-in-out;
      gap: 15px;
    }

    .slider-arrow-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      padding: 0 1rem;
      position: absolute;
      bottom: 0;
      width: 100%;
    }
    
  

    .product-card {
      flex: 0 0 20%;
      max-width: 20%;
      padding: 0 10px;
      box-sizing: border-box;
      transition: transform 0.3s;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-content {
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      background-color: white;
      overflow: hidden;
    }

    .product-img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .product-info {
      padding: 15px;
      text-align: center;
    }

    .product-title {
      font-size: 1.1rem;
      font-weight: bold;
    }

    .product-price {
      color: #2a5298;
      font-size: 1rem;
      margin-top: 10px;
    }

   .arrow-btn {
      background-color: white;
      border: none;
      border-radius: 50%;
      width: 45px;
      height: 45px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: all 0.3s ease;
}





    .arrow-btn:hover {
      background-color: #2a5298;
      color: white;
      transform: scale(1.1);
    }

    .left-arrow {
      left: 0;
    }

    .right-arrow {
      right: 0;
    }

    .arrow-btn:disabled {
      background-color: #ccc;
      color: white;
      cursor: not-allowed;
      transform: scale(1);
    }
    
    .hero {
      background: linear-gradient(to right, #1e3c72, #2a5298);
      color: white;
      padding: 100px 0;
    }
    .feature-icon {
      font-size: 2rem;
      color: #2a5298;
    }
    .navbar {
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

   .navbar-welcome {
  white-space: nowrap;
  padding: 0 8px;
  
}

.navbar-nav .nav-item {
  margin-right: 8px;
}

.navbar-nav form {
  margin: 0;
}

.navbar-nav .btn {
  padding: 5px 12px;
  font-size: 0.875rem;
}



    @keyframes fadeInSlide {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .navbar-nav .nav-link.welcome-message {
      animation: fadeInSlide 0.6s ease forwards;
      color: #2a5298;
    }


    .footer {
      background-color: #1e3c72;
      color: white;
    }


    .category-box {
      text-align: center;
      padding: 20px;
      border: 2px solid #ddd;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .category-box a {
      text-decoration: none;
    }
    .category-box:hover {
      background-color: #f8f9fa;
      transform: scale(1.05);
    }
    .category-icon {
      font-size: 3rem;
      color: #2a5298;
      margin-bottom: 10px;
    }
    .category-name {
      font-weight: bold;
      font-size: 1.1rem;
      color: #333;
    }

  </style>
</head>

<body>

  <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold me-2" href="#">Baskit SmartShop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <form class="d-flex ms-2 w-50" id="search-form">
        <div class="position-relative w-100">
          <input class="form-control me-2 w-100" type="search" placeholder="Search" aria-label="Search" id="search-input"
            autocomplete="off">
          <div id="suggestions" class="suggestions-list"></div>
        </div>
      </form>
<ul class="navbar-nav ms-auto d-flex align-items-center">

  <!-- Links -->
  <li class="nav-item">
    <a class="nav-link" href="Products.php"><i class="fas fa-box"></i> Products</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="Cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
  </li>


  <!-- Welcome message -->
<li class="nav-item d-flex align-items-center">
  <?php if ($name): ?>
    <a class="nav-link" href="User.php">
      <i class="fas fa-user-circle"></i> Profile
    </a>
    <span class="nav-link welcome-message me-2">Welcome, <?php echo htmlspecialchars($name); ?>!</span>
    
  <?php endif; ?>
</li>



  <!-- Button -->
  <li class="nav-item d-flex align-items-center">
    <form method="post" class="d-flex align-items-center mb-0">
      <input type="hidden" name="Status" value="<?php echo $buttonText; ?>" />
      <button type="submit" class="btn btn-outline-primary btn-sm"><?php echo $buttonText; ?></button>
    </form>
  </li>

</ul>



    </div>
  </div>
</nav>

  <!-- Hero Section -->
  <header class="hero text-center" id="home">
    <div class="container" data-aos="fade-up">
      <h1 class="display-4 fw-bold">Welcome to Baskit</h1>
      <p class="lead">The future of shopping, both online and in-store with smart baskets</p>
      <a href="#features" class="btn btn-light btn-lg mt-3">Explore Features</a>
    </div>
  </header>

  <!-- Features Section -->
  <section class="py-5" id="features">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-4" data-aos="zoom-in">
          <div class="mb-3 feature-icon">🛒</div>
          <h5>Smart Basket</h5>
          <p>Use the Baskit for sccanning the products in store.</p>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
          <div class="mb-3 feature-icon">💳</div>
          <h5>Self Checkout</h5>
          <p>Pay directly from your cart via mobile.</p>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
          <div class="mb-3 feature-icon">📦</div>
          <h5>Online Ordering</h5>
          <p>Shop online and pick up at your convenience.</p>
        </div>

        <div class="col-md-4 offset-md-4" data-aos="zoom-in" data-aos-delay="200">
          <div class="mb-3 feature-icon">🤖</div>
          <h5>AI Suggestions</h5>
          <p>Don't know what to buy? No worries! Our WishLsit will help you choose the
             right products based on your interest.</p>
        </div>
      </div>
    </div>
  </section>

<section class="bg-light" id="categories">
  <div class="container py-5">
    <div class="text mb-5">
      <h2 class="fw-bold">Categories</h2>
      <p class="text-muted">Explore our wide range of product categories</p>
    </div>
    <div class="row text-center">
      <div class="col-md-3" data-os="fade-up">
        <div class="category-box">
          <a href="Products.php?query=&category=1&price=lowest">
            <i class="fas fa-baby category-icon"></i>
            <p class="category-name">Baby & Kids</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="category-box">
          <a href="Products.php?query=&category=2&price=lowest">
            <i class="fas fa-bread-slice category-icon"></i>
            <p class="category-name">Bakery</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="category-box">
          <a href="Products.php?query=&category=3&price=lowest">
            <i class="fas fa-mug-hot category-icon"></i>
            <p class="category-name">Beverages</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="category-box">
          <a href="Products.php?query=&category=4&price=lowest">
            <i class="fas fa-box category-icon"></i> 
            <p class="category-name">Canned Goods</p>
          </a>
        </div>
      </div>
      <div class="col-md-3">
        <div class="category-box" data-aos="fade-up" data-aos-delay="400" >
          <a href="Products.php?query=&category=5&price=lowest">
            <i class="fas fa-broom category-icon"></i>
            <p class="category-name">Cleaning & Household</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="500">
        <div class="category-box">
          <a href="Products.php?query=&category=6&price=lowest">
            <i class="fas fa-egg category-icon"></i> 
            <p class="category-name">Dairy & Eggs</p>
          </a>
        </div>
      </div>
      <div class="col-md-3">
        <div class="category-box" data-aos="fade-up" data-aos-delay="600">
          <a href="Products.php?query=&category=7&price=lowest">
            <i class="fas fa-snowflake category-icon"></i>
            <p class="category-name">Frozen Foods</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="700">
        <div class="category-box">
          <a href="Products.php?query=&category=8&price=lowest">
            <i class="fas fa-apple-alt category-icon"></i>
            <p class="category-name">Fruits</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="800">
        <div class="category-box">
          <a href="Products.php?query=&category=9&price=lowest">
            <i class="fas fa-heart category-icon"></i>
            <p class="category-name">Health & Beauty</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="900">
        <div class="category-box">
          <a href="Products.php?query=&category=10&price=lowest">
            <i class="fas fa-drumstick-bite category-icon"></i>
            <p class="category-name">Meat & Poultry</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="1000">
        <div class="category-box">
          <a href="Products.php?query=&category=11&price=lowest">
            <i class="fas fa-tooth category-icon"></i>
            <p class="category-name">Personal Care</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="1100">
        <div class="category-box">
          <a href="Products.php?query=&category=12&price=lowest">
            <i class="fas fa-paw category-icon"></i>
            <p class="category-name">Pet Supplies</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="1200">
        <div class="category-box">
          <a href="Products.php?query=&category=13&price=lowest">
            <i class="fas fa-cookie category-icon"></i>
            <p class="category-name">Snack</p>
          </a>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="1300">
        <div class="category-box">
          <a href="Products.php?query=&category=14&price=lowest">
            <i class="fas fa-carrot category-icon"></i>
            <p class="category-name">Vegetables</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>


  <!-- Product Section -->
  <section class="py-5 bg-light" id="products">
    <div class="container">
      <h2 class="text mb-5" data-aos="fade-up">Popular Products</h2>
      <div class="row">
        <div class="col-md-4" data-aos="fade-right">
          <div class="card">
            <img src="https://via.placeholder.com/400x300" class="card-img-top" alt="Product 1">
            <div class="card-body">
              <h5 class="card-title">Product 1</h5>
              <p class="card-text">High quality and great price.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up">
          <div class="card">
            <img src="https://via.placeholder.com/400x300" class="card-img-top" alt="Product 2">
            <div class="card-body">
              <h5 class="card-title">Product 2</h5>
              <p class="card-text">Best seller with excellent reviews.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
          <div class="card">
            <img src="https://via.placeholder.com/400x300" class="card-img-top" alt="Product 3">
            <div class="card-body">
              <h5 class="card-title">Product 3</h5>
              <p class="card-text">Loved by our customers.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


      <!-- To be changed  -->

<div class="container bestseller-section">
  <h2 class="text mb-4">Bestseller Products</h2>

  <!-- Slider Wrapper -->
  <div class="product-slider-wrapper">
    <div class="product-slider" id="product-slider">
      <!-- Product cards go here -->
    </div>

    <div class="slider-arrow-controls">
      <button id="prev-button" class="arrow-btn left-arrow" disabled>
        <i class="fas fa-chevron-left"></i>
      </button>
      <button id="next-button" class="arrow-btn right-arrow">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>
</div>

  <!-- Contact Section -->
 <section class="py-5" id="contact">
    <div class="container">
        <h2 class="text-center mb-4" data-aos="fade-up">Let us know your comments!</h2>
        <div class="d-flex justify-content-center">
            <a href="Feedback.php" class="btn btn-primary">Send Message</a>
        </div>
    </div>
</section>



  <!-- Footer -->
  <footer class="py-4 text-center footer">
    <div class="container">
      <p>&copy; 2025 SmartShop. All rights reserved.</p>
      <div>
        <a href="#" class="text-white me-3">Facebook</a>
        <a href="#" class="text-white me-3">Twitter</a>
        <a href="#" class="text-white">Instagram</a>
      </div>
    </div>
  </footer>

 

  <script src="./Script/bootstrap.bundle.min.js"></script>
  <script src="./Script/aos.js"></script>
  

  <script>
  
const productSlider = document.getElementById('product-slider');
const nextButton = document.getElementById('next-button');
const prevButton = document.getElementById('prev-button');
let products = [];

let startIndex = 0;

function updateSliderPosition() {
  const productWidth = productSlider.querySelector('.product-card')?.offsetWidth || 0;
  const offset = -(startIndex * productWidth);
  productSlider.style.transform = `translateX(${offset}px)`;
}

function updateButtonStates() {
  prevButton.disabled = startIndex === 0;
  nextButton.disabled = startIndex >= products.length - 5;
}

nextButton.addEventListener('click', () => {
  if (startIndex < products.length - 5) {
    startIndex++;
    updateSliderPosition();
    updateButtonStates();
  }
});

prevButton.addEventListener('click', () => {
  if (startIndex > 0) {
    startIndex--;
    updateSliderPosition();
    updateButtonStates();
  }
});

// Fetch best selling products from the backend
async function fetchBestsellingProducts() {
  const response = await fetch('index.php?action=getBestsellers');
  
  if (response.ok) {
    const data = await response.text(); 
    
    try {
      products = JSON.parse(data); 
      renderProducts(products); 
    } catch (error) {
      console.error('Failed to parse response as JSON:', error);
    }
  } else {
    console.error('Failed to fetch bestselling products');
  }
}


// Render products in the slider
function renderProducts() {
  productSlider.innerHTML = '';

  products.forEach(product => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <div class="product-content">
        <img src="${product.img}" class="product-img" alt="${product.name}">
        <div class="product-info">
          <div class="product-title">${product.name}</div>
          <div class="product-price">$${product.price}</div>

          <!-- Add to cart form -->
                      <form class="add-to-cart-form" data-product-id="${product.id}" method="POST" action="index.php?action=addToCart">
                        <input type="hidden" name="productId" value="${product.id}">
                        <input type="hidden" name="productName" value="${product.name}">
                        <input type="hidden" name="productPrice" value="${product.price}">
                        <input type="hidden" name="productImage" value="http://localhost/Senior-Project/${product.img}">
                        <input type="hidden" name="productDescription" value="${product.description}">
                        <input type="number" name="quantity" value="1" min="1"
                               class="form-control mb-2 shadow-sm border-0 rounded-pill text-right quantity-input w-100" />
                        <button type="submit" class="submit btn-primary rounded-pill px-4 shadow-sm w-100">Add to Cart</button>
                      </form>
        </div>
      </div>
    `;
    productSlider.appendChild(card);
  });

  updateSliderPosition();
  updateButtonStates();
}

// Fetch and display bestselling products when page loads
fetchBestsellingProducts();


// Add to Cart 

document.addEventListener('submit', function (e) {
  if (e.target.matches('.add-to-cart-form')) {
    e.preventDefault();

    const form = e.target;
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
  }
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
      console.log("RAW RESPONSE:", responseText); // debug

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

</script>

 
  <script>
    AOS.init();
  </script>

</body>

</html>

