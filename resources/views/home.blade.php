<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <!-- Font Awesome CSS (Optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
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
            border-color: black;
            /* Lighter green border for toggler */
        }

        .safe-content {
            font-size: 35px;
            font-weight: 700;
            text-align: center;
            color: black;
            line-height: 40px;
        }

        .safe-content-text {
            font-size: 20px;
            font-weight: 500;
            text-align: center;
            color: #72bf44;
        }

        .banner-wrapper {
            width: 100%;
        }

        .banner-img {
            width: 100%;
            height: 100%;
        }

        .drive-safe {
            font-size: 35px;
            font-weight: 700;
        }

        .why {
            font-size: 38px;
            font-weight: 600;

        }

        .safe-drive-main {
            font-size: 16px;
            font-weight: 400;
            line-height: 28px;
        }

        .card-section {
            padding: 75px 0;
        }

        .custom-card {
            border: none;
            background-color: #fff;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            min-height: 265px;
        }

        .custom-card:hover,
        .custom-card:focus {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .custom-card:hover::after,
        .custom-card:focus::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: black;
            /* Green border on hover */
        }

        .custom-card img {
            max-width: 80px;
            margin-bottom: 15px;
        }

        .custom-card .number {
            color: #61CE70;
            /* Secondary green */
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: start;
        }

        .custom-card h5 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .custom-card p {
            font-size: 16px;
            color: #666;
        }

        .benfit-wrapper {
            background-color: black;
            padding: 60px 0px;
        }

        .benfit-text {
            font-size: 35px;
            margin-bottom: 18px;
            text-transform: none;
            color: #fff;
            font-weight: 700;
            line-height: 40px;
        }

        span {
            color: white;
            font-weight: 700;
        }

        .benfit-content {
            font-size: 20px;
            font-weight: 400;
            color: #fff;
        }

        .benfit-safe {
            font-size: 20px;
            font-weight: 600;
            color: #72bf44;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .benefit-item .icon {
            font-size: 25px;
            color: white;
            margin-right: 15px;
        }

        .benefit-item h5 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff
        }

        .benefit-item p {
            color: #bbb;
            font-size: 15px;
            font-weight: 400;
            line-height: 22px;
        }

        .benefit-item a {
            color: #bbb;
            text-decoration: none;

        }

        .safe-a {
            color: #72bf44;
            text-decoration: none;
            font-weight: 600;
        }

        .benefit-item a:hover {
            text-decoration: underline;
        }

        .men-lptop {
            transition: all 0.3s ease-out;
            width: 100%;
            height: 100%;
            object-fit: contain;
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
            color: white;
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
            .safe-content {
                font-size: 22px !important;
                font-weight: 700 !important;
                text-align: center !important;
                color: black !important;
                line-height: 28px !important;
            }

            .drive-safe {
                font-size: 22px !important;
                font-weight: 700 !important;
            }

            .why {
                font-size: 22px !important;
                font-weight: 700 !important;
            }

            .safe-drive-main {
                font-size: 14px !important;
            }

            .text-left-wrapper {
                padding-left: 20px !important;
            }

            .men-lptop {
                margin-bottom: 30px !important;
            }

            .benfit-text {
                font-size: 22px !important;
                font-weight: 700 !important;
                text-align: center !important;
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

    <!-- home page  -->
    <h2 class="safe-content mt-5 mb-5">SDrives: Your Trusted Online <br>Driver Safety Partner</h2>

    <!--  banner -->
    <div class="banner-wrapper">
        <img class="banner-img" src="./images/banr.jpg" alt="">
    </div>
    <div class="container mt-5 mb-5">
        <h3 class="drive-safe"><strong class="why">S</strong>Drive's</h3>
        <p class="safe-drive-main mb-3">
            S'Drives Ride Hailing Service exclusively for women individuals. It’s not just about getting from one place to another it’s about creating a safe, comfortable, and empowering experience for both passengers and drivers.
        </p>
        <p class="safe-drive-main mb-3">
            With S'Drives, you can finally say goodbye to the worries of commuting. Our drivers are all women individuals, carefully vetted to ensure your safety and comfort. Whether it’s a daily commute, a trip to the market, or a late night ride, we’re here to make every journey stress free and enjoyable.
        </p>
    </div>

    <div class="benfit-wrapper mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img class="men-lptop" src="./images/mbl.jpg" alt="man-laptop">
                </div>
                <div class="col-md-5 text-left-wrapper" style="padding-left: 120px;">
                    <h2 class="benfit-text">Here’s why people choose
                        <span>S</span>Drive
                    </h2>

                    <!-- <p class="benfit-content">
                        Save money, save time and save lives (including your own) with Drive <span
                            class="benfit-safe">Safe</span>.com
                    </p> -->

                    <div class="benefit-item">
                        <span class="icon">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <div>
                            <h5>Safe & Secure</h5>
                            <p>
                                Every ride is GPS-tracked, and you can share your trip details with loved ones for added peace of mind.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </span>
                        <div>
                            <h5>Affordable</h5>
                            <p>
                                No surprises, just fair pricing that works for everyone.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="icon">
                            <i class="bi bi-globe"></i>
                        </span>
                        <div>
                            <h5>Empowering Communities</h5>
                            <p>
                                Every ride supports our mission to create opportunities for women and transgender individuals in Pakistan.
                            </p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="icon">
                            <i class="bi bi-person-check"></i>
                        </span>
                        <div>
                            <h5>SDrives is all about</h5>
                            <p>
                                Your journey, your comfort, your safety that’s what S'Drives is all about. Download the app now and experience the difference!
                            </p>
                        </div>
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
</body>

</html>