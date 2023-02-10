<?php
ob_start();
require_once "classes/order.php";
require_once "classes/pagination.php";
require_once "classes/notification.php";
$javaScript = 'include';
require_once "includes/templates/header.php";

/* defining $selectedList values when $_POST['chosen_list'] is set */
$selectedList = selectedListSetValue("orders.php");

/* hidden value of results per page sent by order list */
$rowsPerPage =  isset($_POST['per_page']) ?  filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT) : '10';

echo "<div class='table-wrapper'>";
switch($selectedList) {
    case 'allActiv':
        $tableName = 'List of all active orders';
        break;
    case 'allDoneDesc':
        $tableName = 'List of all done orders';
        break;
    case 'allCanc':
        $tableName = 'List of all cancled orders';
        break;
    case 'allDesc':
        $tableName = 'List of all orders (newest first)';
        break;
    case 'allAsc':
        $tableName = 'List of all orders (oldest first)';
        break;
    case 'personDesc':
        $tableName = 'Orders made by persons (oldest first)';
        break;
    case 'personAsc':
        $tableName = 'Orders made by persons (newest first)';
        break;
    case 'companyDesc':
        $tableName = 'Orders made by companies (newest first)';
        break;
    case 'companyAsc':
        $tableName = 'Orders made by companies (oldest first)';
        break;
}

fixingUrl();
?>

    <h2 class='textcentered'><?=$tableName;?></h2>

    <form id="display-result" action="" method="post">
        <label for="chosen_list">Select order list:</label>
        <select name="chosen_list" id="chosen_list" onchange="this.form.submit()">
            <option value="allActiv" <?= $selectedList === 'allActiv' ? 'selected' : ''; ?>>All active orders</option>
            <option value="allDoneDesc" <?= $selectedList === 'allDoneDesc' ? 'selected' : ''; ?>>All completed orders</option>
            <option value="allCanc" <?= $selectedList === 'allCanc' ? 'selected' : ''; ?>>All cancled orders</option>
            <option value="allDesc" <?= $selectedList === 'allDesc' ? 'selected' : ''; ?>>All orders (newest first)</option>
            <option value="allAsc" <?= $selectedList === 'allAsc' ? 'selected' : ''; ?>>All orders (oldest first)</option>
            <option value="personDesc" <?= $selectedList === 'personDesc' ? 'selected' : ''; ?>>Orders by persons (newest first)</option>
            <option value="personAsc" <?= $selectedList === 'personAsc' ? 'selected' : ''; ?>>Orders by persons (oldest first)</option>
            <option value="companyDesc" <?= $selectedList === 'companyDesc' ? 'selected' : ''; ?>>Orders by companies (newest first)</option>
            <option value="companyAsc" <?= $selectedList === 'companyAsc' ? 'selected' : ''; ?>>Orders by companies (oldest first)</option>
        </select>
        <input type="text" name="per_page" value="<?= $rowsPerPage; ?>" hidden>
    </form>

<?php
/* results per page dropdown */
require_once 'includes/templates/perpagedropdown.php';

$message = new Notification();
$message->getAllOrderMessages();
$message->getAllCustomerMessages();
?>

    <table class='main-table'>
        <thead class='table-header'>
        <tr>
            <th style="width: 4%">ID</th>
            <th style="max-width: 10%; width: 10%">Customer Name</th>
            <th style="max-width: 5%; width: 5%">Loyalty Card</th>
            <th style="max-width: 30%; width: 30%">Articles with comments</th>
            <th style="max-width: 8%; width: 8%">Price:</th>
            <th style="max-width: 25%; width: 25%">Order Comment</th>
            <th style="max-width: 8%; width: 8%">Order Date</th>
            <th style="max-width: 6%; width: 6%">Status</th>
            <th style="max-width: 4%; width: 4%">Edit</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $order = new Order();
        switch($selectedList) {
            case 'allActiv':
                $order->getOrdersTable('allActivDesc');
                break;
            case 'allDoneDesc':
                $order->getOrdersTable('allDoneDesc');
                break;
            case 'allCanc':
                $order->getOrdersTable('allCancDesc');
                break;
            case 'allDesc':
                $order->getOrdersTable('allDesc');
                break;
            case 'allAsc':
                $order->getOrdersTable('allAsc');
                break;
            case 'personDesc':
                $order->getOrdersTable('personDesc');
                break;
            case 'personAsc':
                $order->getOrdersTable('personAsc');
                break;
            case 'companyDesc':
                $order->getOrdersTable('companyDesc');
                break;
            case 'companyAsc':
                $order->getOrdersTable('companyAsc');
                break;
        }
        ?>

        </tbody>
    </table>
<?php
require_once "includes/templates/pagenumbering.php";
require_once "includes/templates/footer.php";