<?php
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.php';
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';
class Database
{
    private object $dbh;

    public function __construct(
        private string $host = DB_HOST,
        private string $dbName = DB_NAME,
        private string $user = DB_USER,
        private string $password = DB_PASSWORD,
    )
    {
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        try {
            $this->dbh = new PDO("mysql:host=$host;dbname=$dbName", $user, $password, $options);
        } catch(Exception $exception) {
            errorLog("Connection error: " . $exception->getMessage());
        }
    }

    /**
     * @return object
     */
    public function getDbh(): object
    {
        return $this->dbh;
    }

    public function relustsPerPage(): void
    {
        /* results per page */
        if(!isset($_POST['per_page']) && isset($_GET['page'])) {
            if(isset($_GET['perpage']) && $_GET['perpage'] != 'all') {
                $isValidPerPage = $this->getPagination()->checkAllowedValues(clean($_GET['perpage']));
                $perPage = $isValidPerPage ? filter_input(INPUT_GET, 'perpage', FILTER_DEFAULT) : 10;
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
    }
}