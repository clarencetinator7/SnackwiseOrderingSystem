<?php 
require_once dirname(__FILE__) . '/../php/classes/DbConnection.php';
if(isset($_GET["staff"]) == 1) {
    $_SESSION['user_type'] = 'staff';
} else {
    $_SESSION['user_type'] = "customer";
    
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Snackwise</title>

    <!-- PAGE ICON -->
    <link rel="icon" href="../img/penguin.png" type="image/icon type">
    

    <!-- FONT LINKS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js"></script>
    <link rel="stylesheet" href="../css/register.css">

    <style>
     
    </style>
</head>

<div class="parent-container">

<div class="container p-5 form-container">
    <div class="snackwise-label">
        <div class="brand-icon">
            <img src="../img/penguin.png" class="snackwise-icon" alt="Snackwise Logo">
        </div>
        <div class="brand-name">
            <span class="red">SNACK<span class="yellow">WISE</span></span>
        </div>
    </div>
    <form id="account_form" method="POST">

        <div class="row">
            <div class="col form-header">
                <h3>Sign Up</h3>
            </div>
        </div>
        <div class="">
            <span id="success_message"></span>
            <span id="error_message"></span>
        </div>
        <section class="personal-information-sec input section">
            <div class="row">
                <span>Personal Information</span>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 form-group">
                    <!-- <label for="firstname">First Name</label> -->
                    <input type="text" class="form-control m-2" name="firstname" id="firstname" placeholder="First Name" value="" autocomplete="off">
                    <span class="" id="firstname_error"></span>
                </div>
                <div class="col-md-6 col-12 form-group">
                    <!-- <label for="lastname">Last Name </label> -->
                    <input type="text" class="form-control m-2" name="lastname" id="lastname" placeholder="Last Name" value="" autocomplete="off">
                    <span class="" id="lastname_error"></span>

                </div>
            </div>
            <div class="row">
                <div class="col">
                    <!-- <label for="contact">Contact Number</label> -->
                    <input type="text" class="form-control m-2" name="contact" id="contact" placeholder="Contact Number" value="" autocomplete="off">
                    <span class="" id="contact_error"></span>
                </div>
            </div>
        </section>

        <section class="address-information-sec input-section">
            <div class="row">
                <span>Address Information</span>
            </div>
            <div class="row">
                <div class="col">
                    <!-- <label for="street" for="">Street</label> -->
                    <input type="text" class="form-control m-2" name="street" id="street" placeholder="House Number / Street / Building No.">
                    <span class="" id="street_error"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-6">
                    <!-- <label for="">Region</label> -->
                    <select name="region" class="form-select form-select-md m-2" id="region">
                        <option class="address_option" value="none">Region</option>
                    </select>
                    <span class="" id="region_error"></span>

                </div>
                <div class="col-md-7 col-6">
                    <!-- <label for="">Province</label> -->
                    <select name="province" class="form-select form-select-md m-2" id="province">
                        <option class="address_option" value="none">Province</option>
                    </select>
                    <span class="" id="province_error"></span>
                </div>
                <div class="col-md-12 col-6">
                    <!-- <label for="">Municipality</label> -->
                    <select name="municipality" class="form-select form-select-md m-2" id="municipality">
                        <option class="address_option" value="none">Munincipality</option>
                    </select>
                    <span class="" id="municipality_error"></span>

                </div>
                <div class="col-md-12 col-6">

                    <!-- <label for="">Barangay</label> -->
                    <select name="barangay" class="form-select form-select-md m-2" id="barangay">
                        <option class="address_option" value="none">Barangay</option>
                    </select>
                    <span class="" id="barangay_error"></span>
                </div>
            </div>
        </section>

        <section class="account-information-sec input-section">
            <div class="row">
                <span>Account Information</span>
            </div>

            <div class="row">
                <div class="col form-group">
                    <!-- <label for="username">Username</label> -->
                    <input type="text" class="form-control m-2" name="username" id="username" placeholder="Username " value="" autocomplete="off">
                    <span class="" id="username_error"></span>
                </div>
            </div>

            <div class="row">
                <div class="col form-group">
                    <!-- <label for="email">Email Address</label> -->
                    <input type="text" class="form-control m-2" name="email" id="email" placeholder="Email Address" value="" autocomplete="off">
                    <span class="" id="email_error"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-group">
                    <!-- <label for="password">Password</label> -->
                    <input type="password" class="form-control m-2" name="password" id="password" placeholder="Password" value="" onkeyup="new Account().verify_password(this.value);" autocomplete="off">
                    <span class="" id="password_error"></span>
                </div>
                <div class="col-12 form-group">
                    <!-- <label for="retype_password">Retype Password</label> -->
                    <input type="password" class="form-control m-2" name="retype_password" id="retype_password" placeholder="Retype Password" value="" autocomplete="off">
                    <span class="" id="retype_password_error"></span>
                </div>

                <div class="password_requirements">
                    <h6><span class="length " id="length" style="opacity: 0;">&#x2716;</span>8 to 16 characters</h6>
                    <h6><span class="case " id="case" style="opacity: 0;">&#x2716;</span>At least 1 Uppercase</h6>
                    <h6><span class="number " id="number" style="opacity: 0;">&#x2716;</span>At least 1 number</h6>
                    <h6><span class="special " id="special" style="opacity: 0;">&#x2716;</span>At least 1 special</h6>
                </div>
            </div>
        </section>
      
        <div class="row">
            <div class="col">
                <p class="text-end">Already have an account? <a href="../account/login.php">Sign In</a></p>
            </div>
        </div>
        <div class="form-end-btn">
            <div class="">
                <a href="../index.php" id="cancel">Cancel</a>
            </div>
            <div class="">
                <button type="button" id="register" class="btn btn-primary"> Register</button>
            </div>

        </div>
    </form>

</div>

</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="../js/Account.js"></script>
    <script src="../js/Address.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        let account = new Account();
        /* onclick="new Account().toggle_password(this.id, this.getAttribute('for'))" */
       /*  document.getElementById("password_toggler").onclick = function (e) {
            new Account().toggle_password(this.id, this.getAttribute('for'));
           
        } */
       





        document.getElementById("register").onclick = function () {

            account.register();
        }


        new Address().addressSelector("region", "province", "municipality", "barangay");

    });
</script>


</html>