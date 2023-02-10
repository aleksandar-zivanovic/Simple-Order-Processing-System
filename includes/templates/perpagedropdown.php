<?php
if(isset($_POST['chosen_list'])) {
    $listType = filter_input(INPUT_POST, 'chosen_list', FILTER_DEFAULT);
} elseif(isset($_GET['sort'])) {
    $listType = clean($_GET['sort']);
} else {
    $listType = "allDesc";
}

$pagination = new Pagination();
foreach($pagination->allowedPerPageValues as $value) {
    if(isset($_POST['per_page']) && $_POST['per_page'] === $value) $totalRows = $value;
}

//if(isset($_POST['per_page']) && $_POST['per_page'] === '10') $totalRows = '10';
//if(isset($_POST['per_page']) && $_POST['per_page'] === '20') $totalRows = '20';
//if(isset($_POST['per_page']) && $_POST['per_page'] === '50') $totalRows = '50';
//if(isset($_POST['per_page']) && $_POST['per_page'] === 'all') $totalRows = 'all';
//$totalRows = isset($_POST['per_page']) ? clean($_POST['per_page']) : "";

if(isset($_POST['per_page'])) $selectedRows = clean($_POST['per_page']);
if(!isset($_POST['per_page'])) $selectedRows = $_GET['perpage'] ?? '10';

?>

<form id="form-perpage" action="" method="post">
    <label for="per_page">Results per page:</label>
    <select name="per_page" id="per_page" onchange="this.form.submit()">
        <?php
        foreach($pagination->allowedPerPageValues as $value) {
            ?>
            <option value="<?= $value; ?>" <?php if($selectedRows == $value) echo 'selected'; ?>><?= $value; ?></option>
        <?php } ?>
    </select>
    <input type="text" name="chosen_list" value="<?= $listType; ?>" hidden>
</form>