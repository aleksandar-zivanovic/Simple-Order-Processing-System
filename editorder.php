<?php
!isset($_GET['editid']) || !is_numeric($_GET['editid']) || intval($_GET['editid']) < 1 ?
    die(header("location:orders.php")) : $theOrderId = filter_input(INPUT_GET, 'editid', FILTER_SANITIZE_NUMBER_INT);
require_once "classes/order.php";
require_once "classes/customer.php";
require_once "classes/article.php";
require_once "classes/loyaltycard.php";
require_once "classes/notification.php";
$jquery = "include";
$javaScript = "include";
require_once "includes/templates/header.php";


$order = new Order();
if(!$order->orderIdExists($theOrderId)) die(header("location:orders.php"));
$theOrderDetails = $order->getOrderById($theOrderId);
$theOrderItems = $order->getOrderedArticlesByOrderId($theOrderId);
$customerId = $order->getColumnFromCustomerByOrderId('customers.id', $theOrderId);

$customer = new Customer();
$customerById = $customer->getCustomers($customerId);
$allCustomers = $customer->getCustomers('data');

$article = new Article();
$allArticles = $article->getAllArticles(null, null, "data", "ORDER BY atype");

$loyaltyCard = new LoyaltyCard();
$cardDetails = $loyaltyCard->getCardById($customerId);
$cardStatus = is_array($cardDetails) ? ucfirst($cardDetails['lcstatus']) : "Doesn't have";

$message = new Notification();
$message->getAllOrderMessages();
$message->getAllArticleMessages();
?>

