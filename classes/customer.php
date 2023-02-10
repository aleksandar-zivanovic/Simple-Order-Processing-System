<?php
require_once 'database.php';

class Customer extends database
{
    public string $customerId;
    public string $customerName;
    public string $customerType;
    public array $allowdCustomerTypes = ['person', 'company'];
    public string $customerComment;

    protected function getLoyaltyCard(): LoyaltyCard
    {
        require_once 'loyaltycard.php';
        return new LoyaltyCard();
    }

    protected function getNotification(): Notification
    {
        require_once 'notification.php';
        return new Notification();
    }

    public function getPagination(): Pagination
    {
        require_once 'pagination.php';
        return new Pagination();
    }

    public function getCustomerById(int $id): array|bool
    {
        $statement = "SELECT * FROM customers WHERE id = :cuid";
        $query = $this->getDbh()->prepare($statement);
        $query->bindValue(':cuid', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

//    public function countAllCustomers(): int
//    {
//        $statement = "SELECT COUNT(id) FROM customers";
//        $query = $this->getDbh()->prepare($statement);
//        $query->execute();
//        return $query->fetch(PDO::FETCH_COLUMN);
//    }

    public function getCustomers($value)
    {
        if(!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "person") {
            $value = "person";
        }

        if(!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "company") {
            $value = "company";
        }

        if(!isset($_POST['chosen_list']) && isset($_GET['sort']) && $_GET['sort'] === "haslc") {
            $value = "haslc";
        }

        $columnName = $this->columnName($value);
        $selectAll = "SELECT * FROM customers";
        $orderByIdDesc = " ORDER BY id DESC";

        /* setting $value if the order is chosen from dropdown menu */
        if(!isset($value) || $value != "data") {
            if(!isset($_POST['chosen_list']) && isset($_GET['page']) && isset($_GET['sort']) && $_GET['sort'] == "allDesc") {
                $value = "allDesc";
            }

            if(!isset($_POST['chosen_list']) && isset($_GET['page']) && !isset($_GET['sort'])) {
                $value = "allDesc";
            }

            if(!isset($_POST['chosen_list']) && isset($_GET['page']) && isset($_GET['sort']) && $_GET['sort'] == "allasc") {
                $value = "allasc";
            }
        }

        if($value != "haslc" && $value != "nolc") {
            if($value === "data" || $value === "allasc") {
                $statement = $selectAll;
            } else {
                $value === "allDesc" ? $statement = $selectAll . $orderByIdDesc : $statement = $selectAll . " WHERE {$columnName} = '{$value}'";
            }
        } elseif($value === "haslc") {
            $statement = $selectAll . " c INNER JOIN loyalty_card lc ON c.id = lc.lccid";
        } else {
            $statement = $selectAll . " c LEFT JOIN loyalty_card lc ON c.id = lc.lccid WHERE lc.id IS NULL";
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

        if(is_numeric($value)) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }

        /* returns array of all customers */
        if($value === "data") {
            $allCustomers = [];
            while($result = $query->fetch(PDO::FETCH_ASSOC)) {
                $allCustomers[] = $result;
            }
            return $allCustomers;
        }

        $this->customersTable($query, $value);
    }

    private function columnName(string $value)
    {
        if(is_numeric($value)) return "id";
        if(in_array($value, ['person', 'company'])) return "ctype";
        if(in_array($value, ['haslc', 'nolc'])) return "ostatus";
    }

    public function customersTable($query, $value) {
        if($value != "haslc" && $value != "nolc") {
            $allCards = $this->getLoyaltyCard()->getAllCardsDetails();
        }
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            if($value != "haslc" && $value != "nolc") {
                $carStatusColumn = "UNDEFINED!";
                foreach($allCards as $singleCard) {
                    if($result['id'] === $singleCard['lccid']) {
                        $lCardStatus = $singleCard['lcstatus'];
                        $hasLCard = " class='has-lcard textcentered' ";
                        $carStatusColumn = "<td{$hasLCard} class='textcentered'>{$lCardStatus}</td>";
                        break;
                    } else {
                        $lCardStatus = "NE";
                        $hasLCard = "";
                        $carStatusColumn = "<td{$hasLCard} class='textcentered'>{$lCardStatus}</td>";
                    }
                }
            } else {
                if($value === "haslc") $carStatusColumn = cardStatusShow($result);
                if($value === "nolc") {
                    $lCardStatus = "NE";
                    $hasLCard = "";
                    $carStatusColumn = "<td{$hasLCard} class='textcentered'>{$lCardStatus}</td>";
                }
            }

            echo "<tr>";
            echo "<td id='{$result['id']}' class='textcentered'>{$result['id']}</td>";
            echo "<td>{$result['cname']}</td>";
            echo "<td>{$result['ctype']}</td>";
            echo $carStatusColumn;
            echo "<td>{$result['ccomment']}</td>";
            echo "<td>{$result['ccreated']}</td>";
            echo "<td>{$result['cupdated']}</td>";
            echo "<td class='textcentered'><a href='editcustomer.php?editid={$result['id']}'>Edit</a></td>";
            echo "</tr>";
        }
    }

    protected function cleanPostValues(): void
    {
        $this->customerName = clean(filter_input(INPUT_POST, 'customerName', FILTER_DEFAULT));
        $this->customerType = clean(filter_input(INPUT_POST, 'customerType', FILTER_DEFAULT));
        $this->customerComment = clean(filter_input(INPUT_POST, 'customerComment', FILTER_DEFAULT));

        if($this->checkCustomerType($this->customerType) !== true) {
            $errorMessage = "ERROR: Entered unrecognized customer type!";
            die(header("location:" . $this->getNotification()->setUncategorizedError($errorMessage)));
        }
    }

    protected function bindCustomerValues($query): void
    {
        $query->bindValue(":cn", $this->customerName, PDO::PARAM_STR);
        $query->bindValue(":ct", $this->customerType, PDO::PARAM_STR);
        $query->bindValue(":cc", $this->customerComment, PDO::PARAM_STR);
    }

    public function createCustomer(): int
    {
        $this->cleanPostValues();
        $dbh = $this->getDbh();
        $statement = "INSERT INTO customers (cname, ctype, ccomment) VALUES(:cn, :ct, :cc)";
        $query = $dbh->prepare($statement);
        $this->bindCustomerValues($query);

        return $query->execute() ? $this->getDbh()->lastInsertId() : 0;
    }

    public function updateCustomer(): bool
    {
        $this->customerId = clean(filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_NUMBER_INT));
        $this->cleanPostValues();
        $dbh = $this->getDbh();
        $statement = "UPDATE customers SET 
                         cname = :cn, 
                         ctype = :ct,
                         ccomment = :cc, 
                         cupdated = CURRENT_TIMESTAMP 
                         WHERE id = {$this->customerId}";
        $query = $dbh->prepare($statement);
        $this->bindCustomerValues($query);

        return (bool)$query->execute();
    }

    protected function checkCustomerType($type): bool
    {
        return in_array($type, $this->allowdCustomerTypes) ? true : false;
    }
}