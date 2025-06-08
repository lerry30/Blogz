<main>
  <div>
    <h4><?= $title ?></h4>
  </div>
  <form action="/tags" method="post">
    <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
    <input type="hidden" name="post" value="<?= $postId ?>" />
    <div class="tag-cont">
      <?php foreach($tags as $tag): ?>
        <div class="tag">
          <span><?= $tag['name'] ?></span>
          <input
            type="checkbox"
            name="s-tags[]"
            value="<?= $tag['name'] ?>"
          />
        </div>
      <?php endforeach; ?>
    </div>
    <button name="btn" class="btn">
      Submit
    </button>
  </form>
</main>
