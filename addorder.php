<?php
require_once "classes/order.php";
require_once "classes/article.php";
require_once "classes/customer.php";
require_once "classes/notification.php";
include_once "includes/templates/header.php";

$message = new Notification();
$message->getAllOrderMessages();
$message->getAllArticleMessages();

if(!isset($_POST['add_articles'])) {
    $customer = new Customer();
    $allCustomers = $customer->getCustomers("data");
} else {
    $addToOrderId = filter_input(INPUT_POST, 'orderId', FILTER_SANITIZE_NUMBER_INT);
    $customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT);
    $generalComment = filter_input(INPUT_POST, 'generalComment', FILTER_DEFAULT);
}

$article = new Article();
$allArticles = $article->getAllArticles(null, null, "data", null);

$types = [];

foreach($allArticles as $article) {
    array_push($types, $article['atype']);
}
?>

    <main id="order-form" class="form-main">

        <?php
        $makeOrder = '<h2 class="textcentered">Make an order:</h2>';
        $addArticles = '<h2 class="textcentered">Add articles to the current order:</h2>';
        $h2Text = !isset($_POST['add_articles']) ? $makeOrder : $addArticles;
        ?>
        <?php echo $h2Text; ?>
        <div class="form-wrapper">

            <?php if(!isset($_POST['add_articles'])): ?>
            <form action="includes/createorder.php" method="POST">
                <?php else: ?>
                <form action="includes/updateorder.php" method="POST">
                    <input type="number" name="orderId" value="<?= $addToOrderId ?>" hidden>
                    <input type="number" name="customerId" value="<?= $customerId ?>" hidden>
                    <?php endif; ?>
                    <fieldset>
                        <legend>Choose articles</legend>

                        <?php
                        $types = array_unique($types);
                        $articlesIdArray = [];
                        foreach($types as $singleType):
                            ?>
                            <details>
                                <summary class="article-type"><span><?= ucfirst($singleType) ?></span></summary>
                                <dl>
                                    <?php
                                    foreach($allArticles as $singleArticle) {
                                        if($singleArticle['atype'] === $singleType) {
                                            $articlesIdArray[] = $singleArticle['id'];
                                            echo "<div class='article-order'>";
                                            echo "<dt>{$singleArticle['aname']} ({$singleArticle['acode']})</dt>";
                                            echo "<dd>{$singleArticle['acomment']}</dd>";
                                            ?>
                                            <div class="item-qty-div">
                                                <label for="articleQuantity<?= $singleArticle['id']; ?>">Quantity:</label>
                                                <input type='number' id="articleQuantity<?= $singleArticle['id']; ?>"
                                                       class="item-qty"
                                                       name="articleQuantity<?= $singleArticle['id']; ?>"
                                                       placeholder="0"><br>
                                            </div>
                                            <div style="position: relative;">
                                                <label for="itemComment<?= $singleArticle['id']; ?>">Article
                                                    Comment:</label>
                                                <textarea style="resize: none;"
                                                          name="itemComment<?= $singleArticle['id']; ?>"
                                                          id="itemComment<?= $singleArticle['id']; ?>"
                                                          class="item-comment" rows="1"></textarea><br>
                                            </div>

                                            <?php
                                            echo "</div>";
                                            echo "<hr>";
                                        }
                                    }
                                    ?>
                                </dl>
                            </details>
                        <?php endforeach; ?>

                        <?php if(!isset($_POST['add_articles'])): ?>
                            <div class="input-item">
                                <label for="customerName">Customer name:</label>
                                <select name="customerName" id="customerName">
                                    <?php
                                    foreach($allCustomers as $customer) {
                                        echo "<option value='{$customer['id']}'>({$customer['id']}) {$customer['cname']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <label for="comment">Order comment:</label>
                        <textarea name="orderComment" id="comment"
                                  rows="5"><?php echo isset($_POST['add_articles']) ? $generalComment : ''; ?></textarea><br>

                        <div class="input-item">
                            <?php
                            $inputCreateOrder = "<input type='submit' name='create_order' value='create new order'>";
                            $inputAddArticles = "<input type='submit' name='add_articles' value='add article(s) to the order'>";
                            $settingSubmitt = !isset($_POST['add_articles']) ? $inputCreateOrder : $inputAddArticles;
                            ?>
                            <?php echo $settingSubmitt; ?>
                        </div>


                    </fieldset>
                </form>
        </div>

    </main>

<?php include_once("includes/templates/footer.php"); ?>