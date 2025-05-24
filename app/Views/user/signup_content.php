<div class="user-form">
  <h2><?= $title ?></h2>
  <form action="/users" method="post">
    <input type="hidden" name="_token" value="<?= $csrf_token ?>" />
    <div class="col">
      <label for="fname">
        First Name
        <span class="text-red">*</span>
      </label>
      <input id="fname" name="fname" />
    </div>
    <div class="col">
      <label for="lname">
        Last Name
        <span class="text-red">*</span>
      </label>
      <input id="lname" name="lname" />
    </div>
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
      Signup
    </button>
  </form>
</div>
