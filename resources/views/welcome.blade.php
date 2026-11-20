<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dar es Salaam International Trade Fair</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Custom Styles */
body {
  font-family: Arial, sans-serif;
}

section {
  padding: 60px 0;
}

#home {
  background-color: #f8f9fa;
}

#register, #booking {
  background-color: #fff;
}

footer {
  background-color: #343a40;
}
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">Trade Fair Portal</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#home">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#register">Visitor Registration</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#booking">Space Booking</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Home Section -->
  <section id="home" class="py-5 text-center bg-light">
    <div class="container">
      <h1>Welcome to the Dar es Salaam International Trade Fair</h1>
      <p>Register as a visitor or book a space for your business.</p>
    </div>
  </section>

  <!-- Visitor Registration Form -->
  <section id="register" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Visitor Registration</h2>
      <form id="visitorForm">
        <div class="mb-3">
          <label for="fullName" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="fullName" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" id="phone" required>
        </div>
        <div class="mb-3">
          <label for="company" class="form-label">Company Name (Optional)</label>
          <input type="text" class="form-control" id="company">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
      </form>
    </div>
  </section>

  <!-- Space Booking Form -->
  <section id="booking" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-4">Space Booking</h2>
      <form id="bookingForm">
        <div class="mb-3">
          <label for="businessName" class="form-label">Business Name</label>
          <input type="text" class="form-control" id="businessName" required>
        </div>
        <div class="mb-3">
          <label for="spaceSize" class="form-label">Space Size (sqm)</label>
          <input type="number" class="form-control" id="spaceSize" required>
        </div>
        <div class="mb-3">
          <label for="bookingDate" class="form-label">Preferred Date</label>
          <input type="date" class="form-control" id="bookingDate" required>
        </div>
        <div class="mb-3">
          <label for="additionalInfo" class="form-label">Additional Information</label>
          <textarea class="form-control" id="additionalInfo" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Book Space</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3">
    <div class="container">
      <p>&copy; 2023 Dar es Salaam International Trade Fair. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="scripts.js"></script>
</body>
</html>
<script>
    $(document).ready(function () {
  // Handle Visitor Registration Form Submission
  $("#visitorForm").submit(function (event) {
    event.preventDefault();
    const fullName = $("#fullName").val();
    const email = $("#email").val();
    const phone = $("#phone").val();
    const company = $("#company").val();

    alert(`Thank you, ${fullName}! Your registration has been received.`);
    $("#visitorForm")[0].reset();
  });

  // Handle Space Booking Form Submission
  $("#bookingForm").submit(function (event) {
    event.preventDefault();
    const businessName = $("#businessName").val();
    const spaceSize = $("#spaceSize").val();
    const bookingDate = $("#bookingDate").val();
    const additionalInfo = $("#additionalInfo").val();

    alert(`Thank you, ${businessName}! Your booking request has been received.`);
    $("#bookingForm")[0].reset();
  });
});
</script>