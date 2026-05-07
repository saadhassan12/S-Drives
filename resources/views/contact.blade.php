<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
   <!-- Font Awesome CSS (Optional) -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  
    <!-- Ionicons CSS (Using latest version) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css" integrity="sha512-XXXXX" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>
<style>
    * {
  font-family: "Poppins", sans-serif;
}
body {
  background-image: linear-gradient(
    to left bottom,
    #051937,
    #05162f,
    #051327,
    #040f1f,
    #010a18
  );

  background-size: 800%;
  animation: animateClr 1s infinite cubic-bezier(0.62, 0.28, 0.23, 0.99);
}
input[type="text"],
input[type="email"],
input[type="tel"],
textarea {
  border: none;
  border-bottom: 2px solid rgb(128, 126, 126);
  background: transparent;
  outline: none;
  width: 100%;
  text-transform: capitalize;
  padding: 1rem 0.4rem;
}
.aside {
  background-image: linear-gradient(to left bottom, #051937, #000000, #010102, #0e0f12, #06080a);
  animation: animateClr 5s infinite cubic-bezier(0.62, 0.28, 0.23, 0.99);
  background-size: 400%;
}

@keyframes animateClr {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

ion-icon:not([name="logo-codepen"]) {
  border: 1px solid currentColor;
  border-radius: 20%;
  padding: 10px 10px;
}

         /* Navbar Custom Theme */
         .navbar {
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            height: 100%;
            max-height: 70px;
        }

        .navbar-brand {
            font-weight: 700;
            color: #61CE70 !important;
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
            border-color: #61CE70;
            /* Lighter green border for toggler */
        }

        .navbar-toggler-icon {
          filter: invert(1); /* White color */
            background-color: #61CE70;
            /* Lighter green toggler icon */
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
  .navbar {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    height: auto !important;
    max-height: none !important;
}
}
</style>
<body>
      <!-- Navbar -->
      @include('nav')

    <div class="container mt-5 mb-5">
        <div class="bg-light">
          <div class="row">
            <div class="col-lg-8 col-md-12 p-5 bg-white rounded-3">
              <!-- <div class="d-flex mb-3 flex-column">
                <h1 class="h5 text-capitalize my-4">What service You need ?</h1>
                <div class="d-flex flex-wrap">
                  <div class="
                          d-flex
                          flex-wrap
                          justify-content-center
                          align-items-center
                          me-4
                        ">
                    <input type="checkbox" name="webdev" class="form-check-input m-0 me-3" id="webdev" />
                    <label for="webdev"> Web Development</label>
                  </div>
                  <div class="
                          d-flex
                          flex-wrap
                          justify-content-center
                          align-items-center
                          me-4
                        ">
                    <input type="checkbox" name="webdes" class="form-check-input m-0 me-3" id="webdes" />
                    <label for="webdes"> Web Design</label>
                  </div>
                  <div class="
                          d-flex
                          flex-wrap
                          justify-content-center
                          align-items-center
                          me-4
                        ">
                    <input type="checkbox" name="logodes" class="form-check-input m-0 me-3" id="logodes" />
                    <label for="logodes"> Logo Design</label>
                  </div>
                  <div class="
                          d-flex
                          flex-wrap
                          justify-content-center
                          align-items-center
                          me-4
                        ">
                    <input type="checkbox" name="others" class="form-check-input m-0 me-3" id="others" />
                    <label for="others"> Others </label>
                  </div>
                </div>
              </div> -->
              <form class="row mb-3">
                <div class="col-md-6 p-3">
                  <input required placeholder="first name" type="text" name="" id="" />
                </div>
                <div class="col-md-6 p-3">
                  <input required placeholder="last name" type="text" name="" id="" />
                </div>
                <div class="col-md-6 p-3">
                  <input required placeholder="E-mail" type="email" name="" id="" />
                </div>
                <div class="col-md-6 p-3">
                  <input required placeholder="phone" type="tel" name="" id="" />
                </div>
                <div class="col-md">
                  <textarea required name="" placeholder="write your message" id="" cols="30" rows="1"></textarea>
                </div>
                <div class="text-end mt-4">
                  <input class="btn px-4 py-3 btn-outline-dark" type="submit" value="Send Message" />
                </div>
              </form>
            </div>
            <div class="col-lg-4 col-md-12 text-white aside px-4 py-5">
              <div class="mb-5">
                <h1 class="h3">Contact Information</h1>
                <p class="opacity-50">
                  <small>
                    Fill out the from and we will get back to you whitin 24 hours
                  </small>
                </p>
              </div>
              <div class="d-flex flex-column px-0">
                <ul class="m-0 p-0">
                  <li class="d-flex justify-content-start align-items-center mb-4">
                    <span class="opacity-50 d-flex align-items-center me-3 fs-2">
                      <ion-icon name="call-outline"></ion-icon>
                    </span>
                    <span>+9329-5587824</span>
                  </li>
                  <li class="d-flex align-items-center r mb-4">
                    <span class="opacity-50 d-flex align-items-center me-3 fs-2">
                      <ion-icon name="mail"></ion-icon>
                    </span>
                    <span>info@sdrivesapp.com</span>
                  </li>
                  <li class="d-flex justify-content-start align-items-center mb-4">
                    <span class="opacity-50 d-flex align-items-center me-3 fs-2">
                      <ion-icon name="pin"></ion-icon>
                    </span>
                    <span>Lahore,Pakistan</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      
      @include('footer')




  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Example using CDN for Ionicons v5.5.2 -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>