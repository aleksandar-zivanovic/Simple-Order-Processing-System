<?php
session_start();

if(!isset($_POST['create_order'])) {
    die(header('location:../index.php'));
}

if(!isset($_POST["customerName"])) {
    require_once "../classes/notification.php";
    $message = new Notification();
    $message->setDataMissingOrInvalid();
    die(header('location:../addorder.php'));
}

require_once "../classes/order.php";
require_once "../classes/article.php";
require_once "../classes/customer.php";

$order = new Order();
$order->createOrder();