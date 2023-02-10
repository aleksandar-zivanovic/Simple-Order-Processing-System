<?php
// START - helpers only for production purpose. Delete them in final version
function prikazPrintR($array)
{
    echo "<pre>";
    print_r($array);
    echo "<pre>";
}

function prikazVarDump($array)
{
    echo "<pre>";
    var_dump($array);
    echo "<pre>";
}

function prikazStatementa($statement) {
    echo "<p style='background-color: palegreen'>{$statement}</p>";
}

function prikazPostGet() {
    echo "<strong>\$_POST</strong>";
    prikazPrintR($_POST);
    echo "<strong>\$_GET</strong>";
    prikazPrintR($_GET);
}
// END - helpers only for production purpose. Delete them in final version


function getPageName()
{
    $pageUri = $_SERVER['PHP_SELF'];
    if(str_ends_with($pageUri, ".php") && !str_ends_with($pageUri, "index.php")) {
        $pageUri = str_replace(".php", "", $pageUri);
        $removeFrom = strrpos($pageUri, "/") + 1;
        $title = ucfirst(substr($pageUri, $removeFrom)) . " | Order Processing System";
    }

    if(str_ends_with($pageUri, "index.php")) {
        $title = "Order Processing System";
    }

    echo $title;
}

function errorLog($error)
{
    $file = __DIR__ . '/logs/error.log';
    $errorMessage = "[" . date('d/m/Y H:i:s') . "] " . $error . PHP_EOL;
    file_put_contents($file, $errorMessage, FILE_APPEND);
}

function clean($text)
{
    return trim(htmlspecialchars($text));
}

function selectedListSetValue(string $page): string
{
    if(isset($_POST['chosen_list'])) {
        return clean(filter_input(INPUT_POST, 'chosen_list', FILTER_DEFAULT));
    } elseif(!isset($_GET['page'])) {
        return $page === "orders.php" ? 'allActiv' : 'allDesc';
    } else {
        // $_GET['sort'] isn't set when there isn't set any sort (from dropdown) and is chosen some page
        return isset($_GET['sort']) ? clean(filter_input(INPUT_GET, 'sort', FILTER_DEFAULT)) : 'allDesc';
    }
}

/* fixing url after choosing different sort type (example: setting customers.php?page=1) */
//if(isset($_POST["chosen_list"]) && isset($_POST["per_page"])):
function fixingUrl(): void
{
    if(!empty($_POST)) {
        $page = 1;
        $sort = clean(filter_input(INPUT_POST, "chosen_list", FILTER_DEFAULT));
        $perPage = clean(filter_input(INPUT_POST, "per_page", FILTER_DEFAULT));
        $url = $_SERVER['REQUEST_URI'];
        $positionStart = strrpos($url, "/") + 1;
        $positionEnd = strpos($url, ".php") + 4;
        $length = $positionEnd - $positionStart;
        $fileName = substr($url, $positionStart, $length);

        echo "<script>urlAdjustment('$sort', '$perPage', '$fileName');</script>";
    }
}

function classByUrl(): string
{
    $url = $_SERVER['REQUEST_URI'];
    if(str_contains($url, "customers.php")) return "customer";
    if(str_contains($url, "articles.php")) return "article";
    if(str_contains($url, "orders.php")) return "order";
    if(str_contains($url, "loyalty-cards.php")) return "loyaltycard";
    return "";
}

function cardStatusShow($result): string
{
    $textBorder = "text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black; font-weight:bold;";
    switch($result['lcstatus']) {
        case "active":
            $addStyle = "style='{$textBorder} color:#B2FA79;'";
            break;
        case "inactive":
            $addStyle = "style='{$textBorder} color:#FFB4A3FF;'";
            break;
        case "removed":
            $addStyle = "style='{$textBorder} color:lightgrey;'";
            break;
    }

    return "<td class='textcentered' {$addStyle}>{$result['lcstatus']}</td>";
}