<main class="form-main">

    <!--  Current order details  -->
    <h2 class="textcentered">Current order:</h2>
    <table id="existing-details-table">
        <tr>
            <th>Order ID</th>
            <td><?=$theOrderDetails['id'];?></td>
        </tr>
        <tr>
            <th>Customer Name</th>
            <td><?="{$customerById['cname']} ({$customerById['ctype']})";?></td>
        </tr>
        <tr>
            <th>Loyalty Card</th>
            <td><?=$cardStatus;?></td>
        </tr>
        <tr>
            <th>Articles with comments</th>
            <td>
                <?php
                    $priceArray = [];
                    $itemIdsAndPrices = [];
                    foreach($theOrderItems as $aSingleItem) {
                        $aSingleItemFromTheOrder = $order->getAnArticlDetailsByItemId($aSingleItem['article_id']);
                        echo "<div id='itemId{$aSingleItem['article_id']}'><i>({$aSingleItem['article_quantity']}x)</i> " . $aSingleItemFromTheOrder['aname'] . ": <i>" . $aSingleItem['item_comment'] . ";</i><br></div>";
                        $resultOfArticleId = $order->getArticleNameAndPriceFromId($aSingleItem['article_id']);
                        $itemIdsAndPrices[] = ['id' => $aSingleItemFromTheOrder['article_id'], 'price' => $aSingleItemFromTheOrder['aprice']];
                        array_push($priceArray, $resultOfArticleId['aprice'] * $aSingleItem['article_quantity']);
                    }

                    $totalPrice = array_sum($priceArray);
                    $itemIdsAndPrices = array_column($itemIdsAndPrices, 'price', 'id');

                ?>
            </td>
        </tr>
        <tr>
            <th>Price</th>
            <td id="total-price">
                <?=$totalPrice;?>
            </td>
        </tr>
        <tr>
            <th>Order Comment</th>
            <td><?=$theOrderDetails['ocomment'];?></td>
        </tr>
        <tr>
            <th>Order Date</th>
            <td><?=$theOrderDetails['odate'];?></td>
        </tr>
    </table>

    <h2 class="textcentered">Edit order details:</h2>
    <div class="form-wrapper">

        <!--  Adding new article(s) form  -->
        <div id="add-article">
            <form method="post" action="addorder.php">
                <input type="number" name="orderId" value="<?=$theOrderId?>" hidden>
                <input type="number" name="customerId" value="<?=$customerId?>" hidden>
                <input type="text" name="orderComment" value="<?=$theOrderDetails['ocomment']?>" hidden>
                <input type="submit" name="add_articles" value="Add articles to the current order"/>
            </form>
        </div>

        <!--  Edit order form  -->
        <form action="includes/updateorder.php" method="POST">
            <fieldset>
                <legend>Update order details</legend>
                <input type="text" id="orderId" name="orderId" value="<?= $theOrderId; ?>" hidden>

                <div class="input-item">
                    <label for="oldCustomerId">Customer ID:</label>
                    <input type="text" id="displayCustomerId" value="<?= $customerById['id']; ?>" disabled>
                </div>

                <div class="input-item">
                    <label for="customerId">Name:</label>
                    <select id="customerId" name="customerId" onchange="updateCustomerId()">
                        <?php
                        foreach($allCustomers as $singleCustomer) {
                            if($singleCustomer['id'] === $customerById['id']):
                                ?>
                                <option class="customer" value="<?= $singleCustomer['id']; ?>" selected><?= $singleCustomer['cname']; ?></option>
                            <?php else: ?>
                                <option class="customer" value="<?= $singleCustomer['id']; ?>"><?= $singleCustomer['cname']; ?></option>
                            <?php
                            endif;
                        }
                        ?>
                    </select>
                </div>

                <div class="input-item">
                    <label for="customerType">Type:</label>
                    <select name="customerType" id="customerType" disabled>
                        <?php
                        $person = '';
                        $company = '';
                        $customerById['ctype'] === 'person' ? $person = 'selected' : $company = 'selected';
                        ?>
                        <option class="customer-type" value="person" <?=$person?>>Person</option>
                        <option class="customer-type" value="company" <?=$company?>>Company</option>
                    </select>
                </div>

                <div class="input-item">
                    <label for="customerLCard">L Card:</label>
                    <input type="text" id="customerLCard" class="lcard" value="<?=$cardStatus; ?>" disabled>

                </div>

                <div class="input-item">
                    <label for="orderStatus">Order Status:</label>
                    <select name="orderStatus" id="orderStatus">

                        <?php
                        $allStatuses = $order->orderStatusExists();
                        foreach($allStatuses as $singleStatus) {
                            if($theOrderDetails['ostatus'] == $singleStatus) {
                                echo "<option value='{$singleStatus}' selected>" . ucfirst($singleStatus) . "</option>";
                            } else {
                                echo "<option value='{$singleStatus}'>" . ucfirst($singleStatus) . "</option>";
                            }
                        }
                        ?>

                    </select>
                    <br>
                </div>
                <input type="text" name="oldOrderStatus" value="<?=$theOrderDetails['ostatus'];?>" hidden>
                <hr>

                <p class="textcentered">***</p>
                <label id="orderComment">General Order Comment:</label>
                <textarea name="orderComment" class="comment"
                          rows="5"><?php echo !empty($theOrderDetails['ocomment']) ? $theOrderDetails['ocomment'] : ""; ?></textarea>
                <input type="text" name="oldOrderComment" value="<?= $theOrderDetails['ocomment']; ?>" hidden>

                <?php
                $allKeys = [];
                foreach($theOrderItems as $key => $singleItem):
                    ?>
                <div id="article-block<?=$singleItem['article_id'];?>">
                    <p class="textcentered">***</p>

                    <input type="number" name="rowId<?=$key; ?>" id="articleQuantity" value="<?= $singleItem['id']; ?>" hidden>

                    <div class="input-item">
                        <label>Article Name:</label>
                        <select name="articleId<?=$key; ?>">
                            <?php
                            /* looping throw all articles to match id of selected article and add 'selected' attribute to its option tag */
                            foreach($allArticles as $singleArticle) {
                                if($singleArticle['id'] == $singleItem['article_id']) {
                                    echo "<option value='{$singleArticle['id']}' selected>({$singleArticle['id']} - {$singleArticle['acode']}) {$singleArticle['aname']} - ({$singleArticle['atype']})</option>";
                                } else {
                                    echo "<option value='{$singleArticle['id']}'>({$singleArticle['id']} - {$singleArticle['acode']}) {$singleArticle['aname']} - ({$singleArticle['atype']})</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-item">
                        <label>Quantity:</label>
                        <span onclick="removeArticleFromOrder(
                        <?=$singleItem['article_id']; ?>,
                        <?=$singleItem['id']; ?>,
                        <?=$singleItem['article_quantity']; ?>,
                        <?=$itemIdsAndPrices[$singleItem['article_id']]; ?>,
                        <?=$totalPrice; ?>
                                )" class="remove-article">remove</span>
                        <input type="number" name="articleQuantity<?=$key; ?>" id="articleQuantity" value="<?= $singleItem['article_quantity']; ?>">
                    </div>

                    <div class="input-item">
                        <label>Item Ordered:</label>
                        <input type="text" id="articleOrdered" value="<?= $singleItem['oiupdated']; ?>" disabled>
                    </div>

                    <label>Item Comment:</label>
                    <textarea name="itemComment<?=$key; ?>" class="comment"
                              rows="5"><?php echo $singleItem['item_comment'] != 'NULL' && !empty($singleItem['item_comment']) ? $singleItem['item_comment'] : ''; ?></textarea><br>
                </div>
                <?php
                endforeach;
                ?>

                <div class="input-item">
                    <input type="submit" name="update_order" value="update the order">
                </div>
            </fieldset>
        </form>
    </div>
</main>

<?php include_once("includes/templates/footer.php"); ?>