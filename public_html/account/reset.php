<?php
require_once dirname(__FILE__). "/../php/classes/Account.php";
require_once dirname(__FILE__). "/../php/classes/Validate.php";
/* this page is used to reset the login attempt of a user */
require_once dirname(__FILE__) . "/../php/classes/Account.php";
require_once dirname(__FILE__) . "/../php/classes/Validate.php";

$account = new Account();

$validate = new Validate();
/* if ($validate->validate_code() || $validate->validate_code() != null) { */
    if ($validate->validate_code() ) {
$account->reset_attempt();
}
 /* checks if the verification code in the URL parameter is in the database */
if ($validate->validate_code()) {
    $account->reset_attempt();
}
