<?php
require_once dirname(__FILE__) . '/php/classes/DbConnection.php';
require_once dirname(__FILE__) . '/php/classes/Validate.php';

$validate = new Validate();
if ($validate->is_logged_in("customer")) {
    header('Location: account/login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Snackwise</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- PAGE ICON -->
    <link rel="icon" href="img/penguin.png" type="image/icon type">

    <!-- FONT LINKS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Poppins:ital,wght@0,300;0,600;0,700;1,400&family=Roboto:ital,wght@0,300;0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js"></script>


    <!-- EXTERNAL CSS -->
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="css/notification.css">



    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lightgallery-bundle.css'>

    <style>
        /* light gallery backdrop */
        .lg-backdrop {
            opacity: 0.8 !important;
        }
    </style>
</head>

<!-- <button type="button" id="current_order">Ordes</button>


    <button type="button" id="completed_order">Order History</button> -->
</head>

<body>
    <!-- orders will be appended here
    <div class="order_list" id="order_list"></div>

    toast_notif notification will be appended here
    <div class="toast_notif" id="toast_notif"></div> -->

    <div class="details-modal">
        <div class="content-container">
            <div class="closing-bar">
                <button class="close-btn">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="mod-top">
                <div class="header-col with-details">
                    <span class="order-number-label">Order No.:</span>
                    <span class="order-number">8215185238</span><br>

                    <span class="label">Order Date:</span>
                    <span class="order-date value">10-18-22</span><br>
    <!-- toast_notif notification will be appended here -->
    <div class="toast_notif" id="toast_notif"></div>

                    <span class="label">Status:</span>
                    <span class="status pending-stat value">Pending</span>
                </div>
                <div class="header-col">
                    <!-- ORDER QR CODE HERE -->
                    INSERT QR HERE
                </div>
            </div>
            <div class="items-list">
                <!-- ORDER ITEMS APPEND HERE -->
                <div class="item">
                    <div class="item-img-container">
                        <img src="img/menu-imgs/a.jpg" alt="Combo A">
                    </div>
                    <div class="item-details">
                        <div class="quantity-con">
                            <span><span class="modal-quantity">3</span>x</span>
                        </div>
                        <div class="item-name-con">
                            <span class="item-name">Combo A</span>
                        </div>
                        <div class="item-price-con">
                            <span class="item-price">75.00PHP</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="mod-footer">
                <div class="footer-col">
                    <span>Subtotal: </span>
                </div>
                <div class="footer-col">
                    <span class="sub-total">253.50PHP</span>
                </div>
            </div>
            <div class="cancel-bar">
                <button class="btn btn-danger cancel-btn">Cancel Order</button>
            </div>
        </div>
    <div class="details-modal" id="order_details_list">
        
    </div>

    <div class="parent-container">
        <!-- START OF NAVBAR -->
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
                            <a href="index.php" class="nav-link" id="">Home</a>
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

                    ?>
                        
                        <div class="user-notifications-container">
                            <button class="notification-button">
                                <i class="fa-solid fa-bell"></i>
                            </button>

                            <div class="notifications-panel">
                                <div class="panel-header-container">
                                    <span class="panel-header">Notifications</span>
                                </div>
                                <div class="notifications-container">
                                    <!-- NOTIFICATIONS TO BE APPENDED HERE -->
                                   
                                    <!--FOR EMPTY NOTIFICATION
                                        Naka 'display:none' pa to sa css.
                                    -->
                                    <div class="empty-notification">
                                        <span class="empty-message"> 
                                            Looks like you doesn't have notifications yet. 
                                        </span>
                                    </div> 
                                    
                                    <!-- START OF DUMMY NOTIFICATIONS -->
                                    <div class="notification" id="notif-success">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header unread-notification">Thank You for Ordering 💖</span>
                                            </div>
                                            <span class="notification-time">1m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                Thanks for your order. It’s always a pleasure to serve you. Enjoy your snack!
                                            </span>
                                        </div>
                                        <span class="additional-message"></span>
                                    </div>

                                    <div class="notification" id="notif-success">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header">Order Ready for Pickup! 😋</span>
                                            </div>
                                            <span class="notification-time">10m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                Your order #3145185238 is now ready for pick-up. Grab it now while it's hot!
                                            </span>
                                        </div>
                                        <span class="additional-message"></span>
                                    </div>                                    
                                   
                                    <div class="notification" id="">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header">Order on Process 🍳</span>
                                            </div>
                                            <span class="notification-time">25m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                They’re getting your food ready. You will recieve another notification if its ready.
                                            </span>
                                        </div>
                                        <span class="additional-message"></span>
                                    </div>
                                   
                                    <div class="notification" id="">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header">Order Confirmed ✨ </span>
                                            </div>
                                            <span class="notification-time">28m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                Your order is now confirmed and will be processed in a few minutes.
                                            </span>
                                        </div>
                                        <span class="additional-message"></span>
                                    </div>
                                    
                                    <div class="notification">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header">Order Placed ✔</span>
                                            </div>
                                            <span class="notification-time">30m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                Your order #3145185238 is now confirmed and now processing.
                                            </span>
                                        </div>
                                        <span class="additional-message"></span>
                                    </div>

                                    <div class="notification" id="notif-cancelled">
                                        <div class="notification-header-container">
                                            <div class="header-info">
                                                <span class="order-number">#3145185238</span>
                                                <span class="notification-header">Order Cancelled ❌</span>
                                            </div>
                                            <span class="notification-time">30m Ago</span>
                                        </div>
                                        <div class="notification-body-container">
                                            <span class="notification-body">
                                                Your order has been rejected and cancelled.
                                            </span>
                                        </div>
                                        <span class="additional-message">
                                            Reason: Item unavailable.
                                        </span>
                                    </div>
                                    <!-- END OF DUMMY NOTIFICATION -->
                                </div>
                            </div>

                        </div>

                        <div class="user-dropdown-container">
                            <button class="user-button">
                                <i class="fa-solid fa-circle-user"></i>
                            </button>
                            <ul class="drop-menu">
                                <li><a href="order.php" class="drop-item" id="active">My Orders <i class="fa-solid fa-receipt"></i></a></li>
                                <li><a href="account/logout.php" class="drop-item">Logout <i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                            </ul>
                        </div>

                    <?php
                    }
                    ?>

                </div>
            </div>
        </nav>
        <!-- ENG OF NAVBAR -->

        <div class="order-container">
            <div class="order-header">
                <h1>My Orders</h1>
            </div>
            <div class="order-sort-container">
                <ul class="sort-list">
                    <li class="sort-item" id="active-sort">
                        <input type="radio" name="category" value="All" id="category1" checked>
                        <label for="category1">All</label>
                    </li>
                    <li class="sort-item" id="">
                        <input type="radio" name="category" value="Placed" id="category2"> 
                        <input type="radio" name="category" value="Pending" id="category2"> 
                        <label for="category2">Pending</label>
                    </li>
                    <li class="sort-item">
                        <input type="radio" name="category" value="Preparing" id="category3">
                        <label for="category3">Preparing</label>
                    </li>
                    <li class="sort-item">
                        <input type="radio" name="category" value="Ready" id="category4">
                        <label for="category4">To Pickup</label>
                    </li>
                    <li class="sort-item">
                        <input type="radio" name="category" value="Completed" id="category5">
                        <label for="category5">Completed</label>
                    </li>
                    <li class="sort-item">
                        <input type="radio" name="category" value="Cancelled" id="category6">
                        <label for="category6">Cancelled</label>
                    </li>
                    <li class="sort-item">
                        <input type="radio" name="category" value="Completed" id="category5">
                        <label for="category5">Completed</label>
                    </li>
                </ul>
            </div>
            <div class="order-list">
                <!-- ORDERS TO BE APPENDED HERE -->

                <div class="order-item">
                    <!-- FOR REFERENCE ONLY -->
                    <!-- ORDER DETAILS HERE -->
                    <div class="order-details-row">
                        <div class="order-no-container">
                            <span>Order No.</span>
                            <span class="order-number">8215185238</span>
                        </div>
                        <div class="order-date-container">
                            <span>10-18-2022</span>
                        </div>
                    </div>
                    <!-- PWEDENG TANGGALIN TONG TRACKING NUMBER
                         KUNG WALA TAYONG GANTO -->
                    <div class="order-details-row">
                        <div class="tracking-container">
                            <span>Tracking Number:</span>
                            <span class="tracking-number">3145185238</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="quantity-container">
                            <span>Quantity:</span>
                            <span class="quantity">3</span>
                        </div>
                        <div class="amount-container">
                            <span>Amount:</span>
                            <span class="total-amt">253.50PHP</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="details-btn-container">
                            <button class="details-btn">Details</button>
                        </div>
                        <div class="status-container">
                            <!-- 
                                Napapalitan kulay nung status pa apply nalang nung class:
                                    .pending-stat
                                    .preparing-stat
                                    .pickup-stat
                                    .complete-stat
                                    .cancelled-stat
                             -->
                            <span class="order-status pending-stat">Pending</span>
                        </div>
                    </div>
                </div>


                <div class="order-item">
                    <!-- FOR REFERENCE ONLY -->
                    <!-- ORDER DETAILS HERE -->
                    <div class="order-details-row">
                        <div class="order-no-container">
                            <span>Order No.</span>
                            <span class="order-number">8215185238</span>
                        </div>
                        <div class="order-date-container">
                            <span>10-18-2022</span>
                        </div>
                    </div>
                    <!-- PWEDENG TANGGALIN TONG TRACKING NUMBER
                         KUNG WALA TAYONG GANTO -->
                    <div class="order-details-row">
                        <div class="tracking-container">
                            <span>Tracking Number:</span>
                            <span class="tracking-number">3145185238</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="quantity-container">
                            <span>Quantity:</span>
                            <span class="quantity">3</span>
                        </div>
                        <div class="amount-container">
                            <span>Amount:</span>
                            <span class="total-amt">253.50PHP</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="details-btn-container">
                            <button class="details-btn">Details</button>
                        </div>
                        <div class="status-container">
                            <!-- 
                                Napapalitan kulay nung status pa apply nalang nung class:
                                    .pending-stat
                                    .preparing-stat
                                    .pickup-stat
                                    .complete-stat
                                    .cancelled-stat
                             -->
                            <span class="order-status preparing-stat">Preparing</span>
                        </div>
                    </div>
                </div>

                <div class="order-item">
                    <!-- FOR REFERENCE ONLY -->
                    <!-- ORDER DETAILS HERE -->
                    <div class="order-details-row">
                        <div class="order-no-container">
                            <span>Order No.</span>
                            <span class="order-number">8215185238</span>
                        </div>
                        <div class="order-date-container">
                            <span>10-18-2022</span>
                        </div>
                    </div>
                    <!-- PWEDENG TANGGALIN TONG TRACKING NUMBER
                         KUNG WALA TAYONG GANTO -->
                    <div class="order-details-row">
                        <div class="tracking-container">
                            <span>Tracking Number:</span>
                            <span class="tracking-number">3145185238</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="quantity-container">
                            <span>Quantity:</span>
                            <span class="quantity">3</span>
                        </div>
                        <div class="amount-container">
                            <span>Amount:</span>
                            <span class="total-amt">253.50PHP</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="details-btn-container">
                            <button class="details-btn">Details</button>
                        </div>
                        <div class="status-container">
                            <!-- 
                                Napapalitan kulay nung status pa apply nalang nung class:
                                    .pending-stat
                                    .preparing-stat
                                    .pickup-stat
                                    .complete-stat
                                    .cancelled-stat
                             -->
                            <span class="order-status pickup-stat">Ready for Pickup</span>
                        </div>
                    </div>
                </div>

                <div class="order-item">
                    <!-- FOR REFERENCE ONLY -->
                    <!-- ORDER DETAILS HERE -->
                    <div class="order-details-row">
                        <div class="order-no-container">
                            <span>Order No.</span>
                            <span class="order-number">8215185238</span>
                        </div>
                        <div class="order-date-container">
                            <span>10-18-2022</span>
                        </div>
                    </div>
                    <!-- PWEDENG TANGGALIN TONG TRACKING NUMBER
                         KUNG WALA TAYONG GANTO -->
                    <div class="order-details-row">
                        <div class="tracking-container">
                            <span>Tracking Number:</span>
                            <span class="tracking-number">3145185238</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="quantity-container">
                            <span>Quantity:</span>
                            <span class="quantity">3</span>
                        </div>
                        <div class="amount-container">
                            <span>Amount:</span>
                            <span class="total-amt">253.50PHP</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="details-btn-container">
                            <button class="details-btn">Details</button>
                        </div>
                        <div class="status-container">
                            <!-- 
                                Napapalitan kulay nung status pa apply nalang nung class:
                                    .pending-stat
                                    .preparing-stat
                                    .pickup-stat
                                    .complete-stat
                                    .cancelled-stat
                             -->
                            <span class="order-status cancelled-stat">Cancelled</span>
                        </div>
                    </div>
                </div>

                <!-- 
                    If wala pang order ito ung mag di-display
                    
                    P.S
                        NAKA DISPLAY BLOCK TO SA CSS.
                 -->
                <div class="no-orders-container">
                    <span class="no-order-message">Looks like you haven't made an order yet.</span>
                    <a href="menu.php">Order Now</a>
                </div>


            </div>
     
            <!-- ORDERS TO BE APPENDED HERE -->
            <div class="order-list" id="order_list"> </div>
           
        </div>

    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="js/Order.js"></script>
<script src="js/Notification.js"></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/lightgallery.umd.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/thumbnail/lg-thumbnail.umd.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/zoom/lg-zoom.umd.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/video/lg-video.umd.js'></script>
    <script>
        /* DROPDOWN */

        const dropMenu = document.querySelector('.drop-menu');
        const dropBtn = document.querySelector('.user-button');
        let dropOpen = false;

        if (dropBtn) {
            dropBtn.addEventListener("click", () => {
                if (!dropOpen) {
                    dropMenu.style.display = "block";
                    dropOpen = true;
                } else {
                    dropOpen = false;
                    dropMenu.style.display = "none";
                }

            })
        }
        /* END OF DROPDOWN */

        // DETAILS MODAL OPENING
        const detailsBtn = document.querySelector('.details-btn');
        const closeBtn = document.querySelector('.close-btn');
        const modal = document.querySelector('.details-modal');
        let modalOpen = false;

        if (detailsBtn) {
            detailsBtn.addEventListener("click", () => {
                modal.style.display = "flex";
                modalOpen = true;
            })
        }

        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
            modalOpen = false;
        })
        new Notification().notification();
        new Order().customer_order();

