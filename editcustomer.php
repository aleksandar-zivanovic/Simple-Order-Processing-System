<?php
!isset($_GET['editid']) ? die(header("location:customers.php")) : "";
require_once "classes/customer.php";
require_once "classes/notification.php";
require_once "classes/loyaltycard.php";
require_once "includes/templates/header.php";

$customer = new Customer();
$customerIdFromGet = clean(filter_input(INPUT_GET, "editid", FILTER_SANITIZE_NUMBER_INT));
if(!is_numeric($customerIdFromGet) || !$customer->getCustomerById($customerIdFromGet)) die(header("location:customers.php"));
$customerById = $customer->getCustomers($customerIdFromGet);

$loyaltyCard = new LoyaltyCard();
$allCardStatuses = $loyaltyCard->allowedStatuses;
$cardDetails = $loyaltyCard->getCardById($customerIdFromGet);
$oldCardStatus = $cardDetails !== false ? $cardDetails["lcstatus"] : "false";
$oldCardId = $cardDetails !== false ? $cardDetails["id"] : 0;

$message = new Notification();
$message->getAllCustomerMessages();
?>

    <main class="form-main">
        <h2 class="textcentered">Edit customer detals:</h2>
        <div class="form-wrapper">
            <form action="includes/updatecustomer.php" method="POST">
                <fieldset>
                    <legend>Update customer details</legend>

                    <div class="input-item">
                        <label for="customerId">Customer ID:</label>
                        <input type="text" id="customerId" value="<?= $customerById['id']; ?>" disabled>
                        <input type="text" id="customerId" name="customerId" value="<?= $customerById['id']; ?>" hidden>
                    </div>

                    <div class="input-item">
                        <label for="customerName">Name:</label>
                        <input type="text" id="customerName" name="customerName" value="<?= $customerById['cname']; ?>">
                    </div>

                    <div class="input-item">
                        <label for="customerType">Type:</label>
                        <select name="customerType" id="customerType">
                            <?php
                            foreach($customer->allowdCustomerTypes as $singleType) {
                                $selected = $singleType == $customerById['ctype'] ? "selected" : "";
                                echo "<option value='{$singleType}'{$selected}>" . ucfirst($singleType) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-item">
                        <input type="text" name="oldCardStatus" value="<?=$oldCardStatus; ?>" hidden="">
                        <input type="number" name="oldCardId" value="<?=$oldCardId; ?>" hidden="">
                        <input type="text" name="lcComment" value="" hidden="">
                        <label for="cardStatus">L Card:</label>
                        <select name="cardStatus" id="cardStatus">
                            <?php
                            foreach($allCardStatuses as $singleStatus) {
                                $selected = $oldCardStatus === $singleStatus ? "selected" : "";
                                echo "<option value='{$singleStatus}' {$selected}>" . ucfirst($singleStatus) . "</option>";
                            }
                            if($oldCardStatus === "false") echo "<option value='false' selected>Doesn't have</option>";
                            ?>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="customerCreated">Added:</label>
                        <input type="text" id="customerCreated" value="<?= $customerById['ccreated']; ?>" disabled>
                    </div>

                    <div class="input-item">
                        <label for="customerUpdated">Updated:</label>
                        <input type="text" id="customerUpdated" value="<?= $customerById['cupdated']; ?>" disabled>
                    </div>

                    <label for="customerComment">Comment:</label>
                    <textarea name="customerComment" id="comment" rows="5"><?php echo $customerById['ccomment'] ?? ''; ?></textarea><br>

                    <div class="input-item">
                        <input type="submit" name="update_customer" value="update the customer">
                    </div>
                </fieldset>
            </form>
        </div>
    </main>

    <script>
        var customerType = "<?= $customerById['ctype']; ?>";
        var lcStatus = "<?= $oldCardStatus; ?>";
        customerDropdown(customerType, lcStatus);
    </script>

<?php include_once "includes/templates/footer.php"; ?>