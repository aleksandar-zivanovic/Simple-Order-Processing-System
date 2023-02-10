<?php
require_once 'database.php';

class Order extends Database
{
    public int $orderId;
    public int $customerId;
    public ?string $orderComment;
    public int $itemId;
    public int $itemQuantity;
    public string $itemComment;

    private function getArticle(): Article
    {
        require_once 'article.php';
        return new Article();
    }

    private function getLoyaltyCard(): LoyaltyCard
    {
        require_once 'loyaltycard.php';
        return new LoyaltyCard();
    }

    public function getPagination(): Pagination
    {
        require_once 'pagination.php';
        return new Pagination();
    }

    public function getAllOrders()
    {
        $statement = "SELECT * FROM orders";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        $resultArray = [];
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $resultArray[] = $result;
        }
        return $resultArray;
    }

    public function getOrderById(int|array $id): array
    {
        $statement = "SELECT * FROM orders WHERE id ";

        if(is_array($id)) {
            $whereIn = "(" . implode(',', $id) . ")";
            $statement .= "IN $whereIn";
        } else {
            $statement .= "= {$id}";
        }

        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        if(is_array($id)) {
            $allOrders = [];
            while($result = $query->fetch(PDO::FETCH_ASSOC)) {
                $allOrders[] = $result;
            }
            return $allOrders;
        } else {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function setValues()
    {
        $allOrders = $this->getAllOrders();
        $arrayOfCustmerIds = [];
        foreach($allOrders as $singleOrder) {
            $arrayOfCustmerIds[] = $singleOrder['ocid'];
        }

        if(isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], 'person')) {
            $column = 'ctype';
            $value = 'person';
        }

        if(isset($_POST['chosen_list']) && str_starts_with($_POST['chosen_list'], 'company')) {
            $column = 'ctype';
            $value = 'company';
        }

        $customerIds = $this->customersByColumn($column, $value);
        $commonIds = array_intersect($arrayOfCustmerIds, $customerIds);
        return $commonIds;
    }

    public function getOrdersTable(?string $value): void
    {
        $statement = "SELECT * FROM orders";

        if(!str_starts_with($value, 'all')) {
            $range = implode(",", $this->setValues());
            $statement = $statement . " WHERE ocid IN ({$range})";
        }

        if(str_contains($value, 'Activ')) {
            $statement = $statement . " WHERE ostatus = 'active'";
        }

        if(str_contains($value, 'Done')) {
            $statement = $statement . " WHERE ostatus = 'done'";
        }

        if(str_contains($value, 'Canc')) {
            $statement = $statement . " WHERE ostatus = 'canceled'";
        }

        if(str_ends_with($value, 'Desc')) {
            $statement = $statement . " ORDER BY id DESC";
        }


        $currentPageNumber = isset($_GET['page']) && !isset($_POST['chosen_list']) ? filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) : 1;

        /* results per page */
        if(!isset($_POST['per_page']) && isset($_GET['page'])) {
            if(isset($_GET['perpage']) && $_GET['perpage'] != 'all') {
                $isValidPerPage = $this->getPagination()->checkAllowedValues(clean($_GET['perpage']));
                $perPage = $isValidPerPage ? filter_input(INPUT_GET, 'perpage', FILTER_DEFAULT) : 10;
            } elseif(isset($_GET['perpage']) && $_GET['perpage'] == 'all') {
                $perPage = "all";
            } else {
                $perPage = 10;
            }
            $statement .= $this->getPagination()->rowsPerPage($perPage, $currentPageNumber);
        }

        if(!isset($_POST['per_page']) && !isset($_GET['page'])) {
            $perPage = 10;
            $statement .= $this->getPagination()->rowsPerPage($perPage, $currentPageNumber);
        }

        if(isset($_POST['per_page']) && $_POST['per_page'] != 'all') {
            $perPage = intval(filter_input(INPUT_POST, 'per_page', FILTER_DEFAULT));
            $statement .= $this->getPagination()->rowsPerPage($perPage, $currentPageNumber);
        }

        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {  // dobijamo listu svih narudzbina (orders)
            $singleOrder = $this->getOrderedArticlesByOrderId($result['id']); // list of all order_itmes in a single order
            $cardStatus = $this->getLoyaltyCard()->getCardByOrderId($result['id']);
            echo "<tr>";
            echo "<td class='textcentered'>{$result['id']}</td>";
            echo "<td>{$this->getColumnFromCustomerByOrderId("cname", $result['id'])}</td>";
            echo "<td class='textcentered'>";
            echo $cardStatus != false ? "<span class='has-lcard'>" . ucfirst($cardStatus["lcstatus"]) . "</span>" : "<strong>No</strong>";
            echo "</td>";
            echo "<td>";
            $priceArray = [];
            foreach($singleOrder as $anArticle) {
                echo "<dl>";
                $resultOfArticleId = $this->getArticleNameAndPriceFromId($anArticle['article_id']);
                array_push($priceArray, $resultOfArticleId['aprice'] * $anArticle['article_quantity']);
                echo "<dt>" . $resultOfArticleId['aname'] . " [x<span class='item_quantity'>" . $anArticle['article_quantity'] . "</span>]</dt>";
                echo "<dd>" . $anArticle['item_comment'] . "</dd>";
                echo "</dl>";
            }
            echo "</td>";
            echo "<td class='textcentered'>" . array_sum($priceArray) . "</td>";
            echo "<td>" . nl2br($result['ocomment']) . "</td>";
            echo "<td>{$result['odate']}</td>";
            $textBorder = "text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black; font-weight:bold;";
            switch($result['ostatus']) {
                case "active":
                    $addStyle = "style='{$textBorder} color:#B2FA79;'";
                    break;
                case "canceled":
                    $addStyle = "style='{$textBorder} color:#FFB4A3FF;'";
                    break;
                case "done":
                    $addStyle = "style='{$textBorder} color:lightgrey;'";
                    break;
            }
            echo "<td {$addStyle} class='textcentered'>{$result['ostatus']}</td>";
            echo "<td class='textcentered'><a href='editorder.php?editid={$result['id']}'>Edit</a></td>";
            echo "</tr>";
        }
    }

    /*
    * returns wanted column from customer table for a customer
    * who made the order. Searching by customer's id
    */
    public function getColumnFromCustomerByOrderId($column, $id): string
    {
        $statement = "SELECT {$column} FROM customers LEFT JOIN orders ON customers.id = orders.ocid WHERE orders.id = {$id}";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        return $query->fetch(PDO::FETCH_COLUMN);
    }

    /* returns an array that contains list of articles of a single order */
    public function getOrderedArticlesByOrderId(int $id): array
    {
        $statement = "SELECT * FROM order_items WHERE order_id = {$id}";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        $order = [];
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $order[] = $result;
        }
        return $order;
    }

    public function getAnArticlDetailsByItemId(int $id): array
    {
        $statement = "SELECT * FROM articles LEFT JOIN order_items ON articles.id = order_items.article_id  WHERE order_items.article_id = {$id}";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getArticleNameAndPriceFromId(int $id): array
    {
        $statement = "SELECT aname, aprice FROM articles WHERE id = {$id}";
        $queryForArticleId = $this->getDbh()->prepare($statement);
        $queryForArticleId->execute();
        return $queryForArticleId->fetch(PDO::FETCH_ASSOC);
    }

    public function customersByColumn($column, $value)
    {
        require 'customer.php';
        $statement = "SELECT id FROM customers WHERE {$column} = '{$value}'";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        $resultArray = [];
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $resultArray[] = $result['id'];
        }
        return $resultArray;
    }

    public function createOrder() {
        $this->filterAndUnsetEmptyPostVariable();
        $articleQty = [];
        $articleIds = [];  // ID-jevi svih proizvoda koji su submitovani u order-u
        $articleCommentsIds = [];
        $articleCommentsText = [];
        $this->orderComment = isset($_POST['orderComment']) ? clean($_POST['orderComment']) : NULL;

        foreach($_POST as $articleParam => $value) {
            if(!empty($value) && $value != '' && $value != 0) {
                if(str_contains($articleParam, 'customerName')) {
                    $customerId = clean($value);
                }

                if(str_contains(haystack: $articleParam, needle: "articleQuantity")) {
                    $articleIds[] = substr($articleParam, 15);
                    $articleQty[] = intval(clean($value));
                }

                if(str_contains(haystack: $articleParam, needle: "itemComment") && !empty($value)) {
                    $articleCommentsIds[] = substr($articleParam, 11);
                    $articleCommentsText[] = clean($value);
                }
            }
        }

        // checking if user chose any article or sent empty order
        if(empty($articleIds)) {
            $this->orderNotifications()->setUncategorizedError("ERROR: You need to choose at least one article to create an order!");
            die(header('location:../addorder.php'));
        }

        // checking if the article(s) ID(s) from $rowIds exist
        if(!$this->getArticle()->articleIdExists($articleIds)) {
            $this->orderNotifications()->setUnexistingArticle();
            die(header('location:../addorder.php'));
        }

        if(!isset($articleQty)) {
            $this->orderNotifications()->setCreateOrderQuantityError("ERROR: quantity of an article can't be zero");
            die(header('location:../addorder.php'));
        }

        $articlesWithQty = array_combine(keys: $articleIds, values: $articleQty);
        $articleComments = array_combine(keys: $articleCommentsIds, values: $articleCommentsText);

        $this->orderId = $this->insertOnlyOrder($customerId, $this->orderComment);
        $this->insertOnlyItems($articlesWithQty, $articleComments, $this->orderId) ?
            $this->orderNotifications()->setCreateOrderMessage(true) : $this->orderNotifications()->setCreateOrderMessage(false);
        header('location:../orders.php');
    }

    public function filterAndUnsetEmptyPostVariable(): void
    {
        foreach($_POST as $key => $value) {
            $_POST[$key] = is_numeric($value) ?
                 filter_var($value, FILTER_SANITIZE_NUMBER_INT) : filter_var($value, FILTER_DEFAULT);
            if(empty($value)) unset($_POST[$key]);
        }
    }

    public function insertOnlyOrder($customerId, $customerComment): int
    {
        $statementOrder = "INSERT INTO orders (ocid, ocomment) VALUES (:id, :ccomment)";
        $query = $this->getDbh()->prepare($statementOrder);
        $query->bindValue(':id', $customerId, PDO::PARAM_INT);
        $query->bindValue(':ccomment', $customerComment, PDO::PARAM_STR);
        $query->execute();
        $orderId = $this->orderId = $this->getDbh()->lastInsertId();

        if(!empty($orderId) && is_int($orderId)) {
            $this->orderId = $this->getDbh()->lastInsertId();
        } else {
            $this->orderNotifications()->setCreateOnlyOrderMessage(false);
            die(header('location:../addorder.php'));
        }

        return $orderId;
    }

    public function insertOnlyItems(array $articlesWithQty, array $articleComments, int $orderId): bool
    {
        $countElements = count($articlesWithQty);

        $statementValues = "VALUES";
        for($i = 0; $i < $countElements; $i++) {
            $statementValues .= "($orderId, :iid{$i}, :iqt{$i}, :acm{$i}),";
        }

        $statementValuesLenght = strlen($statementValues);
        $statementValues = substr($statementValues, 0, $statementValuesLenght -1);
        $statementAllItems = "INSERT INTO order_items (order_id, article_id, article_quantity, item_comment) " . $statementValues;
        $query = $this->getDbh()->prepare($statementAllItems);
        $x = 0;
        foreach($articlesWithQty as $itemId => $itemOrderQty) {
            if(empty($articleComments[$itemId])) {
                $articleComments[$itemId] = '';
            }
            $query->bindValue(":iid{$x}", $itemId, PDO::PARAM_INT);
            $query->bindValue(":iqt{$x}", $itemOrderQty, PDO::PARAM_INT);
            $query->bindValue(":acm{$x}", $articleComments[$itemId], PDO::PARAM_STR);
            $x++;
        }

        return (bool)$query->execute();
    }

    public function updateOrderedItem($articleId, $articleQuantity, $itemComment, $orderedItemId, $orderId)
    {
        $statement = "UPDATE order_items SET 
                       article_id = :aid, 
                       article_quantity = :aqu, 
                       item_comment  = :ico, 
                       oiupdated = CURRENT_TIMESTAMP 
                   WHERE id = {$orderedItemId} order_id = {$orderId}";
        $query = $this->getDbh()->prepare($statement);
        $query->bindValue(':aid', $articleId, PDO::PARAM_INT);
        $query->bindValue(':aqu', $articleQuantity, PDO::PARAM_INT);
        $query->bindValue(':ico', $itemComment, PDO::PARAM_STR);
        echo $query->execute() ? $orderedItemId . ' - USPEO UPIS' : $orderedItemId . " - NIJE USPEO";
    }

    public function updateAllOrderedItems(array $params, int $orderId): bool
    {
        foreach($params as $singleOrder) {
            // for inserting in HTML string as article ID
            if($singleOrder['itemId'] == "") {
                $this->orderNotifications()->setUncategorizedError("You tried to insert a fake ID");
                return false;
            }
            // for inserting in HTML numeric article ID that doesn't exist
            if(!$this->getArticle()->articleIdExists($singleOrder['itemId'])) {
                $this->orderNotifications()->setChosenUnexistingArticleError();
                return false;
            }
        }

        $statement = "INSERT INTO order_items (id, article_id, article_quantity, item_comment) VALUES";

        $countedParams = count($params);
        for($i = 0; $i < $countedParams; $i++) {
            $statement .= "(:rowId{$i}, :itemId{$i}, :itemQuantity{$i}, :itemComment{$i}),";
        }

        $statementLength = strlen($statement);
        $statement = substr($statement, 0, $statementLength - 1);
        $statement .= " ON DUPLICATE KEY UPDATE article_id = VALUES(article_id), article_quantity = VALUES(article_quantity), item_comment = VALUES(item_comment)";

        $query = $this->getDbh()->prepare($statement);

        for($i = 0; $i < $countedParams; $i++) {
            $query->bindValue(":rowId{$i}", $params[$i]['rowId'], PDO::PARAM_INT);
            $query->bindValue(":itemId{$i}", $params[$i]['itemId'], PDO::PARAM_INT);
            $query->bindValue(":itemQuantity{$i}", $params[$i]['itemQuantity'], PDO::PARAM_INT);
            $query->bindValue(":itemComment{$i}", $params[$i]['itemComment'], PDO::PARAM_STR);
        }

        return $query->execute();
    }

    public function updateGeneralOrderDetails(int $customerId, string $orderComment, string $orderStatus, int $orderId): bool
    {
        $statement = "UPDATE orders SET ocid = :ocid, ocomment = :ocomment, ostatus = :ostat, odate = CURRENT_TIMESTAMP  WHERE id = {$orderId}";
        $query = $this->getDbh()->prepare($statement);
        $query->bindValue(':ocid', $customerId, PDO::PARAM_INT);
        $query->bindValue(':ocomment', $orderComment, PDO::PARAM_STR);
        $query->bindValue(':ostat', $orderStatus, PDO::PARAM_STR);
        return (bool)$query->execute();
    }

