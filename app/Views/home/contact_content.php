<h1 class="mb-4"><?= $title ?></h1>

<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-body">
        <form action="/contact/submit" method="POST">
          <!-- CSRF Protection -->
          <input type="hidden" name="_token" value="<?= $csrf_token ?>">

          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Contact Information</h5>
        <p class="card-text">
          <strong>Address:</strong> 123 MVC Street, PHP City<br>
          <strong>Email:</strong> info@simplemvc.com<br>
          <strong>Phone:</strong> (123) 456-7890
        </p>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Business Hours</h5>
        <p class="card-text">
          <strong>Monday - Friday:</strong> 9AM - 5PM<br>
          <strong>Saturday:</strong> 10AM - 2PM<br>
          <strong>Sunday:</strong> Closed
        </p>
      </div>
    </div>
  </div>
</div>
