<?php
if(!isset($_POST['removeRowId'])) die(header('location:../index.php'));
require_once '../classes/order.php';

$id = clean(filter_input(INPUT_POST, 'removeRowId', FILTER_DEFAULT));
$order = new Order();
$order->removeItemFromOrder($id);