<main>
  <div>
    <h4><?= $title ?></h4>
  </div>
  <div class="card-cont">
    <?php if(!isset($blog_posts) || empty($blog_posts) || count($blog_posts) == 0): ?>
      <div class="empty-list">
        <h3>No blog post found</h3>
      </div>
    <?php endif; ?>
    <?php foreach($blog_posts as $post): ?>
      <?php $isPublished = strtolower($post['status']) == 'published'; ?>
      <div class="card">
        <img
          src="/assets/img/uploads/<?= $post['featured_image'] ?>"
          alt="<?= $post['title'] ?>"
        />
        <h4 class="title"><?= $post['title'] ?></h4>
        <article class="info">
          <span><?= new DateTime($post['updated_at'])->format('F j, Y') ?></span>
          <span><?= $post['view_count'] ?> views</span>
          <span class="<?= $post['status'] ?>">
            <?= strtoupper($post['status']) ?>
          </span>
        </article>
        <p><?= $post['excerpt'] ?></p>
        <div class="btn-cont">
          <a href="" class="btn btn-gray">Edit</a>
          <?php if($isPublished): ?>
            <a href="" class="btn btn-gray">View</a>
          <?php else:?>
            <a href="" class="btn">Publish</a>
          <?php endif; ?>
          <form
            action="/posts/archive"
            method="post"
            class="arch-post"
          >
            <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
            <input type="hidden" name="post" value="<?= $post['id'] ?>" />
            <button class="btn btn-red">
              Archive
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