//    public function getOrderItemById(int $id): array
//    {
//        $statement = "SELECT * FROM order_items  WHERE id = " . $id;
//        $sql = $this->getDbh()->prepare($statement);
//        $sql->execute();
//        return $sql->fetch(PDO::FETCH_ASSOC);
//    }

    public function addItemsToOrder(array $params, int $orderId): bool
    {
        if(empty($params)) return false;

        $statementGetItemsIds = "SELECT article_id FROM order_items WHERE order_id = {$orderId}";
        $queryGetItemsIds = $this->getDbh()->prepare($statementGetItemsIds);
        $queryGetItemsIds->execute();
        $previouslyOrderedItemsIds = [];
        while($result = $queryGetItemsIds->fetch(PDO::FETCH_ASSOC)) {
            $previouslyOrderedItemsIds[] = $result['article_id'];
        }

        $addedItemId = [];
        foreach($params as $singleAddedItem) {
            $addedItemId[] = $singleAddedItem[0];
        }

        // IDs of articles those already exist in the order
        $alreadyExistingIds = array_intersect($addedItemId, $previouslyOrderedItemsIds);
        // IDs of articles those don't exist in the current order
        $nonExistingIds = array_diff($addedItemId, $previouslyOrderedItemsIds);
        $allArticlesInOrder = $this->getOrderedArticlesByOrderId($orderId);

        /* Adding already existing items to the order */
        if(!empty($alreadyExistingIds)) {
            $newItemQuantity = "";
            $newItemComment = "";
            $oldItemQuantity = "";
            $oldItemComment = "";
            $orderItemRowId = "";

            // looping throw array of new values ($params)
            foreach($params as $key => $value) {
                // looking for ID that exists in array of existing values ($alreadyExistingIds)
                foreach($alreadyExistingIds as $key) {
                    if($value[0] == $key) {
                        $newItemQuantity = $value[1];
                        $newItemComment = $value[2];
                    }
                }

                foreach($allArticlesInOrder as $key) {
                    if($value[0] == $key['article_id']) {
                        $orderItemRowId = $key['id'];
                        $oldItemQuantity = $key['article_quantity'];
                        $oldItemComment = $key['item_comment'];
                    }
                }
                $quantityValue = $oldItemQuantity + $newItemQuantity;
                if(strlen($oldItemComment) > 2) {
                    if(str_contains($oldItemComment, "(x")) {
                        $commentValue = $oldItemComment . " | " . $newItemComment . " (x{$newItemQuantity})";
                    } else {
                        $commentValue = $oldItemComment . " (x{$oldItemQuantity}) | " . $newItemComment . " (x{$newItemQuantity})";
                    }
                } else {
                    $commentValue = $newItemComment . " (x{$newItemQuantity})";
                }

                $statementExisting = "UPDATE order_items SET article_quantity = :aqu, item_comment = :ico WHERE id = {$orderItemRowId}";
                $queryExisting = $this->getDbh()->prepare($statementExisting);
                $queryExisting->bindValue(":aqu", $quantityValue, PDO::PARAM_INT);
                $queryExisting->bindValue(":ico", $commentValue, PDO::PARAM_STR);

                if($queryExisting->execute()) {
                    $returnedValue = true;
                } else {
                    return false;
                }
            }
        }

        /* Adding new items to the order */
        if(!empty($nonExistingIds)) {
            foreach($params as $key => $value) {
                if(in_array($value[0], $nonExistingIds)) {
                    $statement = "INSERT INTO order_items (order_id, article_id, article_quantity, item_comment) VALUES ";
                    $statementValues = '';
                    $statementValues .= "(:oid, :aid{$key}, :aqu{$key}, :ico{$key}), ";
                    $statement .= $statementValues;
                    $statement = substr($statement, 0, strlen($statement) - 2);
                    $query = $this->getDbh()->prepare($statement);
                    $query->bindValue(':oid', $orderId, PDO::PARAM_INT);
                    $query->bindValue(':aid' . $key, $value[0], PDO::PARAM_INT);
                    $query->bindValue(':aqu' . $key, $value[1], PDO::PARAM_INT);
                    $query->bindValue(':ico' . $key, $value[2], PDO::PARAM_STR);
                    if($query->execute()) {
                        $returnedValue = true;
                    } else {
                        return false;
                    }
                }
            }
        }

        if(isset($returnedValue)) {
            return $returnedValue;
        } else {
            return false;
        }
    }

    public function checkUpdateDetails(int $orderId, string|int $key, string|int $value): void
    {
        if(str_contains($key, 'rowId') && $value == 0) {
            $this->orderNotifications()->setUpdateOrderDetailsError("ERROR: the order doesn't exist");
            die(header('location:../editorder.php?editid=' . $orderId));
        }

        if(str_contains($key, 'articleId') && $value == 0) {
            $this->orderNotifications()->setUpdateOrderDetailsError("ERROR: this article doesn't exist");
            die(header('location:../editorder.php?editid=' . $orderId));
        }

        if(str_contains($key, 'articleQuantity') && $value == 0) {
            $this->orderNotifications()->setUpdateOrderDetailsError("ERROR: quantity of an article can't be zero");
            die(header('location:../editorder.php?editid=' . $orderId));
        }
    }

    private function orderNotifications(): Notification
    {
        require_once 'notification.php';
        return new Notification();
    }

    public function orderIdExists(int $id): bool  // checks if an order with passed ID exists
    {
        $statement = "SELECT COUNT(*) FROM orders WHERE id = $id";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_COLUMN) > 0;
    }

    public function unexistingItemProcess(array $itemIds): void
    {
        $statement = "SELECT COUNT(id) FROM order_items WHERE id IN (" . implode(',', $itemIds) .")";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        if(count($itemIds) !== $sql->fetch(PDO::FETCH_COLUMN)) {
            $this->orderNotifications()->setChosenUnexistingArticleError();
            die(header('location:../orders.php'));
        }
    }

    public function orderStatusExists(string $status = null): bool|array
    {
        $statement = "SELECT DISTINCT ostatus FROM orders";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        $allStatuses = [];
        while($result = $sql->fetch(PDO::FETCH_ASSOC)) {
            $allStatuses[] = $result['ostatus'];
        }

        if($status != null) {
            return in_array($status, $allStatuses);
        } else {
            return $allStatuses;
        }
    }

    public function removeItemFromOrder(int $id) :bool
    {
        $statement = "DELETE FROM order_items WHERE id = $id";
        $sql = $this->getDbh()->prepare($statement);
        return $sql->execute();
    }
}