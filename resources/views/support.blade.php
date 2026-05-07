<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support - Safe Drives</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- Font Awesome CSS (Optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
  <style>
    /* Center the content */
    body {
      background-color: #f8f9fa;
    }
    /* Styled box */
    .support-box {
      background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: auto;
    }
    /* Heading style */
    .support-box h2 {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 15px;
    }
    /* Input field styling */
    .support-box input[type="email"] {
    border: 2px solid #007bff;
    border-radius: 5px;
    padding: 10px;
    font-size: 16px;
    transition: border-color 0.3s;
    width: 94%;
    }
    .support-box input[type="email"]:focus {
      border-color: #0056b3;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    /* Button styling */
    .support-box button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 15px;
      font-size: 16px;
      border-radius: 5px;
      width: 100%;
      transition: background-color 0.3s, transform 0.2s;
      margin-top: 20px;
    }
    .support-box button:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }
    .support-box button:active {
      background-color: #004085;
    }
   .email{
        font-size: 30px;
    font-weight: 500;
    line-height: 28px;
    }
             /* Navbar Custom Theme */
             .navbar {
              height: 100%;
              max-height: 70px;
              box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
            /* background-color: #61CE70;  */
            border-radius: 5px;
        }

        .navbar-toggler {
            border-color: black;
            /* Lighter green border for toggler */
        }

		.logo {
      width: 65px;
    object-fit: cover;
    height: 45px;
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
  text-decoration:none;
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
  text-decoration:none;
  color: #f1f1f1;
}

.contact-details .fa {
  color: #f1f1f1;
  margin-right: 10px;
}

.sociallogos{
  padding:20px 0;
}

.sociallogos .logobox a{
  padding:0 10px;
  text-decoration:none;
  color:#ffffff;
  font-size:22px;
}

.com ul li{
  padding:5px 0;
}

@media only screen and (max-width: 749px) {
  .main-footer {
    padding:20px;
    display:grid;
    grid-template-columns: 1fr 1fr;
  }
    .info{
      padding:20px 0;
  }
}

@media (max-width: 480px) {
  .main-footer {
    grid-template-columns: 1fr;
  }
  .sociallogos{
    padding:20px 0;
  }
  .com{
    padding:20px 0;
  } 

}
ul{
    padding-left: 0;
}
@media (max-width: 576px) {
  .email {
    font-size: 25px !important;
    font-weight: 500 !important;
    line-height: 28px !important;
}
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

  <div class="support-box mt-5 mb-5">
    <h2>Support</h2>
      <div class="mb-3">
        <p class="email">support@sdrivesapp.com</p>
  </div>
</div>
@include('footer')





<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
