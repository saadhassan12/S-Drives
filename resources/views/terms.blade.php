<!--- Created by Mitchell Boland from spangle web design spangle.com.au -->
<!--- Send me a message there if you require a website, or would like some css help -->

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Terms Of Service</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
        integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Maven+Pro:400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
 <!-- Font Awesome CSS (Optional) -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
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
            color: #00843d !important;
            /* background-color: #61CE70;  */
            border-radius: 5px;
        }

        .navbar-toggler {
            border-color: black;
            /* Lighter green border for toggler */
        }

        .navbar-toggler-icon {
            /* background-color: #61CE70; */
            /* Lighter green toggler icon */
        }
        .logo {
            width: 65px;
    object-fit: cover;
    height: 45px;

        }
    html,
    body {
        margin: 0;
        padding: 0;
        font-family: 'Maven Pro', sans-serif;
    }

    /* remove the focus from all input fields.*/
    input:focus,
    select:focus,
    textarea:focus,
    button:focus {
        outline: none;
    }

    .termsAndConditions {
        z-index: 1000;
        border-radius: 3px;
        position: absolute;
        top: 50%;
        left: 50%;
        height: 100%;
        width: 100%;
        background-color: rgb(255, 255, 255);
        transform: translate(-50%, -50%);
        max-width: 900px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: all .2s;
        backface-visibility: hidden;
    }

    .termsAndConditionsHeading {
        margin-bottom: 15px;
    font-size: 35px;
    font-weight: 700;
    color: black;
    }

    .termsParagraphIntro {
        font-size: 16px;
        font-weight: 400;
        text-align: justify;
        line-height: 28px;
    }

    .spangleWelcome {
        font-weight: 500;
    font-size: 35px;
    color: black;
    }

    .serviceLeadingSection {
        width: 80%;
        margin-top: 15px;
        margin-bottom: 15px;

    }

    .sn {
        font-size: 32px;
        padding-right: 5px;
    }

    .st {
        font-size: 32px;
    }

    .spl {
        margin-top: 15px;
        margin-bottom: 15px;

    }

    .serviceInfoContainer {
        position: relative;
        width: 100%;
    }

    .serviceLead {
        font-weight: 700;
        margin-left: 15px;
        margin-top: 15px;
        margin-block-end: 5px;

    }

    .serviceDetails {
        margin-left: 15px;
        text-align: justify;
    }

    .displayNone {
        display: none;
    }

    .secionLine {
        height: 40px;
        width: 5px;

        position: absolute;
        top: 7px;
    }

    /*Font Colours below*/
    .lightGreen {
        color: black;
    font-size: 30px;
    font-weight: 600;
    }

    .blue {
        color: black;
    font-size: 30px;
    font-weight: 600;
    }

    .orange {
        color: black;
    font-size: 30px;
    font-weight: 600;
    }

    .purple {
        color: black;
    font-size: 30px;
    font-weight: 600;
    }

    /*Set the colours for the lines*/

    .lineColorGreen {
        background-image: linear-gradient(to right bottom, black, rgb(11, 103, 92));
    }

    .lineColorBlue {
        background-image: linear-gradient(to right bottom, black, rgb(11, 103, 92));
    }

    .lineColorOrange {
        background-image: linear-gradient(to right bottom, black, rgb(11, 103, 92));
    }

    .lineColorPurple {
        background-image: linear-gradient(to right bottom, black, rgb(11, 103, 92));
    }

    .closeTerms {
        border-bottom: 1px solid black;
        margin-top: 15px;
        margin-bottom: 15px;
        padding-bottom: 5px;
        font-size: 22px;
        font-weight: 500;
    }

    .fadeIn {
        animation-name: fadeIn;
        animation-duration: .8s;
        animation-timing-function: ease-out;
        z-index: 1001;
    }

    @keyframes fadeIn {
        0% {

            opacity: 0;
        }

        100% {

            opacity: 1;
        }
    }

                 /* Navbar Custom Theme */
         .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
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
.terms-wrapper{
    background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            margin: auto;
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

    <div class="terms-wrapper mt-5 mb-5">
         
        <h1 class="termsAndConditionsHeading">Terms Of Service</h1>
        <h4 class="spangleWelcome">Welcome to SDrives!</h4>
        <p class="termsParagraphIntro">
            These Terms of Use Terms govern your access to and use of the SDrives mobile app, websites, services,
            content, tools, and overall platform. By agreeing to these Terms or using the Platform, you establish a
            contractual relationship with us SDrives.
            Additional terms may apply to specific services, such as Courier or intercity transportation.
            For details about how we handle your data, please refer to our Privacy Policy.

        </p>
        <div class="serviceLeadingSection">
            <h4><span class="sn blue">1.</span><span class="st blue">Freedom for Drivers.</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Options for Riders Our Operating Model the Platform connects independent providers of transport,
                    courier services Drivers with users seeking those services Passengers. Passengers propose a price
                    for a ride or service, which Drivers can accept or counter with a different offer. Passengers can
                    choose from Drivers interested in their request. Once a Passenger confirms a ride, a separate
                    agreement is established between the Passenger and the Driver. Passengers pay Drivers the mutually
                    agreed price directly, covering all costs (e.g., tolls, taxes, and fees).
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives does not intervene or influence this financial arrangement.
                    SDrives as a Technology Platform SDrives serves as a technology company facilitating
                    connections between Drivers and Passengers. Users independently decide to offer or use services at
                    their own risk.

                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives does not control Drivers or their service provision. Any tools or processes provided aim
                    to enhance user experience and do not imply an employment or agency relationship.
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
        </div>

        <div class="serviceLeadingSection">
            <h4><span class="sn orange">2.</span><span class="st orange">Your SDrives Account</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Account Setup to use the Platform, you must create an account and be at least 18 years old (or the
                    legal age in your jurisdiction). You agree to provide accurate, up-to-date information and may need
                    to verify your identity
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Account Maintenance You are responsible for keeping your account secure and current. Report any
                    unauthorized activity immediately. Account Termination Accounts inactive for over three years may be
                    deleted. SDrives may retain some information for legal or security purposes.
                </p>
                <div class="secionLine lineColorOrange"></div>
            </div>

        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn lightGreen">3.</span><span class="st lightGreen">Your Safety</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives prioritizes user safety by verifying Driver credentials, conducting checks.
                    Contact local police or share trip details with a trusted contact during emergencies.

                </p>
                <div class="secionLine lineColorGreen"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple">4.</span><span class="st purple">Communication</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Between Drivers and Passengers and Drivers must maintain respectful, professional communication.
                    Misuse of contact information or failure to honor agreed terms is prohibited. SDrives may
                    investigate and take action in cases of harassment or non-compliance.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple">5.</span><span class="st purple">Indemnity</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    You agree to indemnify and hold SDrives, its affiliates, and representatives harmless from any
                    claims, liabilities, or expenses arising from:
                    Your use of the Platform or Services.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Your violation of these Terms, applicable laws. Disputes between Drivers and Passengers.

                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>

        <div class="serviceLeadingSection">
            <h4><span class="sn purple">6.</span><span class="st purple">Liability
                </span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Between Drivers and Passengers and Drivers must maintain respectful, professional communication.
                    Misuse of contact information or failure to honor agreed terms is prohibited. SDrives may
                    investigate and take action in cases of harassment or non-compliance.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple">7.</span><span class="st purple">Resolving Disputes

                </span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Disputes will first be resolved amicably. If unresolved, arbitration under the Pakistan LAW will
                    apply. Arbitration will be governed by Pakistan law.

                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple">8.</span><span class="st purple"> Updates to Terms or Platform</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives may update these Terms or Platform features periodically. Changes will be communicated
                    through app notifications or emails. Continued usage indicates agreement to updated Terms.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple">9.</span><span class="st purple">General Terms</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    General Terms Ensure your app is updated for compatibility. Links to third-party websites or
                    services are not with SDrives. These Terms do not affect your statutory rights.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>


        <h1 class="termsAndConditionsHeading">SDrives Courier Terms of Use</h1>
        <h4 class="spangleWelcome">Welcome to SDrives Courier!</h4>
        <p class="termsParagraphIntro">
            These terms are part of our overall Terms of Use. By using SDrives' Courier service, you're agreeing to
            follow both these terms and the general rules we’ve set out. If there’s any confusion or conflict between
            the two, these specific terms will take priority. This applies to SDrives’ apps, websites, products,
            services, and platform.
        </p>
        <p style="text-align: justify;">
            If you’re using the platform as a Courier, there might be extra terms you need to follow. To see how we
            handle your personal information, take a look at our Privacy Policy. By agreeing to these terms (or simply
            using the platform), you’re entering into an agreement with us.
        </p>
        <div class="serviceLeadingSection">
            <h4><span class="sn blue"></span><span class="st blue">SDrives Courier</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Senders can choose a Courier from a list of options. Once the Sender agrees to the terms, the
                    process moves forward.
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives is the platform that connects Couriers with Users (Senders). When a Sender wants to make
                    a delivery, they propose a price for the Courier’s service. The Courier has the choice to accept
                    that price or suggest something different.
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    The Sender will then pay the agreed-upon amount through the platform, which covers all costs
                    involved in the delivery like fees, taxes, tolls, and any other charges. SDrives doesn’t get
                    involved in the payment between the Sender and the Courier; that’s all handled directly between
                    them.
                    Users Responsibilities
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    The Sender will then pay the agreed-upon amount through the platform, which covers all costs
                    involved in the delivery like fees, taxes, tolls, and any other charges. SDrives doesn’t get
                    involved in the payment between the Sender and the Courier; that’s all handled directly between
                    them.
                    Users Responsibilities
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>

            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    SDrives does not control Drivers or their service provision. Any tools or processes provided aim
                    to enhance user experience and do not imply an employment or agency relationship.
                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Make sure the courier can easily access the delivery address, and either you or someone you trust
                    should be available to accept the parcel when it arrives. It’s on you to pack the parcel securely so
                    nothing gets damaged during transit. If anything’s delayed or damaged due to poor packing or wrong
                    delivery details, you’ll be held responsible.
                    You’re fully responsible for any legal issues that arise if you send items you’re not authorized to
                    send.

                </p>
                <div class="secionLine lineColorBlue"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <h4><span class="sn purple"></span><span class="st purple">Courier Responsibilities</span></h4>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    As a Courier, you are responsible for ensuring the Parcel is safely and promptly transported from
                    collection to delivery. It is your duty to take the necessary precautions to avoid any loss or
                    damage during transit. You could be held accountable for any damage or loss that occurs during
                    delivery, within any limitations of liability that apply.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Both the Sender and the Courier agree that their responsibilities are limited to what is outlined
                    above. Neither party will be held accountable for any delays or failures due to circumstances beyond
                    their control, such as natural disasters, government actions, or other force majeure events.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    If the delivery cannot be completed because the Recipient is unavailable or incorrect details were
                    provided by the Sender, SDrives will not be responsible for storing the Parcel. The Sender and
                    Recipient are responsible for making new arrangements for the Parcel’s handling.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Upon receiving the Parcel from the Sender, you may choose to
                    Carefully inspect its contents.
                    Ask the Sender to disclose the contents and properly seal the package.
                    Refuse to accept the Parcel if the Sender refuses to comply, or if the contents seem suspicious or
                    illegal.
                    If you suspect a breach of these Terms or the law, you should contact us at 
                    <a href="https://sdrivesapp.com/support">https://sdrivesapp.com/support</a>
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead">Restricted Goods</h6>
                <p class="serviceDetails">
                    You are responsible for making sure that the Parcel does not contain any restricted items.
                    Restricted Goods include, but are not limited to
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>

        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead">Parcels valued over PKR 10,000.</h6>
                <p class="serviceDetails">
                    You are responsible for making sure that the Parcel does not contain any restricted items.
                    Restricted Goods include, but are not limited to
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>

        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Narcotics, prescription drugs, psychotropic substances, explosives, hazardous chemicals, radioactive
                    materials, firearms, ammunition, fireworks, etc.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Foreign currency or banknotes.
                    High-value items like jewelry, precious metals, or gemstones.
                    Hazardous or perishable materials that could be harmful to others or the environment.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Animals, regulated species, or biological materials.
                    Items requiring special transport, such as food or liquids in unapproved containers.

                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Fragile items without proper packaging.
                    Shipments that are prohibited by law.
                    Illegal goods or counterfeit products.
                    Hazardous waste (e.g., batteries).
                    Alcoholic beverages, tobacco products, e-cigarettes, or obscene materials. For inquiries, please
                    contact us through our support page: 
                    <a href="https://sdrivesapp.com/support">https://sdrivesapp.com/support</a>
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
        </div>
        <h1 class="termsAndConditionsHeading">SDrives Inter City</h1>
        <div class="serviceLeadingSection">
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    These are the rules for using SDrives. Inter City services, including our app, website, and
                    platform. By using SDrives. Inter City you agree to follow these Terms. If anything doesn’t
                    match up, these Terms will take the lead. By accepting these Terms or jumping on the platform,
                    you’re officially teaming up with us
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    if you’re using SDrives as a Driver, there might be some extra rules just for you. And if you're
                    curious about how we handle your personal info, take a look at our Privacy Policy.
                </p>
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead">How SDrives. Inter City Works</h6>
                <p class="serviceDetails">
                    Connecting Drivers & Passengers: Our platform connects Drivers (people offering rides) with
                    Passengers (people needing rides) for trips between cities. When a Passenger requests a ride,
                    they’ll suggest a price. Drivers can either accept it or suggest a different one.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Choosing a Driver: Once a Passenger requests a ride, they can pick a Driver from the list. Once they
                    confirm, the deal made directly between both of you.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Payment: The Passenger pays the agreed price directly to the Driver through the platform. This price
                    covers everything tolls, taxes, and any extra fees. SDrives doesn't get involved in payments.
                    It’s all between you and the Driver.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Creating Ride Offers: Drivers can post their ride offers, with details like pick up time, location,
                    number of seats, and the price.
                    Agreeing to Offers: The Passenger can agree to the Driver’s offer and price. The Driver can accept
                    or decline the request.

                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead mt-5">Passenger Responsibilities</h6>
                <p class="serviceDetails">
                    Providing Accurate Details: As a Passenger it’s your job to provide the right details for your ride
                    like the correct address, street name, or house number. Or, just pick from available options.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead"></h6>
                <p class="serviceDetails">
                    Accountability for Mistakes: If you mess up with the details, causing problems, you’ll be
                    responsible for fixing it.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead">Driver Responsibilities</h6>
                <p class="serviceDetails">
                    Sticking to Your Area: Drivers, make sure to accept rides within your registered area. If you go
                    outside it, you're taking the risk of not following local rules.
                <div class="secionLine lineColorPurple"></div>
            </div>
            <div class="serviceInfoContainer">
                <h6 class="serviceLead">Driver Responsibilities</h6>
                <p class="serviceDetails">
                    Got any questions or need help? Just reach out to SDrives Support we’ve got your back!
                <div class="secionLine lineColorPurple"></div>
            </div>
     </div>
<!--  -->
<!-- <div class="container">
    <h3 class="mt-5">How Safe Drives. Inter City Works
    </h3>
    <div class="main-question">
        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Connecting Drivers & Passengers</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>Our platform connects Drivers (people offering rides) with Passengers (people needing rides) for
                    trips between cities. When a Passenger requests a ride, they’ll suggest a price. Drivers can
                    either accept it or suggest a different one.
                </p>
            </div>
        </div>

        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Choosing a Driver</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>Once a Passenger requests a ride, they can pick a Driver from the list. Once they confirm, the
                    deal made directly between both of you.
                </p>
            </div>
        </div>

        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Payment</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                The Passenger pays the agreed price directly to the Driver through the platform. This price covers
                everything tolls, taxes, and any extra fees. Safe Drives doesn't get involved in payments. It’s all
                between you and the Driver.
            </div>
        </div>

        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Creating Ride Offers</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>Drivers can post their ride offers, with details like pick up time, location, number of seats,
                    and the price.
                </p>
            </div>
        </div>

        <div class="item mb-5">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Agreeing to Offers</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>
                    The Passenger can agree to the Driver’s offer and price. The Driver can accept or decline the
                    request.
                </p>
            </div>
        </div>
        <h3>Passenger Responsibilities</h3>
        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Providing Accurate Details</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>
                    As a Passenger it’s your job to provide the right details for your ride like the correct
                    address, street name, or house number. Or, just pick from available options.
                </p>
            </div>
        </div>
        <div class="item mb-5">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Accountability for Mistakes</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>
                    If you mess up with the details, causing problems, you’ll be responsible for fixing it.
                </p>
            </div>
        </div>
        <h3>Driver Responsibilities </h3>
        <div class="item">
            <a href="#" onclick="toggleQuestion(this); return false;">
                <h4>Sticking to Your Area</h4>
                <svg viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7"></path>
                </svg>
            </a>
            <div>
                <p>
                    Drivers, make sure to accept rides within your registered area. If you go outside it, you're
                    taking the risk of not following local rules.
                </p>
            </div>
        </div>

        <h4 class="text-center mt-4">
            <strong>
                Got any questions or need help? Just reach out to Safe Drives Support we’ve got your back!
            </strong>
        </h4>
    </div>
    </div>
    <script>
        function toggleQuestion(element) {
            const item = element.closest('.item');
            item.classList.toggle('active');
        }
    </script> -->
        <h4 class="closeTerms">Contact Us For questions or support, visit our Support Contact Page.</h4>
    </div>

    @include('footer')

   <!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>