<?php 
    /* adds selected bestseller item to cart */
        if(isset($_GET['order'])) {
     ?>
    new Notification().create_notification("Order Successfully Placed", "success");
    let url = document.location.href;
    window.history.pushState({}, "", url.split("?")[0]);
<?php
        } 
        ?>
    </script>
</body>


    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

<script src="js/Order.js"></script>
<script src="js/Notification.js"></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/lightgallery.umd.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/thumbnail/lg-thumbnail.umd.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/zoom/lg-zoom.umd.js'></script>
<script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/video/lg-video.umd.js'></script>

<script>
    let order = new Order();
    document.addEventListener("DOMContentLoaded", function(event) {
        order.display_order();
        document.getElementById("current_order").onclick = function() {

            order.display_order();
        };
        document.getElementById("completed_order").onclick = function() {

            order.display_completed_order();
        };

    });



    /* determined if a customer canceled its order, 
    if an order has been canceled, the table will be reloaded */
    get_notification();

    function get_notification() {
        Pusher.logToConsole = true;

        let pusher = new Pusher('56c2bebb33825a275ca8', {
            cluster: 'ap1'
        });

        let channel = pusher.subscribe('snackwise');
        channel.bind('notif', function(data) {
            if (data['notification']['type'] == "order_staff_to_customer") {
                order.display_order();
            }



        });
    }
</script>

</html>