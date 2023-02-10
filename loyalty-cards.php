<?php
ob_start();
require_once "classes/loyaltycard.php";
require_once "classes/notification.php";
require_once "classes/pagination.php";
$javaScript = 'include';
require_once "includes/templates/header.php";

/* defining $selectedList values when $_POST['chosen_list'] is set */
$selectedList = selectedListSetValue("loyalty-cards.php");

/* hidden value of results per page */
$rowsPerPage =  isset($_POST['per_page']) ?  filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT) : '10';

echo "<div class='table-wrapper'>";
switch($selectedList) {
    case 'alllc':
        $tableName = 'List of all loyalty cards with all statuses (oldest first)';
        break;
    case 'allDesc':
        $tableName = 'List of all loyalty cards with all statuses (lastest first)';
        break;
    case 'allAct':
        $tableName = 'List of all active loyalty cards (oldest first)';
        break;
    case 'allActDesc':
        $tableName = 'List of all active loyalty cards (lastest first)';
        break;
    case 'allInact':
        $tableName = 'List of all inactive loyalty cards (oldest first)';
        break;
    case 'allInactDesc':
        $tableName = 'List of all inactive loyalty cards (lastest first)';
        break;
    case 'allRem':
        $tableName = 'List of all removed loyalty cards (oldest first)';
        break;
    case 'allRemDesc':
        $tableName = 'List of all removed loyalty cards (lastest first)';
        break;
    case 'company':
        $tableName = 'List of all loyalty cards owned by companies (oldest first)';
        break;
    case 'companyDesc':
        $tableName = 'List of all loyalty cards owned by companies (lastest first)';
        break;
    case 'person':
        $tableName = 'List of all loyalty cards owned by persons (oldest first)';
        break;
    case 'personDesc':
        $tableName = 'List of all loyalty cards owned by persons (lastest first)';
        break;
}

fixingUrl();
?>

    <h2 class='textcentered'><?=$tableName;?></h2>

    <form id="display-result" action="" method="post">
        <label for="chosen_list">Select loyalty card list:</label>
        <select name="chosen_list" id="chosen_list" onchange="this.form.submit()">
            <option value="alllc" <?= $selectedList === 'alllc' ? 'selected' : ''; ?>>All loyalty cards (oldest first)</option>
            <option value="allDesc" <?= $selectedList === 'allDesc' ? 'selected' : ''; ?>>All loyalty cards (latest first)</option>
            <option value="allAct" <?= $selectedList === 'allAct' ? 'selected' : ''; ?>>All active cards (oldest first)</option>
            <option value="allActDesc" <?= $selectedList === 'allActDesc' ? 'selected' : ''; ?>>All active cards (latest first)</option>
            <option value="allInact" <?= $selectedList === 'allInact' ? 'selected' : ''; ?>>All inactive cards (oldest first)</option>
            <option value="allInactDesc" <?= $selectedList === 'allInactDesc' ? 'selected' : ''; ?>>All inactive cards (latest first)</option>
            <option value="allRem" <?= $selectedList === 'allRem' ? 'selected' : ''; ?>>All removed cards (oldest first)</option>
            <option value="allRemDesc" <?= $selectedList === 'allRemDesc' ? 'selected' : ''; ?>>All removed cards (latest first)</option>
            <option value="company" <?= $selectedList === 'company' ? 'selected' : ''; ?>>Companies (oldest first)</option>
            <option value="companyDesc" <?= $selectedList === 'companyDesc' ? 'selected' : ''; ?>>Companies (latest first)</option>
            <option value="person" <?= $selectedList === 'person' ? 'selected' : ''; ?>>Persons (oldest first)</option>
            <option value="personDesc" <?= $selectedList === 'personDesc' ? 'selected' : ''; ?>>Persons (latest first)</option>
        </select>
        <input type="text" name="per_page" value="<?= $rowsPerPage; ?>" hidden>
    </form>

<?php
require_once 'includes/templates/perpagedropdown.php';

$message = new Notification();
$message->getAllCardMessages();
?>

    <table class='main-table'>
        <thead class='table-header'>
        <tr>
            <th style="width: 4%">ID</th>
            <th style="max-width: 34%; width: 34%">Customer</th>
            <th style="max-width: 7%; width: 8%">Status</th>
            <th style="max-width: 25%; width: 25%">Comment</th>
            <th style="max-width: 8%; width: 8%">Created at</th>
            <th style="max-width: 8%; width: 8%">Updated at</th>
            <th style="max-width: 10%; width: 10%">Change status</th>
            <th style="max-width: 4%; width: 4%">Edit</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $loyaltycard = new LoyaltyCard();
        switch($selectedList) {
            case 'alllc':
                $loyaltycard->getLoyaltyCardsTable('alllc');
                break;
            case 'allDesc':
                $loyaltycard->getLoyaltyCardsTable('allDesc');
                break;
            case 'allAct':
                $loyaltycard->getLoyaltyCardsTable('allAct');
                break;
            case 'allActDesc':
                $loyaltycard->getLoyaltyCardsTable('allActDesc');
                break;
            case 'allInact':
                $loyaltycard->getLoyaltyCardsTable('allInact');
                break;
            case 'allInactDesc':
                $loyaltycard->getLoyaltyCardsTable('allInactDesc');
                break;
            case 'allRem':
                $loyaltycard->getLoyaltyCardsTable('allRem');
                break;
            case 'allRemDesc':
                $loyaltycard->getLoyaltyCardsTable('allRemDesc');
                break;
            case 'company':
                $loyaltycard->getLoyaltyCardsTable('company');
                break;
            case 'companyDesc':
                $loyaltycard->getLoyaltyCardsTable('companyDesc');
                break;
            case 'person':
                $loyaltycard->getLoyaltyCardsTable('person');
                break;
            case 'personDesc':
                $loyaltycard->getLoyaltyCardsTable('personDesc');
                break;
        }
        ?>

        </tbody>
    </table>
<?php
require_once "includes/templates/pagenumbering.php";
require_once "includes/templates/footer.php";