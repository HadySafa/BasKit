<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="<?= $homeLink ?>">
      <img src="https://flowbite.com/docs/images/logo.svg" alt="Logo" width="30" height="30" class="me-2">
      <h2 class="mb-0 h5">BasKit</h2>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav me-3 mb-2 mb-lg-0">
        <?php foreach ($links as $text => $url):
          $active = strpos($url, $activeLink) !== false ? 'active' : '';
        ?>
          <li class="nav-item">
            <a class="nav-link <?= $active ?>" href="<?= $url ?>"><?= $text ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <form method="post" class="d-flex">
        <input type="hidden" name="Status" value="<?= $buttonText ?>" />
        <button class="btn btn-outline-primary" type="submit"><?= $buttonText ?></button>
      </form>
    </div>
  </div>
</nav>
