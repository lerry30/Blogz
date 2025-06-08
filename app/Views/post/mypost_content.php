<main>
  <div>
    <h4><?= $title ?></h4>
  </div>
  <div class="card-cont">
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
