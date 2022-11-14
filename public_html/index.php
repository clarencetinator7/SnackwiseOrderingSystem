<?php

require_once dirname(__FILE__) . '/php/classes/Account.php';
require_once dirname(__FILE__) . '/php/classes/DbConnection.php';
require_once dirname(__FILE__) . '/php/classes/Validate.php';

$validate = new Validate();

$account = new Account();

/* deletes expired verification code */
$account->delete_code();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Snackwise</title>

    <!-- PAGE ICON -->
    <link rel="icon" href="img/penguin.png" type="image/icon type">

    <!-- FONT LINKS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Poppins:ital,wght@0,300;0,600;0,700;1,400&family=Roboto:ital,wght@0,300;0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- BOOTSTRAP JS  -->


    <!-- FONT AWESOME -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js"></script>

    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/notification.css">

    <!-- DATE PICKER -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<body>

    <!-- SIDEBAR -->

    <div class="sidecart  flex-column h-100 top-0  " id="sidecart">

        <div class="mx-3 mt-3">
            <p class="cart-label bold mb-0"> SHOPPING CART <i class=" sidecart-close fa-solid fa-xmark float-end" onclick="close_cart();"></i></p>
            <hr class="w-100 my-2">
        </div>

        <div class="cart_list flex-grow-1 mx-3">


            <div class="cart_item d-flex align-items-center my-3 mx-1 p-1 ">
                <i class="item-remove fa-solid fa-xmark"></i>
                <img class=" cart-image mx-2 " src="img/menu-imgs/A.jpg" alt="">
                <div class="d-flex flex-column">
                    <div class="item-name">Combo A</div>
                    <div class=""><span class="item-quantity">1x</span><span class="ms-3 bolder item-price">75.00 ₱</span></div>
                </div>
            </div>


            <div class="cart_item d-flex align-items-center my-3 mx-1 p-1 ">
                <i class="item-remove fa-solid fa-xmark"></i>
                <img class=" cart-image mx-2 " src="img/menu-imgs/B.jpg" alt="Combo B">
                <div class="d-flex flex-column">
                    <div class="item-name">Combo B</div>
                    <div class=""><span class="item-quantity">1x</span><span class="ms-3 bolder item-price">89.00 ₱</span></div>
                </div>
            </div>





        </div>

        <div class="cart_price_information d-flex flex-column mx-3 mb-3">
            <hr class="w-100 my-2">
            <div class=" bold">SUBTOTAL:</div>
            <div class="text-end bolder">164.00 ₱</div>

            <button type="button" class="btn btn-checkout w-100 my-2">CHECKOUT</button>
            <div type="button" class="btn btn-clear w-100">Clear Cart</div>
        </div>
    </div>

    <!-- SIDEBAR -->

    <div class="parent-container">
        <div class="top-wrapper">

            <nav class="navbar navbar-light bg-light navbar-expand-md">
                <div class="container">
                    <a href="index.php" class="navbar-brand">
                        <!-- <img src="./img/penguin.png" alt="Penguin Logo" height="58" width="52"> -->
                        <span class="red">SNACK</span><span class="yellow">WISE</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto nav-list">
                            <li class="nav-item">
                                <a href="index.php" class="nav-link" id="active">Home</a>
                            </li>
                            <li class="nav-item">
                                <a href="menu.php" class="nav-link">Menu</a>
                            </li>
                            <li class="nav-item">
                                <a href="contactus.php" class="nav-link">Contact Us</a>
                            </li>
                        </ul>
                        <?php
                        if ($validate->is_logged_in("customer")) {
                        ?>
                            <form action="#" class="form-inline sign-btns">
                                <a name="log-in-btn" class="btn" href="account/login.php">Login</a>
                                <a name="sign-up-btn" id="" class="btn btn-primary" href="account/register.php" role="button">Sign Up</a>
                            </form>
                        <?php
                        } else {
                            /* dito lalagay yung logout*/
                        ?>
                            <form action="#" class="form-inline sign-btns">

                                <a name="sign-up-btn" class="btn btn-primary" href="account/logout.php">Logout</a>
                            </form>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </nav>

            <section class="hero container">
                <div class="home-header">
                    <span class="sub-header">Wise meal for a wise budget.</span>
                    <h1 class="">Try our <span class="yellow">better</span> and <span class="red">cheaper</span> snacks experience</h1>
                    <a href="account/register.php" class="btn cta-btn">Sign Up to Order
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.43 5.93005L20.5 12.0001L14.43 18.0701M3.5 12.0001H20.33" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
                <div class="home-header-picture">
                    <img src="img/hero-img-wblob.png" alt="burger, fries, and hotdog">
                </div>
            </section>

        </div>

        <section class="container featured-products">
            <div class="fp-header">
                <h2>Best Sellers</h2>
                <a href="menu.php">View all
                    <svg width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.93 1L18 7.07L11.93 13.14M1 7.07H17.83" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div class="products-container container">

                <!-- bestseller items will be appended here -->
                <div class="bestseller_list row justify-content-start" id="bestseller_list">

                    <!-- <div class="col-12 col-md-3 product">
                        <div class="product-img-container">
                            <img src="img/menu-imgs/B.jpg" alt="combo a image" class="product-img">
                        </div>
                        <div class="product-details-container">
                            <div class="product-caption">
                                <span class="product-name">Combo B</span>
                                <span class="product-description">Includes: Regular Burger, Carbonara, Blue Lemonade</span>
                            </div>
                            <div class="cart-container">
                                <span class="product-price">PHP 89.00</span>
                                <span class="add-to-cart-container">
                                    <button class="add-to-cart-btn" type="submit" onclick="open_cart();">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div> 

                    <div class="col-12 col-md-3 product">
                        <div class="product-img-container">
                            <img src="img/menu-imgs/C.jpg" alt="combo a image" class="product-img">
                        </div>
                        <div class="product-details-container">
                            <div class="product-caption">
                                <span class="product-name">Combo C</span>
                                <span class="product-description">Includes: Regular Hotdog, Regular Fries, Blue Lemonade</span>
                            </div>
                            <div class="cart-container">
                                <span class="product-price">PHP 80.00</span>
                                <span class="add-to-cart-container">
                                    <button class="add-to-cart-btn" type="submit" onclick="open_cart();">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3 product">
                        <div class="product-img-container">
                            <img src="img/menu-imgs/D.jpg" alt="combo a image" class="product-img">
                        </div>
                        <div class="product-details-container">
                            <div class="product-caption">
                                <span class="product-name">Combo D</span>
                                <span class="product-description">Includes: Regular Fries, Carbonara, Blue Lemonade</span>
                            </div>
                            <div class="cart-container">
                                <span class="product-price">PHP 85.00</span>
                                <span class="add-to-cart-container">
                                    <button class="add-to-cart-btn" type="submit" onclick="open_cart();">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    -->


                </div>
        </section>

        <section class="how-to-container">
            <div class="container">
                <div class="row">
                    <div class="col how-to-header">
                        <h2 class="text-center">How It Works?</h2>
                    </div>
                </div>
                <div class="row instructions-container">
                    <div class="col col-md-3 instruction">
                        <div class="instruction-illustration-container">
                            <object data="img/instruction-icons/create-account-icon.svg"></object>
                        </div>
                        <div class="instruction-caption">
                            <span class="instruction-name">Create
                                Account</span>
                            <span class="instruction-details">Before you can order,
                                you must create an
                                account first.</span>
                        </div>
                    </div>

                    <div class="col col-md-3 instruction">
                        <div class="instruction-illustration-container">
                            <object data="img/instruction-icons/order-icon.svg"></object>
                        </div>
                        <div class="instruction-caption">
                            <span class="instruction-name">Order</span>
                            <span class="instruction-details">
                                Browse through our menu, and place your order
                            </span>
                        </div>
                    </div>

                    <div class="col col-md-3 instruction">
                        <div class="instruction-illustration-container">
                            <object data="img/instruction-icons/meal-preparation-icon.svg"></object>
                        </div>
                        <div class="instruction-caption">
                            <span class="instruction-name">Meal Preparation</span>
                            <span class="instruction-details">
                                It may took a while when we prepare your order. Chill out it will be worth it.
                            </span>
                        </div>
                    </div>

                    <div class="col col-md-3 instruction">
                        <div class="instruction-illustration-container">
                            <object data="img/instruction-icons/pick-up-icon.svg"></object>
                        </div>
                        <div class="instruction-caption">
                            <span class="instruction-name">Pick Up</span>
                            <span class="instruction-details">
                                You will get notified through call when your order is ready for pick-up.
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-12 footer-header">
                        <img src="img/penguin.png" alt="penguin.png" width="116px" height="104px">
                    </div>
                </div>
                <div class="row footer-details">
                    <div class="col-12 col-md-3 address-col">
                        <span class="detail-title">
                            ADDRESS
                        </span>
                        <span class="details">
                            Zone 3, Stall 1 Sto. Rosario Hagonoy Bulacan
                        </span>
                    </div>

                    <div class="col-12 col-md-3 contact-col">
                        <span class="detail-title">
                            CALL US
                        </span>
                        <span class="details">
                            0970 860 1556
                        </span>
                    </div>

                    <div class="col-12 col-md-3 hours-col">
                        <span class="detail-title">
                            OPENING HOURS
                        </span>
                        <span class="details">
                            Mon-Saturday: 8:00AM - 6:00PM <br>
                            Sunday: 10AM - 4PM
                        </span>
                    </div>

                    <div class="col-12 col-md-3 newsletter-col">
                        <span class="detail-title">
                            NEWSLETTER
                        </span>
                        <span class="details">
                            Subscribe to our daily newsletter for all latest updates.
                        </span>

                        <div class="input-container">
                            <form action="#" class="newsletter-form">
                                <input type="text" name="email" id="newsletter-input" placeholder="Email Address">
                                <button type="submit">SUBSCRIBE</button>
                            </form>
                        </div>

                    </div>

                </div>

            </div>

            <div class="lower-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-6 soc-med">
                            <span>Like us on: </span>
                            <span>
                                <a href="https://www.facebook.com/SnackWisePriceForEveryJuan" class="social-media-icon">
                                    <i class="fa-brands fa-square-facebook"></i>
                                </a>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="right">Copyright © 2022 Snackwise. All Rights Reserved.</span>
                        </div>
                    </div>
                </div>
            </div>

        </footer>

    </div>


    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="js/Menu.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            let menu = new Menu();
            menu.display_bestseller();
        });

        function open_cart() {
            document.getElementById('sidecart').style.display = "flex";
            document.getElementById('sidecart').style.animationName = "open_cart";

        }

        function close_cart() {
            document.getElementById('sidecart').style.animationName = "close_cart";
        }
    </script>

</body>

</html>