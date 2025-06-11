<main>
  <div>
    <h4><?= $title ?></h4>
  </div>
  <div class="card-cont">
    <?php if(!isset($archived_blog_posts) || empty($archived_blog_posts)  || count($archived_blog_posts) == 0): ?>
      <div class="empty-list">
        <h3>Empty</h3>
      </div>
    <?php endif ?>
    <?php foreach($archived_blog_posts as $post): ?>
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
          <a href="" class="btn btn-gray">View</a>
          <form
            action="/posts/unarchive"
            method="post"
          >
            <input type="hidden" name="_token" value="<?= $csrf_token; ?>" />
            <input type="hidden" name="post" value="<?= $post['id'] ?>" />
            <button class="btn btn-gray">Unarchive</button>
          </form>
          <form
            action="/posts/delete"
            method="post"
            class="del-post"
          >
            <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
            <input type="hidden" name="post" value="<?= $post['id'] ?>" />
            <button type="button" class="btn btn-red">
              Delete
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
