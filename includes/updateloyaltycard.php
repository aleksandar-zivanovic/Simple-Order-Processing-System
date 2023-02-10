<?php
session_start();
if(!isset($_POST['update_loyalty_card'])) die(header("location:../loyalty-cards.php"));
require_once '../classes/loyaltycard.php';
require_once '../classes/notification.php';

$message = new Notification();
$loyaltyCard = new LoyaltyCard();
$loyaltyCard->id = clean(filter_input(INPUT_POST, 'cardId', FILTER_SANITIZE_NUMBER_INT));

if(!empty($_POST['cardId']) &&
    !empty($_POST['customerId']) &&
    !empty($_POST['cardStatus']) &&
    in_array($_POST['cardStatus'], $loyaltyCard->allowedStatuses)
) {
    if(
        $_POST['oldCustomerId'] == $_POST['customerId'] &&
        $_POST['oldCardStatus'] == $_POST['cardStatus'] &&
        $_POST['oldCardComment'] == $_POST['lcComment']
    ) {
        die(header("location:../editloyaltycard.php?editid=" . $loyaltyCard->id));
    }

    if(!$loyaltyCard->getCustomerByCardId(clean($loyaltyCard->id))) {
        $message->setUncategorizedError("ERROR: Chosen customer doesn't exist!");
        die(header("location:../editloyaltycard.php?editid=" . $loyaltyCard->id));
    }

    if($loyaltyCard->updateCard()) {
        $message->setUpdateCardDetails(true);
        header("location:../editloyaltycard.php?editid=" . $loyaltyCard->id);
    } else {
        $message->setUpdateCardDetails(false);
        header("location:../editloyaltycard.php?editid=" . $loyaltyCard->id);
    }
} else {
    $message->setDataMissingOrInvalid();
    header("location:../editloyaltycard.php?editid=" . $loyaltyCard->id);
}