<div class="user-form">
  <h2><?= $title ?></h2>
  <form action="/users/auth" method="post">
    <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
    <div class="col">
      <label for="email">
        Email
        <span class="text-red">*</span>
      </label>
      <input type="email" name="email" />
    </div>
    <div class="col">
      <label for="password">
        Password
        <span class="text-red">*</span>
      </label>
      <input type="password" name="password" />
    </div>
    <button name="btn" class="btn">
      Signin
    </button>
  </form>
</div>
