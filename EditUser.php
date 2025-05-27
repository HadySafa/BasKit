<?php

require_once './Backend/Controller/Controller.php';

$userId = (int) $_GET['id'];
$controller = new Controller();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
  $controller->updateUser($_POST);
  
  echo json_encode([
      'status' => 'success',
      'message' => 'User updated successfully!'
  ]);
  exit;
}


// get the user with the specified id
if (isset($_GET['id'])) {
  $user = $controller->getUserById($_GET['id']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  
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

<div class="container py-5">
  <h2 class="mb-4 text-center">✏️ Admin - Edit User</h2>

  <div class="form-section mb-5">
    <h4>Edit User Details</h4>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form id="editUserForm" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="ajax" value="1">
      <input type="hidden" name="Id" value="<?= $user->getId() ?>">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">User Name</label>
          <input type="text" name="Name" class="form-control" value="<?= htmlspecialchars($user->getName()) ?>" required>
        </div>


        <div class="col-md-6 mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="Phone" class="form-control" value="<?= htmlspecialchars($user->getPhone()) ?>">
        </div>

        <div class="col-md-3 mb-3">
          <label class="form-label">Balance $</label>
          <input type="number" name="Balance" class="form-control" value="<?= $user->getBalance() ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Active</label>
          <select name="Active" class="form-control">
            <option value="1" <?= $user->isActive() ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= !$user->isActive() ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Role</label>
          <input type="text" name="Role" class="form-control" value="<?= htmlspecialchars($user->getRole()) ?>">
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="submit btn-primary btn-rounded px-4">Update User</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('editUserForm').addEventListener('submit', function(e) {
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
