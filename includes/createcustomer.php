<?php
session_start();
if(!isset($_POST['create_customer'])) {
    die(header("location:../customers.php"));
}

require_once "../classes/customer.php";
require_once "../classes/loyaltycard.php";
require_once "../classes/notification.php";

$loyaltyCard = new LoyaltyCard();
$cardStatuses = $loyaltyCard->allowedStatuses;
$cardStatuses[] = "false";

$message = new Notification();

if(!empty($_POST['customerName']) &&
    !empty($_POST['customerType']) &&
    in_array($_POST['customerLCard'], $cardStatuses)
) {
    $customer = new Customer();
    $result = $customer->createCustomer();
    if($result !== 0) {
        array_pop($cardStatuses);
        if(in_array($_POST['customerLCard'], $cardStatuses)) {
            $loyaltyCard->customerId = $result;
            $loyaltyCard->status = clean(filter_input(INPUT_POST, 'customerLCard', FILTER_DEFAULT));
            $loyaltyCard->comment = "";
            $message->setCreateCardMessage((bool)$loyaltyCard->createLoyaltyCard($escapeCleanPostValues = true), clean($_POST['customerName']));
        }

        $message->setCreateCustomerMessage(clean($_POST['customerName']), true);
        header("location:../customers.php");
    } else {
        $message->setCreateCustomerMessage(clean($_POST['customerName']), false);
        header("location:../addcustomer.php");
    }

} else {
    $message->setDataMissingOrInvalid();
    header("location:../addcustomer.php");
}