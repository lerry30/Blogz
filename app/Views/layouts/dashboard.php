<?php
  use App\Helpers\Auth;

  if(!Auth::isLoggedIn()) {
    redirect('/users/login');
    die();
  }

  $user = Auth::getUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">                                    <title><?= $title ?? 'Bolgz' ?></title>
  <link href="/assets/css/gen.css" rel="stylesheet">
  <link href="/assets/css/<?= $css_file ?>.css" rel="stylesheet">
  <script type="module" src="/assets/js/layouts/dashboard.js" defer></script>
  <?php if(isset($js_file)): ?>
    <script type="module" src="/assets/js/pages/<?= ltrim($js_file, '/') ?>.js"></script>
  <?php endif; ?>
</head>
<body>
  <header>
    <nav>
      <?php require_once getRoot()."/app/Views/components/logo.php"; ?>
      <ul class="slider close">
        <li><a href="/dashboards/user">Dashboard</a></li>
        <li><a href="/posts/mypost">My Posts</a></li>
        <li><a href="/categories/create">New Post</a></li>
        <li><a href="/posts/myarchived">Archive</a></li>
        <li><a href="/users/logout">Logout</a></li>
      </ul>
    </nav>
  </header>
  <!-- Main Content -->
  <div class="splash-cont">
    <?php if(isset($_GET['error'])): ?>
      <div class="splash splash-error">
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
      <div class="splash splash-success">
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php endif; ?>

    <!-- Content will be injected here -->
    <div class="page-content">
      <?php require_once getRoot()."/app/Views/$content_path.php"; ?>
    </div>
  </div>
</body>
</html>
