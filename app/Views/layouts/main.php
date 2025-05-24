<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Bolgz' ?></title>
  <link href="/assets/css/style.css" rel="stylesheet">
  <script type="module" src="/assets/js/main.js" defer></script>
</head>
<body>
  <header>
    <nav>
      <button class="logo">Blogz</button>
      <ul class="closed">
        <li><a href="/" class="logo">Blogz</a></li>
        <li><a href="/users/create">Signup</a></li>
        <li><a href="/users/login">Signin</a></li>
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
    <?php require_once __DIR__ . "/../$content_path.php"; ?>
  </div>

  <!-- Footer -->
  <footer>
    <div>
      <p>&copy; <?= date('Y') ?> Blogz. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
