<?php
!isset($_GET['editid']) || !is_numeric($_GET['editid']) || intval($_GET['editid']) < 1 ?
    die(header("location:loyalty-cards.php")) : $theLoyaltyCardId = filter_input(INPUT_GET, 'editid', FILTER_SANITIZE_NUMBER_INT);
require_once "classes/customer.php";
require_once "classes/loyaltycard.php";
require_once "classes/notification.php";
$javaScript = $jquery = "include";
require_once "includes/templates/header.php";

$loyaltyCard = new LoyaltyCard();
$theCardDetails = $loyaltyCard->getCardById($theLoyaltyCardId);
if(!$theCardDetails) die(header("location:loyalty-cards.php"));

$theCustomerDetails = $loyaltyCard->getCustomerByCardId($theLoyaltyCardId);

$customer = new Customer();
$allCustomers = $customer->getCustomers('data');

$message = new Notification();
$message->getAllCardMessages();
?>

    <main class="form-main">

        <!--  Selected card details  -->
        <h2 class="textcentered">Loyalty card details:</h2>
        <table id="existing-details-table">
            <tr>
                <th>Customer ID</th>
                <td><?= $theCustomerDetails['lccid']; ?></td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td><?= "{$theCustomerDetails['cname']} ({$theCustomerDetails['ctype']})"; ?></td>
            </tr>
            <tr>
                <th>Loyalty Card Status</th>
                <td><?= "{$theCardDetails['lcstatus']}"; ?></td>
            </tr>
            <tr>
                <th>Loyalty Card Comment</th>
                <td><?php echo $theCardDetails['lccomment'] ? "{$theCardDetails['lccomment']}" : ""; ?></td>
            </tr>

            <tr>
                <th>Loyalty Card Created</th>
                <td><?= $theCardDetails['lccreated']; ?></td>
            </tr>

            <tr>
                <th>Loyalty Card Last Update</th>
                <td><?= $theCardDetails['lcupdated']; ?></td>
            </tr>
        </table>

        <h2 class="textcentered">Edit loyalty card details:</h2>
        <div class="form-wrapper">

            <!--  Edit card form  -->
            <form action="includes/updateloyaltycard.php" method="POST">
                <fieldset>
                    <legend>Update card details</legend>
                    <input type="text" id="cardId" name="cardId" value="<?= $theLoyaltyCardId; ?>" hidden>

                    <div class="input-item">
                        <label for="oldCustomerId">Customer ID:</label>
                        <input type="text" id="oldCustomerId" name="oldCustomerId" value="<?= $theCustomerDetails['lccid']; ?>" hidden>
                        <input type="text" id="displayCustomerId" value="<?= $theCustomerDetails['lccid']; ?>" disabled>
                    </div>

                    <div class="input-item">
                        <label for="customerId">Name:</label>
                        <select id="customerId" name="customerId" onchange="updateCustomerId()">
                            <?php
                            foreach($allCustomers as $singleCustomer):
                                if($singleCustomer['id'] === $theCardDetails['lccid']):
                                    ?>
                                    <option class="customer" value="<?= $theCustomerDetails['lccid']; ?>" selected><?= $theCustomerDetails['cname']; ?></option>
                                <?php
                                else:
                                    if(!in_array($singleCustomer['id'], $loyaltyCard->getAllCustomersWithCards())):
                                        ?>
                                        <option class="customer" value="<?= $singleCustomer['id']; ?>"><?= $singleCustomer['cname']; ?></option>
                                    <?php
                                    endif;
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="cardStatus">Card Status:</label>
                        <select name="cardStatus" id="cardStatus">

                            <?php
                            $allStatuses = $loyaltyCard->allowedStatuses;
                            foreach($allStatuses as $singleStatus) {
                                if($theCardDetails['lcstatus'] == $singleStatus) {
                                    echo "<option value='{$singleStatus}' selected>" . ucfirst($singleStatus) . "</option>";
                                } else {
                                    echo "<option value='{$singleStatus}'>" . ucfirst($singleStatus) . "</option>";
                                }
                            }
                            ?>

                        </select>
                        <br>
                    </div>
                    <input type="text" name="oldCardStatus" value="<?= $theCardDetails['lcstatus']; ?>" hidden>

                    <label id="cardComment">Loyalty Card Comment:</label>
                    <textarea name="lcComment" class="comment"
                              rows="5"><?php echo !empty($theCardDetails['lccomment']) ? $theCardDetails['lccomment'] : ""; ?></textarea>
                    <input type="text" name="oldCardComment" value="<?= $theCardDetails['lccomment']; ?>" hidden>

                    <div class="input-item">
                        <input type="submit" name="update_loyalty_card" value="update the loyalty card">
                    </div>

                </fieldset>
            </form>
        </div>
    </main>

<?php include_once("includes/templates/footer.php"); ?>