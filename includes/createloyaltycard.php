<?php
session_start();
if(!isset($_POST['create_lc'])) die(header("location:../index.php"));
require_once '../classes/loyaltycard.php';
require_once '../classes/customer.php';
require_once '../classes/notification.php';

$customer = new Customer();
$customerDetails = $customer->getCustomerById(clean($_POST['customerId']));
$customerName = $customerDetails['cname'];
$message = new Notification();

if(!empty($_POST['customerId']) && !empty($_POST['cardStatus'])) {
    $loyaltyCard = new LoyaltyCard();
    $result = $loyaltyCard->createLoyaltyCard();
    if($result !== 0) {
        $message->setCreateCardMessage(true,$customerName);
        header("location:../loyalty-cards.php#lcid{$result}");
    } else {
        $message->setCreateCardMessage(false,$customerName);
        header("location:../addloyaltycard.php");
    }

} else {
    $message->setDataMissingOrInvalid();
    header("location:../addloyaltycard.php");
}