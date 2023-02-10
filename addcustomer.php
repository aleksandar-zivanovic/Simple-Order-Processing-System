<?php
require_once 'classes/loyaltycard.php';
require_once 'classes/notification.php';
require_once "includes/templates/header.php";
$message = new Notification();
$message->getAllCustomerMessages();
?>

    <main class="form-main">
        <h2 class="textcentered">Create new customer:</h2>
        <div class="form-wrapper">
            <form action="includes/createcustomer.php" method="POST">
                <fieldset>
                    <legend>Enter customer details</legend>

                    <div class="input-item">
                        <label for="customerName">Name:</label>
                        <input type="text" id="customerName" name="customerName"">
                    </div>

                    <div class="input-item">
                        <label for="customerType">Type:</label>
                        <select name="customerType" id="customerType">
                            <option value="person">Person</option>
                            <option value="company">Company</option>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="customerLCard">L Card:</label>
                        <select name="customerLCard" id="customerLCard">
                            <?php
                            $loyaltyCard = new LoyaltyCard();
                            $cardStatuses = $loyaltyCard->allowedStatuses;
                            $cardStatuses[] = "false";
                            foreach($cardStatuses as $singleStatus) {
                                if($singleStatus != "false") {
                                    echo "<option value='{$singleStatus}'>" . ucfirst($singleStatus) . "</option>";
                                } else {
                                    echo "<option value='{$singleStatus}' selected>No loyalty card</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <label for="comment">Comment:</label>
                    <textarea name="customerComment" id="comment" rows="5"><?php echo $customerById['ccomment'] ?? ''; ?></textarea><br>

                    <div class="input-item">
                        <input type="submit" name="create_customer" value="create customer">
                    </div>
                </fieldset>
            </form>
        </div>
    </main>
<?php include_once "includes/templates/footer.php"; ?>