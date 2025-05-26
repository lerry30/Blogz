<?php
  if(!isLoggedin()) {
    redirect('/users/login');
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">                                    <title><?= $title ?? 'Bolgz' ?></title>
  <link href="/assets/css/gen.css" rel="stylesheet">
  <link href="/assets/css/dashboard.css" rel="stylesheet">
  <script type="module" src="/assets/js/layouts/dashboard.js" defer></script>
</head>
<body>
  <header>
    <nav>
      <?php require_once __DIR__."/../components/logo.php"; ?>
      <ul class="slider close">
        <li><a href="">Dashboard</a></li>
        <li><a href="">My Posts</a></li>
        <li><a href="">New Post</a></li>
      </ul>
    </nav>
  </header>
  <!-- Main Content -->
  <div>
    <?php if(isset($_GET['error'])): ?>
      <div>
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
      <div>
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>

    <!-- Content will be injected here -->
    <?php require_once __DIR__."/../$content_path.php"; ?>
  </div>
</body>
</html>
