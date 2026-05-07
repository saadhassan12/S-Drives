<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Account</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
  <!-- Font Awesome CSS (Optional) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    /* navbar start */
    .navbar {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      height: 100%;
      max-height: 70px;

    }

    .navbar-brand {
      font-weight: 700;
      color: black !important;
      font-size: 30px;
    }

    .navbar-nav .nav-link {
      color: black !important;
      transition: color 0.3s, background-color 0.3s;
      font-size: 16px;
      font-weight: 500;
    }

    .navbar-nav .nav-link:hover {
      color: black !important;
      border-radius: 5px;
    }

    .navbar-toggler {
      border-color: black;
    }

    .logo {
      width: 65px;
      object-fit: cover;
      height: 45px;

    }

    /* navend */
    body {
      background-color: #f8f9fa;
      height: 100vh;
      margin: 0;
    }

    .verification-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      padding: 40px;
      width: 100%;
      max-width: 500px;
      margin: auto;
    }

    .input-group,
    #phoneNumber {
      width: 100%;
      max-width: 400px;
      border: 1px solid #ebeced;
      border-radius: 4px;
      background-color: #ebeced;
      min-height: 48px;
    }

    .form-control {
      border-radius: 0.25rem;
      max-width: 100%;
      border: none !important;
      box-shadow: none !important;
      outline: none !important;
    }

    #getCodeBtn {
      background-color: #007bff;
      border: none;
      padding: 10px 15px;
      font-size: 16px;
      border-radius: 5px;
      color: black;
      font-weight: 700;
      cursor: pointer;
      transition: 0.3s ease;
      cursor: pointer;
    }

    #getCodeBtn:disabled {
      background-color: #d6d6d6;
      /* cursor: not-allowed; */
    }

    label {
      font-size: 14px;
      font-weight: 500;

    }

    .del-account {
      font-size: 28px;
      font-weight: 600;
      line-height: 36px;
      margin-bottom: 20px;
    }

    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


    .main-footer {
      padding: 70px 0;
      display: flex;
      justify-content: space-evenly;
      background-color: black;
    }

    .main-footer ul {
      list-style: none;
    }

    .main-footer h1 {
      font-size: 22px;
      line-height: 117%;
      color: #ffffff;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .main-footer h2 {
      color: #ffffff;
      font-weight: 500;
    }

    .main-footer ul li a {
      color: #ffffffcc;
      text-decoration: none;
    }

    footer {
      background-color: #262b2f;
      border-top: 1px solid #6EB981;
      font-size: 17px;
      padding: 15px 5px;
      color: #ffffff;
      text-align: center;
    }

    footer a {
      text-decoration: none;
      color: #ffffff;
    }

    .logoinfo p {
      color: #6EB981;
      font-size: 17px;
      margin-top: 5px;
    }

    .contact-details {
      margin-top: 20px;
    }

    .contact-details li {
      list-style: none;
      margin: 10px 0;
    }

    .contact-details li a {
      text-decoration: none;
      color: #f1f1f1;
    }

    .contact-details .fa {
      color: #f1f1f1;
      margin-right: 10px;
    }

    .sociallogos {
      padding: 20px 0;
    }

    .sociallogos .logobox a {
      padding: 0 10px;
      text-decoration: none;
      color: #ffffff;
      font-size: 22px;
    }

    .com ul li {
      padding: 5px 0;
    }

    @media only screen and (max-width: 749px) {
      .main-footer {
        padding: 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
      }

      .info {
        padding: 20px 0;
      }
    }

    @media (max-width: 480px) {
      .main-footer {
        grid-template-columns: 1fr;
      }

      .sociallogos {
        padding: 20px 0;
      }

      .com {
        padding: 20px 0;
      }

    }

    ul {
      padding-left: 0;
    }

    @media (max-width: 576px) {
      .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        height: auto !important;
        max-height: none !important;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  @include('nav')



  <div class="verification-container mt-5 mb-5">
    <h5 class="del-account">To delete the account, verify your number</h5>
    <form method="POST" action="{{ route('delete.account') }}">
    @csrf 
      <label for="phoneNumber">Phone Number</label>
      <div class="input-group">
        <input type="tel" id="phoneNumber" class="form-control" placeholder="Enter your number +92311111111" required name="phone_number">
      </div>
      <button type="submit" class="btn btn-primary mt-5" disabled id="getCodeBtn">Delete Account</button>
    </form>
  </div>



  @include('footer')




  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    // Function to add the country code '+92' to the phone number
    function addCountryCode() {
      var phoneInput = document.getElementById('phoneNumber');
      var phoneNumber = phoneInput.value;

      // Check if the phone number already has the +92 country code
      if (!phoneNumber.startsWith("+92")) {
        phoneInput.value = "+92" + phoneNumber;
      }
    }
    const phoneInputField = document.querySelector("#phoneNumber");
    const phoneInput = window.intlTelInput(phoneInputField, {
      initialCountry: "pk",
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    document.getElementById('phoneNumber').addEventListener('input', function() {
      const button = document.getElementById('getCodeBtn');
      const error = document.querySelector('.text-danger');

      if (!phoneInput.isValidNumber()) {
        button.disabled = true;
        error.classList.remove('d-none');
      } else {
        button.disabled = false;
        error.classList.add('d-none');
      }
    });
  </script>
</body>

</html>