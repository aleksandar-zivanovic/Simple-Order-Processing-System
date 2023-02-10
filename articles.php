<?php
require_once "classes/article.php";
require_once "classes/pagination.php";
require_once "classes/notification.php";
$javaScript = 'include';
require_once "includes/templates/header.php";

$message = new Notification();
$message->getAllArticleMessages();

//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'allAsc' ? $selectedList = 'allAsc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'allDesc' ? $selectedList = 'allDesc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'inactiveAsc' ? $selectedList = 'inactiveAsc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'inactiveDesc' ? $selectedList = 'inactiveDesc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'activeAsc' ? $selectedList = 'activeAsc' : '';
//isset($_POST['chosen_list']) && $_POST['chosen_list'] === 'activeDesc' ? $selectedList = 'activeDesc' : '';
//$selectedList = $_POST['chosen_list'] ?? 'activeDesc';

/* deffining $selectedList values when $_POST['chosen_list'] isn't set */
$selectedList = selectedListSetValue("articles.php");

/* hidden value of results per page sent by customer list */
$rowsPerPage =  isset($_POST['per_page']) ?  filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT) : '10';

echo "<div class='table-wrapper'>";
switch($selectedList) {
    case 'allAsc':
        $tableName = 'List of all articlase (oldest first)';
        break;
    case 'allDesc':
        $tableName = 'List of all articlase in database (newest first)';
        break;
    case 'inactiveAsc':
        $tableName = 'List of inactive articlase (oldest first)';
        break;
    case 'inactiveDesc':
        $tableName = 'List of inactive articlase (newest first)';
        break;
    default:
        $tableName = 'List of active articlase (newest first)';
}

fixingUrl();
?>

    <h2 class='textcentered'><?=$tableName;?></h2>

    <form id="display-result" action="" method="post">
        <label id="display-result"  for="chosen_list">Sort article list:</label>
        <select name="chosen_list" id="chosen_list" onchange="this.form.submit()">
            <option value="activeAsc" <?=$selectedList === 'activeAsc' ? 'selected': '';?>>Active articles (oldest first)</option>
            <option value="activeDesc" <?=$selectedList === 'activeDesc' ? 'selected': '';?>>Active articles (newest first)</option>
            <option value="inactiveAsc" <?=$selectedList === 'inactiveAsc' ? 'selected': '';?>>Removed articles (oldest first)</option>
            <option value="inactiveDesc" <?=$selectedList === 'inactiveDesc' ? 'selected': '';?>>Removed articles (newest first)</option>
            <option value="allAsc" <?=$selectedList === 'allAsc' ? 'selected': '';?>>All Articles (oldest first)</option>
            <option value="allDesc" <?=$selectedList === 'allDesc' ? 'selected': '';?>>All Articles (newest first)</option>
        </select>
        <input type="text" name="per_page" value="<?= $rowsPerPage; ?>" hidden>
    </form>

<?php require_once 'includes/templates/perpagedropdown.php' ?>

    <table class='main-table'>
        <thead class='table-header'>
        <tr>
            <th style="width: 2%">DB ID</th>
            <th>Type</th>
            <th>Name</th>
            <th>Code</th>
            <th>Price</th>
            <th style="width: 5%">Unit</th>
            <th>Comment</th>
            <th style="width: 8%">Created at</th>
            <th style="width: 8%">Updated at</th>
            <th>Edit</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $article = new Article();
        switch($selectedList) {
            case 'allAsc':
                $article->getAllArticles("allAsc", "astatus", "table", null);
                break;
            case 'allDesc':
                $article->getAllArticles("allDesc", "astatus", "table", "ORDER BY id DESC");
                break;
            case 'inactiveAsc':
                $article->getAllArticles("inactiveAsc", "astatus", "table", null);
                break;
            case 'inactiveDesc':
                $article->getAllArticles("inactiveDesc", "astatus", "table", "ORDER BY id DESC");
                break;
            case 'activeAsc':
                $article->getAllArticles("activeAsc", "astatus", "table", null);
                break;
            default:
                $article->getAllArticles("activeDesc", "astatus", "table", "ORDER BY id DESC");
        }
        ?>

        </tbody>
    </table>

<?php
echo "</div>";

require_once "includes/templates/pagenumbering.php";
require_once "includes/templates/footer.php";