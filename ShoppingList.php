<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping List</title>
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .shopping-container {
      max-width: 600px;
      margin: 40px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }
    .form-control, .btn {
      border-radius: 12px;
    }
    .list-group-item {
      border-radius: 10px;
      margin-bottom: 10px;
    }
    .item-name.checked {
      text-decoration: line-through;
      color: #aaa;
    }
    .remove-btn {
      border: none;
      background: none;
      color: #dc3545;
      font-size: 1.1rem;
    }
    .remove-btn:hover {
      color: #a71d2a;
    }
    .input-group > .form-control {
      border-right: 0;
    }
    .input-group > .btn-primary {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }
  </style>
</head>
<body>
  
<div class="shopping-container container mt-4">
  <h3 class="text-center mb-4">🛒 My Shopping List</h3>

  <div class="input-group mb-3">
    <input
      type="text"
      id="shopping-input"
      class="form-control"
      placeholder="Type an item and press Enter..."
      aria-label="Shopping item input"
    />
    <button class="btn btn-primary" id="addItemBtn" type="button">Add</button>
  </div>

  <ul class="list-group" id="shoppingList"></ul>

  <div class="d-grid mt-3">
    <button class="btn btn-outline-danger" id="clearAllBtn" type="button">Clear All</button>
  </div>

  <hr class="my-4" />

  <div class="d-grid mb-3">
    <button class="btn btn-success" id="submitBtn" type="button">
      Get AI Suggestions
    </button>
  </div>

  <div id="suggestionsContainer" class="row"></div>
</div>


<script>

document.addEventListener('DOMContentLoaded', () => {
  const shoppingInput = document.getElementById('shopping-input');
  const addItemBtn = document.getElementById('addItemBtn');
  const clearAllBtn = document.getElementById('clearAllBtn');
  const shoppingListElement = document.getElementById('shoppingList');
  const submitBtn = document.getElementById('submitBtn');
  const suggestionsContainer = document.getElementById('suggestionsContainer');
  const list = shoppingListElement; 

  // Load saved shopping list from session on page load
  async function loadShoppingList() {
    const response = await fetch('index.php?action=getShoppingList');
    const data = await response.json();

    if (data.status === 'success' && Array.isArray(data.items)) {
      shoppingListElement.innerHTML = ''; // Clear existing

      data.items.forEach(item => {
        const li = document.createElement('li');
        li.classList.add('list-group-item');
        li.innerHTML = `<span class="item-text">${item}</span> <button class="btn btn-sm btn-danger float-end remove-btn">×</button>`;
        shoppingListElement.appendChild(li);
      });
    } else {
      console.error('Failed to load shopping list');
    }
  }

  // Save the current shopping list to session
  async function saveShoppingList() {
    const items = Array.from(shoppingListElement.querySelectorAll('.item-text'))
                       .map(span => span.textContent.trim());
    try {
      const res = await fetch("index.php?action=saveShoppingList", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ items })
      });
      const data = await res.json();
      if (data.status !== "success") console.error("Save failed", data.message);
    } catch (err) {
      console.error("Error saving shopping list:", err);
    }
  }

  // Add item to list
  function addItem(text) {
    if (!text.trim()) return;
    // Prevent duplicates
    const existingItems = Array.from(shoppingListElement.querySelectorAll('.item-text'))
                               .map(span => span.textContent.toLowerCase());
    if (existingItems.includes(text.toLowerCase())) return;

    const li = document.createElement('li');
    li.classList.add('list-group-item');
    li.innerHTML = `<span class="item-text">${text}</span> <button class="btn btn-sm btn-danger float-end remove-btn">×</button>`;
    shoppingListElement.appendChild(li);
    saveShoppingList();
  }

  // Handle add button click and Enter key on input
  addItemBtn.addEventListener('click', () => {
    addItem(shoppingInput.value);
    shoppingInput.value = '';
  });
  shoppingInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      addItem(shoppingInput.value);
      shoppingInput.value = '';
    }
  });

  // Remove item when clicking remove button
  shoppingListElement.addEventListener('click', e => {
    if (e.target.classList.contains('remove-btn')) {
      e.target.closest('li').remove();
      saveShoppingList();
    }
  });

  // Clear all items button
  clearAllBtn.addEventListener('click', () => {
    shoppingListElement.innerHTML = '';
    saveShoppingList();
  });

  // Get suggestions from AI
submitBtn.addEventListener("click", async () => {
  // Show loading message while fetching suggestions
  suggestionsContainer.innerHTML = "<p class='text-muted'>Loading suggestions...</p>";

  try {
    const res = await fetch("index.php?action=getListSuggestions");
    const data = await res.json();
    console.log('Suggestions:', data);

    if (data.status !== "success") {
      suggestionsContainer.innerHTML = `<p class="text-danger">${data.message}</p>`;
      return;
    }

    suggestionsContainer.innerHTML = ""; // Clear previous suggestions

    //Create an array of promises to fetch product details in parallel
    const detailPromises = data.suggestions.map(item =>
      fetch(`index.php?action=getProductDetails&productId=${item.id}`)
        .then(res => res.json())
        .then(detailData => ({ item, detailData }))  // Attach suggestion item with detail response
        .catch(err => {
          console.error("Error fetching product details:", err);
          return null;
        })
    );

    //Await all detail fetches to complete simultaneously
    const detailsResults = await Promise.all(detailPromises);

    //Loop through all fetched details and render product cards
    detailsResults.forEach(result => {
      // Skip if there was an error or invalid response
      if (!result || result.detailData.status !== "success") return;

      const { item, detailData } = result;
      const product = detailData.product;

      // Create the product card element
      const col = document.createElement("div");
      col.className = "col-md-4 mb-4";
      col.innerHTML = `
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title">${item.name}</h5>
            <p class="card-text text-muted">${product.description}</p>
            <p class="card-text font-weight-bold">$${product.price}</p>
            <form class="add-to-cart-form" method="POST" action="index.php?action=addToCart">
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

      // Auto-check list item if product matches (both user term and suggested product)
      markAsChecked(item.matchedTerm);
      markAsChecked(item.name);
    });

  } catch (err) {
    console.error("Suggestion error:", err);
    suggestionsContainer.innerHTML = "<p class='text-danger'>Failed to load suggestions.</p>";
  }
});

// Mark item in list as checked
function markAsChecked(productName) {
  const allItems = list.querySelectorAll("li");
  allItems.forEach(li => {
    const text = li.querySelector(".item-text").textContent.toLowerCase();
    if (productName.toLowerCase().includes(text)) {
      li.classList.add("text-success");
      li.style.textDecoration = "line-through";
    }
  });
}


  // Initial load
  loadShoppingList();
});

</script>

</body>
</html>
