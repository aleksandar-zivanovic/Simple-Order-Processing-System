<?php
require_once "classes/loyaltycard.php";
require_once "classes/customer.php";
require_once "classes/notification.php";
include_once "includes/templates/header.php";

$message = new Notification();
$message->getAllCardMessages();

$customer = new Customer();
$allCustomers = $customer->getCustomers("data");

$loyaltyCard = new LoyaltyCard();
?>

    <main id="order-form" class="form-main">
        <h2 class="textcentered">Create new loyalty card:</h2>
        <div class="form-wrapper">
            <form action="includes/createloyaltycard.php" method="POST">
                <fieldset>
                    <legend>Loyalty card</legend>
                    <div class="input-item">
                        <label for="customerName">Customer name:</label>
                        <select name="customerId" id="customerName">
                            <?php
                            foreach($allCustomers as $customer) {
                                if(!in_array($customer['id'], $loyaltyCard->getAllCustomersWithCards())) {
                                    echo "<option value='{$customer['id']}'>({$customer['id']}) {$customer['cname']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="status">Status:</label>
                        <select name="cardStatus" id="status">
                            <?php
                            foreach($loyaltyCard->allowedStatuses as $status) {
                                echo "<option value='{$status}'>" . ucfirst($status) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <label for="comment">Loyalty card comment:</label>
                    <textarea name="lcComment" id="comment" rows="5"></textarea><br>

                    <div class="input-item">
                        <input type="submit" name="create_lc" value="create loyalty card">
                    </div>


                </fieldset>
            </form>
        </div>
    </main>

<?php include_once("includes/templates/footer.php"); ?>