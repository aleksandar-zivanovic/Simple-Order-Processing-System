<?php
session_start();
if(!isset($_POST['update_customer'])) die(header("location:../customers.php"));
require_once "../classes/customer.php";
require_once "../classes/loyaltycard.php";
require_once "../classes/notification.php";

$loyaltyCard = new LoyaltyCard();
$customersCardDetails = $loyaltyCard->getCardByUserId(clean($_POST['customerId']));
$customersCard = is_array($customersCardDetails) ? $customersCardDetails['lcstatus'] : "false";
$oldCardStatus = clean(filter_input(INPUT_POST, 'oldCardStatus', FILTER_DEFAULT));
$loyaltyCard->id = clean(filter_input(INPUT_POST, 'oldCardId', FILTER_DEFAULT));
$cadStatuses = $loyaltyCard->allowedStatuses;
$cadStatuses[] = "false";
$message = new Notification();

if(!empty($_POST['customerId']) &&
    !empty($_POST['customerName']) &&
    !empty($_POST['customerType']) &&
    in_array($_POST['cardStatus'], $cadStatuses)
) {
    $customer = new Customer();
    if($customer->updateCustomer()) {
        /* creating new loyalty card */
        if($loyaltyCard->id == 0 && $_POST['cardStatus'] != $oldCardStatus) $loyaltyCard->createLoyaltyCard();

        /* updating existing loyalty card status if status is changed */
        if($oldCardStatus != "false" && $_POST['cardStatus'] != $oldCardStatus) $loyaltyCard->updateCard();

        $message->setUpdateCustomerDetails(clean(filter_input(INPUT_POST, 'customerName', FILTER_DEFAULT)), true);
        header("location:../editcustomer.php?editid=" . clean(filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT)));
    } else {
        $message->setUpdateCustomerDetails(clean(filter_input(INPUT_POST, 'customerName', FILTER_DEFAULT)), false);
        header("location:../editcustomer.php?editid=" . clean(filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT)));
    }
} else {
    $message->setDataMissingOrInvalid();
    header("location:../editcustomer.php?editid=" . clean(filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT)));
}