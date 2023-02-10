<?php
require_once 'database.php';

class LoyaltyCard extends Database
{
    public int $id;
    public string $status;
    public string $comment;
    public array $allowedStatuses = ['active', 'inactive',  'removed'];
    public int $customerId;

    private function getNotification()
    {
        require_once 'notification.php';
        return new Notification();
    }

    private function getCustomer(): Customer
    {
        require_once 'customer.php';
        return new Customer();
    }

    public function getPagination(): Pagination
    {
        require_once 'pagination.php';
        return new Pagination();
    }

    public function getCardById(int $customerId): array|false
    {
        $statement = "SELECT * FROM loyalty_card WHERE lccid = $customerId";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function getCardByOrderId(int $orderId): array|false
    {
        $statement = "SELECT * FROM loyalty_card lc LEFT JOIN orders o ON lc.lccid = o.ocid WHERE o.ocid = $orderId";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCardsDetails(): array
    {
        $statement = "SELECT * FROM loyalty_card";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        $allCards = [];
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $allCards[] = $result;
        }
        return $allCards;
    }

    public function getLoyaltyCardsTable(?string $value): void
    {
        $statement = "SELECT * FROM loyalty_card";

        /* taking $value from $_GET when there is no $_POST value */
        if(empty($_POST) && isset($_GET['sort'])) $value = clean(filter_input(INPUT_GET, 'sort', FILTER_DEFAULT));
        if(!str_starts_with($value, 'all') && !str_contains($value, 'company') && !str_contains($value, 'person')) {
            $range = implode(",", $this->setValues());
            $statement = $statement . " WHERE ocid IN ({$range})";
        }

        if(str_contains($value, 'Act')) {
            $statement = $statement . " WHERE lcstatus = 'active'";
        }

        if(str_contains($value, 'Inact')) {
            $statement = $statement . " WHERE lcstatus = 'inactive'";
        }

        if(str_contains($value, 'Rem')) {
            $statement = $statement . " WHERE lcstatus = 'removed'";
        }

        if(str_contains($value, 'company')) {
            $statement = $statement . " lc LEFT JOIN customers c ON lc.lccid = c.id  WHERE c.ctype = 'company'";
        }

        if(str_contains($value, 'person')) {
            $statement = $statement . " lc LEFT JOIN customers c ON lc.lccid = c.id  WHERE c.ctype = 'person'";
        }

        /* setting descending order */
        if(str_ends_with($value, 'Desc')) {
            $statement .= str_contains($value, 'company') || str_contains($value, 'person') ? " ORDER BY lc.id DESC" : " ORDER BY id DESC";
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
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {  // list of all l. cards
            if($value != "person" && $value != "company") { // list of all customers with a l. card for the search criteria
                $singleCard = $this->getCustomerByCardId($result['id']);
            } else {
                $singleCard = $this->getCustomer()->getCustomerById($result['lccid']);
            }
            if($singleCard) {
                echo "<tr>";
                echo "<td class='textcentered' id='lcid{$result['id']}'>{$result['id']}</td>";
                echo "<td><a class='linkNoDecoration' href='editcustomer.php?editid={$result['lccid']}' target='_blank'>{$result['lccid']}. {$singleCard['cname']} ({$singleCard['ctype']})</a></td>";
                echo cardStatusShow($result);
                echo "<td>{$result['lccomment']}</td>";
                echo "<td>{$result['lccreated']}</td>";
                echo "<td>{$result['lcupdated']}</td>";
                $active = $result['lcstatus'] == "active" ? "selected" : "";
                $inactive = $result['lcstatus'] == "inactive" ? "selected" : "";
                $removed = $result['lcstatus'] == "removed" ? "selected" : "";
                echo "<td class='textcentered'>
                        <form method='post' action=''>
                            <select name='lcstatus{$result['id']}' id='lcstatus{$result['id']}' onchange='this.form.submit()'>
                                <option value='active' $active>Active</option>
                                <option value='inactive' $inactive>Inactive</option>
                                <option value='removed' $removed>Removed</option>
                            </select>
                        </form>
                      </td>";
                echo "<td class='textcentered'><a href='editloyaltycard.php?editid={$result['id']}'>Edit</a></td>";
                echo "</tr>";

                if(isset($_POST["lcstatus" . $result['id']])) {
                    if($this->changeCardStatus($result['id'])) {
                        $newStatus = clean(filter_input(INPUT_POST, "lcstatus" . $result['id'], FILTER_DEFAULT));
                        $this->getNotification()->setSwitchCardStatus(true, $singleCard['cname'], $newStatus);
                        header("location:loyalty-cards.php#lcid{$result['id']}");
                    } else {
                        $this->getNotification()->setSwitchCardStatus(false);
                    }
                }
            }
        }
    }

    public function getCustomerByCardId(int $id): array|false
    {
        $statement = "SELECT * FROM customers c LEFT JOIN loyalty_card lc ON c.id = lc.lccid WHERE lc.id = $id";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCustomersWithCards(): array
    {
        $statement = "SELECT lccid FROM loyalty_card";
        $sql = $this->getDbh()->prepare($statement);
        $sql->execute();
        $ids = [];
        while($result = $sql->fetch(PDO::FETCH_COLUMN)) {
            $ids[] = $result;
        }
        return $ids;
    }

    public function getCardByUserId(int $customerId): array|false
    {
        $statement = "SELECT * FROM loyalty_card WHERE lccid = {$customerId}";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function changeCardStatus(int $id): bool
    {
        $this->status = clean(filter_input(INPUT_POST, "lcstatus" . $id, FILTER_DEFAULT));
        $statement = "UPDATE loyalty_card SET lcstatus = :lcstat WHERE id = " . $id;
        $sql = $this->getDbh()->prepare($statement);
        $sql->bindValue(':lcstat', $this->status, PDO::PARAM_STR);
        return (bool)$sql->execute();
    }

    public function createLoyaltyCard(null|bool $escapeCleanPostValues = null): int
    {
        if($escapeCleanPostValues == null) $this->cleanPostValues();
        $this->createCardErrorFilters();
        $dbh = $this->getDbh();
        $statement = "INSERT INTO loyalty_card (lccid, lcstatus, lccomment) VALUES(:cid, :cst, :lcc)";
        $query = $dbh->prepare($statement);
        $this->bindCardValues($query);
        return $query->execute() ? $this->getDbh()->lastInsertId() : 0;
    }

    protected function cleanPostValues(): void
    {
        $this->customerId = clean(filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT));
        $this->status = clean(filter_input(INPUT_POST, 'cardStatus', FILTER_DEFAULT));
        $this->comment = clean(filter_input(INPUT_POST, 'lcComment', FILTER_DEFAULT));
    }

    /* Errors those happen only if HTML is changed by a user */
    protected function createCardErrorFilters(): void
    {
        if($this->checkCardType($this->status) !== true) {
            $this->getNotification()->setUncategorizedError("ERROR: Entered loyalty card status that doesn't exist!");
            die(header("location:../addloyaltycard.php"));
        }

        if(in_array($this->customerId, $this->getAllCustomersWithCards())) {
            $this->getNotification()->setUncategorizedError("ERROR: Entered customer that already has loyalty card!");
            die(header("location:../addloyaltycard.php"));
        }

        if(!$this->getCustomer()->getCustomerById($this->customerId)) {
            $this->getNotification()->setUncategorizedError("ERROR: Entered a customer that doesn't exist!");
            die(header("location:../addloyaltycard.php"));
        }
    }

    protected function bindCardValues($query): void
    {
        $query->bindValue(":cid", $this->customerId, PDO::PARAM_INT);
        $query->bindValue(":cst", $this->status, PDO::PARAM_STR);
        $query->bindValue(":lcc", $this->comment, PDO::PARAM_STR);
    }

    protected function checkCardType($type): bool
    {
        return in_array($type, $this->allowedStatuses);
    }

    public function updateCard(): bool
    {
        $statement = "UPDATE loyalty_card SET lccid = :cid, lcstatus = :cst, lccomment  = :lcc WHERE id = {$this->id}";
        $query = $this->getDbh()->prepare($statement);
        $this->cleanPostValues();
        $this->bindCardValues($query);
        return $query->execute();
    }

}