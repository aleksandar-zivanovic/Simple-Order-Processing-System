<?php
require_once 'database.php';

class Article extends Database
{
    public string $articleId;
    public string $articleType;
    public array $articleTypes;
    public string $articleName;
    public string $articleCode;
    public string $articlePrice;
    public string $articleUnit;
    public string $articleComment;
    public string $articleStatus;

    public function tableElements ($result) {
        echo "<td class='textcentered'>{$result['id']}</td>";
        echo "<td>{$result['atype']}</td>";
        echo "<td>{$result['aname']}</td>";
        echo "<td>{$result['acode']}</td>";
        echo "<td>{$result['aprice']}</td>";
        echo "<td>{$result['aunit']}</td>";
        echo "<td>{$result['acomment']}</td>";
        echo "<td>{$result['acreated']}</td>";
        echo "<td>{$result['aupdated']}</td>";
        echo "<td><a href='editarticle.php?editid={$result['id']}'>Edit</a></td>";
    }

    /**
     ** $value is a value from a column | allowed column name or null
     ** $column is a column from articles table | allowed table name or null
     ** $output is data when it returns array of data and table when it returns table elements | data or table
     ** $order is ORDER BY part of statement and can be null or order by ending statement | allowed "ORDER BY ..." or null
     * */
    public function getAllArticles(string|int|null $value, string|null $column, string $output, ?string $order)
    {
        $dbh = $this->getDbh();
        $statement = "SELECT * FROM articles";

        if($column == "astatus") {
            if($value == "allAsc" || $value == "allDesc") {
                $value = "all";
            }

            if($value == "inactiveAsc" || $value == "inactiveDesc") {
                $value = "inactive";
            }

            if($value == "activeAsc" || $value == "activeDesc") {
                $value = "active";
            }
        }

        // for table output when a column and value specified
        if($output == "table" && $value != null && $value != 'all' && $column != null) $statement .= " WHERE $column = '{$value}'";

        // for data output when column and its numeric values are specified
        if($output == "data" && is_numeric($value) && $column != null) $statement .= " WHERE id = {$value}";

        // adding ORDER BY to the statement
        $order == null || "" ? "" : $statement .= " " . $order;

        $currentPageNumber = isset($_GET['page']) && !isset($_POST['chosen_list']) ? filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) : 1;

        // results per page
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

        $query = $dbh->prepare($statement);
        $query->execute();

        // returns data array of all articles
        if($output == "data" && $value == null && $column == null) {
            $allArticles = [];
            while($result = $query->fetch(PDO::FETCH_ASSOC)) {
                $allArticles[] = $result;
            }
            return $allArticles;
        }

        // returns data array of a column with a numeric value
        if($output == "data" && is_numeric($value) && $column != null) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }

        // returns table elements those contain articles (used only for articles.php)
        if($output == "table" && $value != null && $column == 'astatus') {
            while($result = $query->fetch(PDO::FETCH_ASSOC)) {
                $result['astatus'] === 'inactive' ? $markInactive = " class='inactive-article' " : $markInactive = '';
                echo "<tr{$markInactive}>";
                $this->tableElements($result);
                echo "</tr>";
            }
        }
    }

    protected function cleanPostValues()
    {
        $this->articleName = clean(filter_input(INPUT_POST, 'articleName', FILTER_DEFAULT));
        $this->articleStatus = clean(filter_input(INPUT_POST, 'articleStatus', FILTER_DEFAULT));
        $this->articleType = clean(filter_input(INPUT_POST, 'articleType', FILTER_DEFAULT));
        $this->articleCode = clean(filter_input(INPUT_POST, 'articleCode', FILTER_DEFAULT));
        $this->articlePrice = clean(filter_input(INPUT_POST, 'articlePrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $this->articleUnit = clean(filter_input(INPUT_POST, 'articleUnit', FILTER_DEFAULT));
        $this->articleComment = clean(filter_input(INPUT_POST, 'articleComment', FILTER_DEFAULT));

        if(!$this->checkArticleType(clean($_POST['articleType']))) {
            $this->articleNotifications()->setUnexistingArticleType();
            die(header('location:../articles.php'));
        }
    }

    protected function bindArticleValues($query) {
        $query->bindValue(":ty", $this->articleType, PDO::PARAM_STR);
        $query->bindValue(":na", $this->articleName, PDO::PARAM_STR);
        $query->bindValue(":cd", $this->articleCode, PDO::PARAM_STR);
        $query->bindValue(":pr", $this->articlePrice, PDO::PARAM_STR);
        $query->bindValue(":un", $this->articleUnit, PDO::PARAM_STR);
        $query->bindValue(":cm", $this->articleComment, PDO::PARAM_STR);
        $query->bindValue(":st", $this->articleStatus, PDO::PARAM_STR);
}

    public function createArticle()
    {
        $this->cleanPostValues();

        $dbh = $this->getDbh();
        $statement = "INSERT INTO articles (atype, aname, acode, aprice, aunit, acomment, astatus) VALUES (:ty, :na, :cd, :pr, :un, :cm, :st)";
        $query = $dbh->prepare($statement);
        $this->bindArticleValues($query);

        if($query->execute()) {
            $this->articleNotifications()->setCreateArticleMessage(true);
        } else {
            $this->articleNotifications()->setCreateArticleMessage(false);
        }
        header('location:../articles.php');
    }

    public function updateArticle() {
        $this->cleanPostValues();
        $this->articleId = clean(filter_input(INPUT_POST, 'articleId', FILTER_SANITIZE_NUMBER_INT));
        $dbh = $this->getDbh();
        $statement = "UPDATE articles SET 
                          atype = :ty, 
                          aname = :na, 
                          acode = :cd, 
                          aprice = :pr, 
                          aunit = :un, 
                          acomment = :cm, 
                          astatus = :st
                          WHERE id = {$this->articleId}";
        $query = $dbh->prepare($statement);
        $this->bindArticleValues($query);

        if($query->execute()) {
            $this->articleNotifications()->setUpdateArticleMessage(true);
            header("location:../editarticle.php?editid={$this->articleId}");
        } else {
            $this->articleNotifications()->setUpdateArticleMessage(false);
            header("location:../editarticle.php?editid={$this->articleId}");
        }
    }

    public function getArticleTypes(): array
    {
        $this->articleTypes = [];
        $statement = "SELECT DISTINCT atype FROM articles";
        $query = $this->getDbh()->prepare($statement);
        $query->execute();
        while($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $this->articleTypes[] = $result['atype'];
        }
        return $this->articleTypes;
    }

    public function checkArticleType(string $type): bool
    {
        $existingTypes = $this->getArticleTypes();
        return in_array($type, $existingTypes);
    }

    public function articleNotifications()
    {
        require_once 'notification.php';
        return new Notification();
    }

    public function getPagination(): Pagination
    {
        require_once 'pagination.php';
        return new Pagination();
    }

    public function articleIdExists(int|array $id): bool
    {
        if(is_numeric($id)) {
            $statement = "SELECT COUNT(*) FROM articles WHERE id = {$id}";
            $sql = $this->getDbh()->prepare($statement);
            $sql->execute();
            return $sql->fetch(PDO::FETCH_COLUMN) > 0;
        }

        if(is_array($id)) {
            $countInput = count($id);
            $statement = "SELECT COUNT(id) FROM articles WHERE id IN (" . implode(',', $id) . ")";
            $sql = $this->getDbh()->prepare($statement);
            $sql->execute();
            return $countInput === $sql->fetch(PDO::FETCH_COLUMN);
        }

         return false;
    }
}