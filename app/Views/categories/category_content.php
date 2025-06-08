<main>
  <div class="cat-head flex-center">
    <h4><?= $title ?></h4>
  </div>
  <div id="category-list" class="category-list">
    <?php foreach($categories as $category): ?>
      <div class="category-card <?= $category['is_active'] ? '' : 'inactive' ?>">
        <a href="/posts/create/<?= $category['id'] ?>">
          <h2><?= $category['name'] ?></h2>
          <p><?= $category['description'] ??= 'No description available' ?></p>
          <span class="post-count">
            <?= $category['post_count'] ?> posts
          </span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</main>
<script src="/assets/js/pages/categories/index.js"></script>
