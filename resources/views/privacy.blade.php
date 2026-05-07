<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Privacy Policy</title>
	 <!-- Font Awesome CSS (Optional) -->
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
	   <!-- Bootstrap CSS -->
	   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
	<style>
		  /* Navbar Custom Theme */
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
		body {
			font-family: Arial, sans-serif;
			background-color: #f9f9f9;
		}

		.privacy-policy-container {
			padding: 2rem;
			background: white;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			max-width: 780px;
			margin: auto;
		}

		.privacy-policy-container h1 {
			color: black;
			margin-bottom: 1.5rem;
			text-align: center;
		    font-size: 35px;
            font-weight: 700;
		}

		.privacy-policy-container h2 {
			position: relative;
			padding-left: 1rem;
			margin-top: 2rem;
			color: #333;
			font-size: 32px;
			font-weight: 500;
		}

		.privacy-policy-container h2::before {
			content: "";
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 5px;
			height: 100%;
			background-color: black;
		}

		.privacy-policy-container p {
			line-height: 28px;
			color: #555;
			font-size: 16px;
			font-weight: 400;

		}

		.privacy-policy-container ul {
			margin: 1rem 0;
			padding-left: 1.5rem;
		}

		.privacy-policy-container ul li {
			margin-bottom: 0.5rem;
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
	.privacy-policy-container h1 {
			color: black !important;
			text-align: center !important;
		    font-size: 22px !important;
            font-weight: 700 !important;
		}
		.privacy-policy-container p {
			font-size: 14px !important;
			font-weight: 400 !important;
		}
		.privacy-policy-container h2{
			font-size: 22px !important;
            font-weight: 500 !important;
		}
		.privacy-policy-container ul li{
			font-size: 14px !important;
			font-weight: 400 !important;
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
@include('nav')

	<div class="privacy-policy-container mt-5 mb-5">
		<h1>SDrives Privacy Policy</h1>
		<p>This document outlines how we collect, use, and protect your personal information when you use SDrives'
			services.
		</p>
		<h2>Operator Information</h2>
		<p>S'Drives operates and is committed to protecting the privacy of its users in accordance with local laws
			and global privacy standards.
		</p>
		<h2>Scope of This Policy</h2>
		<p>
			This policy applies to all interactions with S'Drives through our website, mobile app, and related
			services. It does not apply to data collected about employees, job applicants, or business partners.
		</p>
		<p>
			If any part of this policy conflicts with applicable laws, we will comply with legal requirements to the
			extent necessary to address the conflict.
		</p>
		<h2>In this policy</h2>
		<p>
			Passengers refer to users requesting transportation services.
			Drivers refer to individuals providing transportation services through S'Drives.
		</p>
		<ul>
			<li class="mb-3">Information We Collect</li>
			<li class="mb-3">S'Drives collects the following categories of information</li>
			<li class="mb-3">Information provided directly by users.</li>
			<li class="mb-3">Information collected automatically when interacting with our services.
				We do not collect sensitive personal information such as political affiliations, religious beliefs, or
				sexual orientation.
			</li>
		</ul>
		<ul>
			<li>Data You Provide</li>
			<li>Passengers</li>
			<li>During registration, we may collect</li>
			<li>Name</li>
			<li>Phone number</li>
			<li>Email address</li>
			<li>City and country</li>
			<li>Optional profile picture</li>
			<p>In some cases, we may request a selfie for identity verification or national ID details for enhanced
				security and compliance.
			</p>
		</ul>
		<ul>
			<li> Drivers</li>
			<p>When registering as a Driver, we require</p>
			<li>Full name</li>
			<li>Profile picture</li>
			<li>Vehicle details (make, model, year)</li>
			<li>License information</li>
			<li>Proof of identity and address</li>
			<p>Additional data, such as background checks or driving records, may be required under local laws. This
				information is essential to ensure eligibility and compliance.
			</p>
		</ul>
		<!-- <h2>Communication Data</h2> -->
		<!-- <p>
			We may collect chat messages, phone calls, or correspondence related to customer support or disputes.
		</p> -->
		<h2>Automatically Collected Information</h2>
		<ul>
			<li>Location Data</li>
			<li>Passengers</li>
			<p>Location is collected during rides when permissions are enabled. Alternatively, pick-up and drop off
				details can be entered manually.
			</p>
			<li>Drivers</li>
			<p> Location data is collected while the app is in use, including a limited period after exiting Drivers
				mode to provide support.
			</p>
			<li>Fare Data</li>
			<p>We collect trip related details, including</p>
			<li>Routes</li>
			<li>Distances</li>
			<li>Fares</li>
			<li>Device Information</li>
			<p>
				We may collect device details, such as language settings and sensor data.
			</p>
			<li>Usage Data</li>
			<p>Data on app interactions, app performance may be collected to improve user experience.</p>
		</ul>
		<h2>Data from Other Sources</h2>
		<p>We may receive additional information about users from</p>
		<ul>
			<li>Background check providers</li>
			<p>Feedback or referrals from other users
			</p>
		</ul>
		<h2>Your Choices</h2>
		<p>
			Updating Information: You can modify personal details through the app.
			Location Settings: Location sharing can be controlled via your device’s settings.
		</p>
		<h2>How We Use Your Data
		</h2>
		<p>We use the data collected to</p>
		<ul>
			<li>
				Facilitate the functionality of the S'Drives app.

			</li>
			<li>Verify the eligibility and compliance of Drivers.
			</li>
			<li>Prevent fraud and enforce policies.
			</li>
			<li>Provide customer support and resolve disputes.
			</li>
			<li>Send notifications, including trip receipts and invoices.
			</li>
			<li>Enhance app features based on user feedback.
			</li>
			<li>Fulfill legal obligations and protect user rights.
			</li>
			<p>Automated decision-making processes, such as matching Passengers with Drivers and identifying fraudulent
				activities, are implemented to improve efficiency.
			</p>
		</ul>
		<h2>Sharing Your Information
		</h2>
		<p>S'Drives may share your data under specific circumstances:
		</p>
		<ul>
			<li>With Other Users</li>
			<p>Passengers may view Driver details such as name, profile picture, and vehicle information. Drivers may
				view Passenger details necessary for trip coordination.
			</p>
			<li>With S'Drives Affiliates</li>
			<p> Data may be shared across affiliated entities to enhance services and ensure compliance.
			</p>
			<li>With Authorities</li>
			<p>Information may be shared with law enforcement or regulatory bodies if required by law.
				<a href="https://web.sdrivesapp.com/support">https://web.sdrivesapp.com/support</a>
			</p>
		</ul>
	</div>


    @include('footer')

	    <!-- Bootstrap Icons -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>