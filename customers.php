<?php
require_once "classes/customer.php";
require_once "classes/pagination.php";
require_once "classes/notification.php";
$javaScript = 'include';
require_once "includes/templates/header.php";

//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'allDesc' ? $selectedList = 'allDesc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'allasc' ? $selectedList = 'allasc' : '';
////isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'hasls' ? $selectedList = 'hasls' : '';
////isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'nols' ? $selectedList = 'nols' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'person' ? $selectedList = 'person' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'company' ? $selectedList = 'company' : '';

/* defining $selectedList values when $_POST['chosen_list'] isn't set */
$selectedList = selectedListSetValue("customers.php");

/* hidden value of results per page sent by customer list */
$rowsPerPage =  isset($_POST['per_page']) ?  filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT) : '10';

echo "<div class='table-wrapper'>";
switch($selectedList) {
    case 'allDesc':
        $tableName = 'List of all customers (descending)';
        break;
    case 'allasc':
        $tableName = 'List of all customers (ascending)';
        break;
    case 'haslc':
        $tableName = 'List of customers with loyalty card ';
        break;
    case 'nolc':
        $tableName = 'List of customers without loyaltycard ';
        break;
    case 'person':
        $tableName = 'List of customers by type: Person';
        break;
    case 'company':
        $tableName = 'List of customers by type: Company';
        break;
}

fixingUrl();
?>

    <h2 class='textcentered'><?= $tableName; ?></h2>

    <form id="display-result" action="" method="post">
        <label for="chosen_list">Sort customers:</label>
        <select name="chosen_list" id="chosen_list" onchange="this.form.submit()">
            <option value="allDesc" <?= $selectedList === 'allDesc' ? 'selected' : ''; ?>>All customers (descending)</option>
            <option value="allasc" <?= $selectedList === 'allasc' ? 'selected' : ''; ?>>All customers (ascending)</option>
            <option value="haslc" <?= $selectedList === 'haslc' ? 'selected' : ''; ?>>Customers with loyalty card</option>
            <option value="nolc" <?= $selectedList === 'nolc' ? 'selected' : ''; ?>>Customers without loyalty card</option>
            <option value="person" <?= $selectedList === 'person' ? 'selected' : ''; ?>>Customers by type: person</option>
            <option value="company" <?= $selectedList === 'company' ? 'selected' : ''; ?>>Customers by type: company</option>
        </select>
        <input type="text" name="per_page" value="<?= $rowsPerPage; ?>" hidden>
    </form>

<?php
/* results per page dropdown */
require_once 'includes/templates/perpagedropdown.php';

$message = new Notification();
$message->getAllCustomerMessages();
$message->getAllCardMessages();
?>

    <table class='main-table'>
        <thead class='table-header'>
        <tr>
            <th style="width: 2%">ID</th>
            <th style="width: 21%">Name</th>
            <th style="max-width: 10%; width: 10%">Type</th>
            <th style="max-width: 5%; width: 5%">Loyalty Card</th>
            <th style="width: 40%">Comment</th>
            <th style="width: 8%">Created at</th>
            <th style="width: 8%">Updated at</th>
            <th style="max-width: 5%; width: 5%">Edit</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $customer = new Customer();
        switch($selectedList) {
            case 'allDesc':
                $customer->getCustomers('allDesc');
                break;
            case 'allasc':
                $customer->getCustomers('allasc');
                break;
            case 'haslc':
                $customer->getCustomers('haslc');
                break;
            case 'nolc':
                $customer->getCustomers('nolc');
                break;
            case 'person':
                $customer->getCustomers('person');
                break;
            case 'company':
                $customer->getCustomers('company');
                break;
        }
        ?>

        </tbody>
    </table>

<?php
echo "</div>";

require_once "includes/templates/pagenumbering.php";
require_once "includes/templates/footer.php";