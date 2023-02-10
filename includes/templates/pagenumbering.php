<div>
    <p style="text-align: center;">
        <?php
        if(isset($_POST['per_page'])) {
            $rowsPerPage = clean($_POST['per_page']);
        } elseif(isset($_GET['perpage'])) {
            $rowsPerPage = clean($_GET['perpage']);
        } else {
            $rowsPerPage = 10;
        }

        $rawPageUrl = $_SERVER['REQUEST_URI'];

        /* setting column and its value for customers.php */
        if(str_contains($rawPageUrl, "/customers.php")) {
            if((isset($_POST['chosen_list']) && $_POST['chosen_list'] === "company") ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "company")) {
                $columnName = "ctype";
                $columnValue = "company";
            } elseif((isset($_POST['chosen_list']) && $_POST['chosen_list'] === "person") ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "person")) {
                $columnName = "ctype";
                $columnValue = "person";
            } elseif((isset($_POST['chosen_list']) && $_POST['chosen_list'] === "haslc") ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "haslc")) {
                $columnName = "lcstatus";
                $columnValue = "true";
            } elseif((isset($_POST['chosen_list']) && $_POST['chosen_list'] === "nolc") ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "nolc")) {
                $columnName = "lcstatus";
                $columnValue = "false";
            } else {
                $columnName = null;
                $columnValue = null;
            }
        }

        /* setting column and its value for articles.php */
        if(str_contains($rawPageUrl, "/articles.php")) {
            if((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "inactive")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "inactive"))) {
                $columnName = "astatus";
                $columnValue = "inactive";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "active")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "active"))) {
                $columnName = "astatus";
                $columnValue = "active";
            } else {
                $columnName = null;
                $columnValue = null;
            }
        }

        /* setting column and its value for orders.php */
        if(str_contains($rawPageUrl, "/orders.php")) {
            if((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allActiv")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allActiv"))) {
                $columnName = "ostatus";
                $columnValue = "active";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allDone")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allDone"))) {
                $columnName = "ostatus";
                $columnValue = "done";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allCanc")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allCanc"))) {
                $columnName = "ostatus";
                $columnValue = "canceled";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "person")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "person"))) {
                $columnName = "ctype";
                $columnValue = "person";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "company")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "company"))) {
                $columnName = "ctype";
                $columnValue = "company";
            } else {
                $columnName = null;
                $columnValue = null;
            }
        }

        /* setting column and its value for loyalty-cards.php */
        if(str_contains($rawPageUrl, "/loyalty-cards.php")) {
            if((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allAct")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allAct"))) {
                $columnName = "lcstatus";
                $columnValue = "active";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allInact")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allInact"))) {
                $columnName = "lcstatus";
                $columnValue = "inactive";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "allRem")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "allRem"))) {
                $columnName = "lcstatus";
                $columnValue = "removed";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "person")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "person"))) {
                $columnName = "ctype";
                $columnValue = "person";
            } elseif((isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], "company")) ||
                (!isset($_POST['chosen_list']) && isset($_GET['sort']) && str_starts_with($_GET['sort'], "company"))) {
                $columnName = "ctype";
                $columnValue = "company";
            } else {
                $columnName = null;
                $columnValue = null;
            }
        }

        $className = classByUrl();
        $totalPages = ${$className}->getPagination()->totalPages($rowsPerPage, $className . 's', $columnName, $columnValue);

        for($pageNum = 1; $pageNum <= $totalPages; $pageNum++):
            $removeFrom = strpos($rawPageUrl, "?");
            $pageUrl = substr($rawPageUrl, 0, $removeFrom) . "?page=" . $pageNum;

            if(isset($_POST['chosen_list'])) {
                $pageUrl .= "&sort=" . filter_input(INPUT_POST, 'chosen_list', FILTER_DEFAULT);
            } elseif(!empty($_GET['sort'])) {
                $pageUrl .= "&sort=" . filter_input(INPUT_GET, 'sort', FILTER_DEFAULT);
            }

            if(isset($_POST['per_page'])) {
                $pageUrl .= "&perpage=" . filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT);
            } elseif(!empty($_GET['perpage'])) {
                $pageUrl .= "&perpage=" . filter_input(INPUT_GET, 'perpage', FILTER_DEFAULT);
            }
            ?>
            <a href="<?= $pageUrl ?>">[<?= $pageNum; ?>]</a>
        <?php endfor; ?>
    </p>
</div>