<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();
$users = $controller->getAllUsers(); // Get all users


include "./Backend/Controller/HeaderVersion2_Logic.php";
$links = ["Profile" => "./Admin.php"];
$activeLink = "";
$showButton = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $isAjax = isset($_POST['ajax']) && $_POST['ajax'] == 1;
  $action = $_POST['action'] ?? null;

  if ($isAjax && $action) {
    if ($action === 'delete') {
          $result = $controller->deleteUser(['Id' => $_POST['id']]);
          echo json_encode(['status' => $result ? 'success' : 'fail']);
          exit;
      }
  }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="./Style/bootstrap.min.css" />
  <link rel="stylesheet" href="./Style/elements.css" />

  <style>
    body { background-color: var(--color2); }
    .form-section, .user-card { background-color: #fff; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px; }
    .form-label { font-weight: 500; }
    .btn-rounded { border-radius: 50px; }
  </style>
</head>
<body>

  <?php include "./HeaderVersion2_Nav.php" ; ?>

<div class="container py-5">
  <h2 class="mb-4 text-center">🛒 Admin - Manage Users</h2>

  <!-- Existing Users -->
  <h4 class="mb-3">User List</h4>
  <div class="row">
    <?php foreach ($users as $user): ?>
      <div class="col-md-4 mb-4">
        <div class="user-card p-3">
          <h5><?= htmlspecialchars($user['Name']) ?></h5>
          <p class="mb-1">Id: <?= htmlspecialchars($user['Id']) ?></p>
          <p class="mb-1">Email: <?= $user['Email'] ?></p>
          <p class="mb-1">Phone: <?= $user['Phone'] ?></p>
          <div class="d-flex justify-content-between mt-3">
            <a href="EditUser.php?id=<?= $user['Id'] ?>" class="btn btn-sm btn-outline-success btn-rounded">Edit</a>
            <form method="POST" class = "delete-form" data-id="<?= $user['Id'] ?>">
              <button class="btn btn-sm btn-outline-danger btn-rounded">Delete</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


<script>

// Delete Product AJAX

document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this user?')) return;

        const userId = this.dataset.id;
        const formData = new FormData();
        
        formData.append('ajax', 1);
        formData.append('action', 'delete');
        formData.append('id', userId);


        fetch('', { 
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('User deleted!');
                this.closest('.col-md-4').remove(); // Remove the User from the DOM
            } else {
                alert('Failed to delete user.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting user.');
        });
    });
});

</script>


</body>

</html>

