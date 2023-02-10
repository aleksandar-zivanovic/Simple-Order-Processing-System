<?php
if(!isset($_GET['cuid']) || !is_numeric($_GET['cuid']) || $_GET['cuid'] == 0) die(header('location:../index.php'));

session_start();
require_once "../classes/customer.php";
require_once "../classes/loyaltycard.php";

$customer = new Customer();
$loyaltyCard = new LoyaltyCard();
$id = filter_input(INPUT_GET, 'cuid', FILTER_SANITIZE_NUMBER_INT);
$theCustomer = $customer->getCustomerById($id);
$cardByUserId = $loyaltyCard->getCardByUserId($id);
$cardStatus = $cardByUserId ? $cardByUserId["lcstatus"] : false;
$response = ["customertype" => $theCustomer["ctype"], "loyaltycard" => $cardStatus];
echo json_encode($response);