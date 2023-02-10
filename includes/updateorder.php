<?php
require_once '../classes/order.php';
require_once '../classes/notification.php';
require_once '../classes/customer.php';
require_once '../classes/article.php';
require_once 'functions.php';
session_start();

$message = new Notification();
$order = new Order();
$customer = new Customer();
$article = new Article();

if(!isset($_POST['update_order']) && !isset($_POST['add_articles'])) die(header('location:../orders.php'));
if(empty($_POST['orderId']) || empty($_POST['customerId'])) die(header('location:../orders.php'));
$postKeys = array_keys($_POST);
$searchResultArticleId = [];
$articleIdArray = [];
$searchResultArticleQuantity = [];

if(!isset($_POST['add_articles'])) {
    foreach($postKeys as $itemOrder) {
        $searchResultArticleId[] = str_contains($itemOrder, 'articleId');
        $searchResultArticleQuantity[] = str_contains($itemOrder, 'articleQuantity');
    }
    if(!in_array(true, $searchResultArticleId) && in_array(true, $searchResultArticleQuantity)) die(header('location:../orders.php'));
}

$orderId = filter_input(INPUT_POST, 'orderId', FILTER_SANITIZE_NUMBER_INT);
$customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT);
$orderComment = clean(!empty($_POST['orderComment']) ? filter_input(INPUT_POST, 'orderComment', FILTER_DEFAULT) : null);
$oldOrderComment = clean(filter_input(INPUT_POST, 'oldOrderComment', FILTER_DEFAULT));
$oldOrderStatus = clean(filter_input(INPUT_POST, 'oldOrderStatus', FILTER_DEFAULT));

// check if the customer with the passed ID exists
if(!$customer->getCustomerById($customerId)) {
    $message->setCustomerNotFoundError();
    die(header('location:../orders.php'));
}

if(isset($_POST['update_order'])) {
    if(empty(filter_input(INPUT_POST, 'orderStatus', FILTER_DEFAULT))) die(header('location:../editorder.php?editid=' . $orderId));
    $orderStatus = filter_input(INPUT_POST, 'orderStatus', FILTER_DEFAULT);
    if(!$order->orderStatusExists($orderStatus)) {
        $message->setUnexistingOrderStatusError();
        die(header('location:../editorder.php?editid=' . $orderId));
    }
} else {
    $orderStatus = $oldOrderStatus;
}

$theOrder = $order->getOrderById($orderId);
// update details if any general order detail is changed
if($theOrder['ocid'] != $customerId || $orderComment != $oldOrderComment || $orderStatus != $oldOrderStatus) {
    if(!$order->updateGeneralOrderDetails($customerId, $orderComment, $orderStatus, $orderId)) {
        $theCustomer = $customer->getCustomerById($customerId);
        $message->setUpdateCustomerDetails($theCustomer['cname'], false);
        die(header('location:../orders.php'));
    }
}


$rowIds = [];
$articleIds = [];
$itemQuantities = [];
$itemComments = [];

foreach($_POST as $key => $value) {
    $order->checkUpdateDetails($orderId, $key, $value);
    str_contains($key, 'rowId') && !empty($value) ? $rowIds[] = filter_var($value, FILTER_SANITIZE_NUMBER_INT) : '';
    if(isset($_POST['update_order'])) {
        str_contains($key, 'articleId') && !empty($value) ? $articleIds[] = filter_var($value, FILTER_SANITIZE_NUMBER_INT) : '';
    } elseif(isset($_POST['add_articles'])) {
        str_contains($key, 'articleQuantity') && !empty($value) ? $articleIds[] = filter_var(substr($key, 15), FILTER_SANITIZE_NUMBER_INT) : '';
    }
    str_contains($key, 'articleQuantity') && !empty($value) ? $itemQuantities[] = filter_var($value, FILTER_SANITIZE_NUMBER_INT) : '';
    str_contains($key, 'itemComment') ? $itemComments[] = filter_var($value, FILTER_DEFAULT) : '';
}

if(isset($_POST['add_articles'])) {
    $itemComments = [];
    foreach($articleIds as $value) {
        $itemComments[] = $_POST['itemComment' . $value];
    }
}

// checking if the article(s) ID(s) from $articleIds exist
if(!$article->articleIdExists($articleIds)) {
    $message->setUnexistingArticle();
    die(header('location:../editorder.php?editid=' . $orderId));
}

$allParameters = [];
$itemIds = [];

/* updating already ordered articles */
if(isset($_POST['update_order'])) {
    foreach($rowIds as $key => $value) {
        $allParameters[] = [
            'rowId' => $value, // ID of the order_items table
            'itemId' => $articleIds[$key],
            'itemQuantity' => $itemQuantities[$key],
            'itemComment' => $itemComments[$key]
        ];
        $itemIds[] = $value;
    }

    $order->unexistingItemProcess($itemIds);

    $order->updateAllOrderedItems($allParameters, $orderId) ?
        $message->setUpdateOrderMessage(true) :
        $message->setUpdateOrderMessage(false);
    header('location:../editorder.php?editid=' . $orderId);
} elseif(isset($_POST['add_articles'])) {
    for($i = 0; $i < count($articleIds); $i++) {
        $allParameters[] = [$articleIds[$i], $itemQuantities[$i], $itemComments[$i]];
    }
}

/* adding new article(s) to an existing order */
if(isset($_POST['add_articles'])) {
    $itemIds = [];
    foreach($allParameters as $key => $value) {
        if(!empty($allParameters[$key][0])) $itemIds[] = $allParameters[$key][0];
    }

    if(empty($itemIds)) {
        $message->setUncategorizedError("You tried to insert a fake ID");
        die(header('location:../orders.php'));
    } else {
        $order->unexistingItemProcess($itemIds);
    }

    $order->addItemsToOrder($allParameters, $orderId) ? $message->setUpdateOrderMessage(true) : $message->setUpdateOrderMessage(false);
    header('location:../editorder.php?editid=' . $orderId);
}