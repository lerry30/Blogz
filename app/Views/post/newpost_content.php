<main class="post-form">
  <div>
    <h4><?= $title ?></h4>
  </div>
  <form action="/posts" method="post" enctype="multipart/form-data">
    <h5>Category: <?= $category['name'] ?></h5>
    <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
    <input type="hidden" name="category" value="<?= $category['id'] ?>" />
    <div class="col">
      <label for="title">
        Title
        <span class="text-red">*</span>
      </label>
      <input id="title" name="title" />
    </div>
    <div class="col">
      <label for="short-desc">
        Short Description
        <span class="text-red">*</span>
      </label>
      <input id="short-desc" name="short-desc" />
    </div>
    <div class="col">
      <label for="content">
        Content
        <span class="text-red">*</span>
      </label>
      <textarea id="content" name="content">
      </textarea>
    </div>
    <div class="col">
      <label for="ftr-img">
        Featured Image
        <span class="text-red">*</span>
      </label>
      <input type="file" id="ftr-img" name="ftr-img" accept="img/png,img/jpg,img/jpeg" />
    </div>
    <div class="row">
      <div>
        <span>Draft</span>
        <input type="radio" name="status" value="draft" checked />
      </div>
      <div>
        <span>Publish</span>
        <input type="radio" name="status" value="published" />
      </div>
    </div>
    <button name="btn" class="btn">
      Post
    </button>
  </form>
</main>
<script src=""></script>
