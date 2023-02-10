<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';

class Notification
{
    public function getMessage(): void
    {
        // general message
    }

    private function success(string $text): string
    {
        return "<p class='success'>{$text}</p>";
    }

    private function fail(string $text): string
    {
        return "<p class='fail'>{$text}</p>";
    }


    public function setDataMissingOrInvalid(): void
    {
        $_SESSION['msgDataMissingOrInvalid'] = $this->fail("ERROR: You didn't enter all data or the data is not valid!");
    }

    public function getDataMissingOrInvalid(): void
    {
        if(isset($_SESSION['msgDataMissingOrInvalid'])) {
            echo $_SESSION['msgDataMissingOrInvalid'];
            unset($_SESSION['msgDataMissingOrInvalid']);
        }
    }

    public function getAllCustomerMessages(): void
    {
        $this->getCreateCustomerMessage();
        $this->getUpdateCustomer();
        $this->getCustomerNotFoundError();
    }

    public function setCreateCustomerMessage(string $customerName, bool $result): void
    {
        if($result == true) {
            $_SESSION['msgCreateCustomer'] = $this->success($customerName . " is successfully created!");
        } else {
            $_SESSION['msgCreateCustomer'] = $this->fail("ERROR: {$customerName} isn't created!");
        }
    }

    public function getCreateCustomerMessage(): void
    {
        if(isset($_SESSION['msgCreateCustomer'])) {
            echo $_SESSION['msgCreateCustomer'];
            unset($_SESSION['msgCreateCustomer']);
        }

        $this->getDataMissingOrInvalid();
    }

    public function setUpdateCustomerDetails(string $customerName, bool $result): void
    {
        if($result === true) {
            $_SESSION['msgUpdateCustomer'] = $this->success($customerName . "'s details are updated!");
        } else {
            $_SESSION['msgUpdateCustomer'] = $this->fail("ERROR: {$customerName}'s details aren't updated!");
        }
    }

    public function getUpdateCustomer(): void
    {
        if(isset($_SESSION['msgUpdateCustomer'])) {
            echo $_SESSION['msgUpdateCustomer'];
            unset($_SESSION['msgUpdateCustomer']);
        }

        $this->getDataMissingOrInvalid();
    }

    public function setCustomerNotFoundError(): void
    {
        $_SESSION['msgCustomerNotFound'] = $this->fail("ERROR: Customer with provided ID doesn't exist!");
    }

    public function getCustomerNotFoundError(): void
    {
        if(isset($_SESSION['msgCustomerNotFound'])) {
            echo $_SESSION['msgCustomerNotFound'];
            unset($_SESSION['msgCustomerNotFound']);
        }
    }

    public function getAllArticleMessages(): void
    {
        $this->getCreateArticleMessage();
        $this->getUnexistingArticleType();
        $this->getDataMissingOrInvalid();
        $this->getUpdateArticleMessage();
        $this->getUnexistingArticle();
    }

    public function setCreateArticleMessage(bool $result): void
    {
        if($result) {
            $_SESSION['msgCreateArticle'] = $this->success(clean(filter_input(INPUT_POST, 'articleName', FILTER_DEFAULT)) . " is created!");
        }

        if((!$result)) {
            $_SESSION['msgCreateArticle'] = $this->fail(clean(filter_input(INPUT_POST, 'articleName', FILTER_DEFAULT)) . " isn't created!");
        }
    }

    public function getCreateArticleMessage(): void
    {
        if(isset($_SESSION['msgCreateArticle'])) {
            echo $_SESSION['msgCreateArticle'];
            unset($_SESSION['msgCreateArticle']);
        }
    }

    public function setUnexistingArticle(): void
    {
        $_SESSION['msgUnexistingArticle'] = $this->fail("ERROR: You chose article that doesn't exist in our base!");
    }

    public function getUnexistingArticle(): void
    {
        if(isset($_SESSION['msgUnexistingArticle'])) {
            echo $_SESSION['msgUnexistingArticle'];
            unset($_SESSION['msgUnexistingArticle']);
        }
    }

    public function setUnexistingArticleType(): void
    {
        $_SESSION['msgUnexistingArticleType'] = $this->fail("Selected article type is not available");
    }

    public function getUnexistingArticleType(): void
    {
        if(isset($_SESSION['msgUnexistingArticleType'])) {
            echo $_SESSION['msgUnexistingArticleType'];
            unset($_SESSION['msgUnexistingArticleType']);
        }
    }

    public function setUpdateArticleMessage(bool $result): void
    {
        if($result) {
            $_SESSION['msgUpdateArticle'] = $this->success(clean(filter_input(INPUT_POST, 'articleName', FILTER_DEFAULT)) . " is updated!");
        }

        if((!$result)) {
            $_SESSION['msgUpdateArticle'] = $this->fail(clean(filter_input(INPUT_POST, 'articleName', FILTER_DEFAULT)) . " isn't updated!");
        }
    }

    public function getUpdateArticleMessage(): void
    {
        if(isset($_SESSION['msgUpdateArticle'])) {
            echo $_SESSION['msgUpdateArticle'];
            unset($_SESSION['msgUpdateArticle']);
        }
    }

    public function getAllOrderMessages(): void
    {
        $this->getCreateOrderMessage();
        $this->getCreateOnlyOrderMessage();
        $this->getCreateOrderQuantityError();
        $this->getUpdateOrderMessage();
        $this->getDataMissingOrInvalid();
        $this->getUpdateOrderDetailsError();
        $this->getChosenUnexistingArticleError();
        $this->getNotChosenAnyItemError();
        $this->getUnexistingOrderStatusError();
        $this->getUncategorizedError();
    }

    public function setCreateOrderMessage(bool $result): void
    {
        if($result) {
            $_SESSION['msgCreateOrder'] = $this->success("The order is created!");
        }

        if((!$result)) {
            $_SESSION['msgCreateOrder'] = $this->fail("ERROR: The order is created, but without articles!");
        }
    }

    public function getCreateOrderMessage(): void
    {
        if(isset($_SESSION['msgCreateOrder'])) {
            echo $_SESSION['msgCreateOrder'];
            unset($_SESSION['msgCreateOrder']);
        }
    }

    public function setCreateOnlyOrderMessage(bool $result): void
    {
        if((!$result)) {
            $_SESSION['msgCreateOnlyOrder'] = $this->fail("ERROR: The order isn't created!");
        }
    }

    public function getCreateOnlyOrderMessage(): void
    {
        if(isset($_SESSION['msgCreateOnlyOrder'])) {
            echo $_SESSION['msgCreateOnlyOrder'];
            unset($_SESSION['msgCreateOnlyOrder']);
        }
    }

    public function setCreateOrderQuantityError(): void
    {
        $_SESSION['msgCreateOrderQuantityError'] = $this->fail("ERROR: quantity of an article can't be zero");
    }



    public function getCreateOrderQuantityError(): void
    {
        if(isset($_SESSION['msgCreateOrderQuantityError'])) {
            echo $_SESSION['msgCreateOrderQuantityError'];
            unset($_SESSION['msgCreateOrderQuantityError']);
        }
    }

    public function setUpdateOrderMessage(bool $result): void
    {
        $result ?
            $_SESSION['msgUpdateOrder'] = $this->success("The order is updated!") :
            $_SESSION['msgUpdateOrder'] = $this->fail("ERROR: The order isn't updated!");
    }

    public function getUpdateOrderMessage(): void
    {
        if(isset($_SESSION['msgUpdateOrder'])) {
            echo $_SESSION['msgUpdateOrder'];
            unset($_SESSION['msgUpdateOrder']);
        }
    }

    public function setUpdateOrderDetailsError($errorMessage): void
    {
        $_SESSION['msgOrderDetailsError'] = $this->fail($errorMessage);
    }

    public function getUpdateOrderDetailsError(): void
    {
        if(isset($_SESSION['msgOrderDetailsError'])) {
            echo $_SESSION['msgOrderDetailsError'];
            unset($_SESSION['msgOrderDetailsError']);
        }
    }

    public function setChosenUnexistingArticleError(): void
    {
        $_SESSION['msgChosenUnexistingArticleError'] = $this->fail("ERROR: You chose some article that doesn't exist in our base!");
    }


    public function getChosenUnexistingArticleError(): void
    {
        if(isset($_SESSION['msgChosenUnexistingArticleError'])) {
            echo $_SESSION['msgChosenUnexistingArticleError'];
            unset($_SESSION['msgChosenUnexistingArticleError']);
        }
    }

    public function setNotChosenAnyItemError($errorMessage): void
    {
        $_SESSION['msgNotChosenAnyItemError'] = $this->fail($errorMessage);
    }

    public function getNotChosenAnyItemError(): void
    {
        if(isset($_SESSION['msgNotChosenAnyItemError'])) {
            echo $_SESSION['msgNotChosenAnyItemError'];
            unset($_SESSION['msgNotChosenAnyItemError']);
        }
    }

    public function getUnexistingOrderStatusError(): void
    {
        if(isset($_SESSION['msgUnexistingOrderStatusError'])) {
            echo $_SESSION['msgUnexistingOrderStatusError'];
            unset($_SESSION['msgUnexistingOrderStatusError']);
        }
    }

    public function setUnexistingOrderStatusError(): void
    {
        $_SESSION['msgUnexistingOrderStatusError'] = $this->fail("ERROR: You chose unexisting status for the order");
    }

    public function getAllCardMessages(): void
    {
        $this->getSwitchCardStatus();
        $this->getDataMissingOrInvalid();
        $this->getUncategorizedError();
        $this->getCreateCardMessage();
        $this->getUpdateCardDetails();
    }

    public function setSwitchCardStatus($value, $customerName, $newStatus): void
    {
        if($value == true) {
            $_SESSION['msgSwitchCardStatus'] = $this->success("SUCCESS: {$customerName} status is changed to {$newStatus}!");
        } else {
            $_SESSION['msgSwitchCardStatus'] = $this->fail("ERROR: {$customerName} status ISN'T changed!");
        }
    }

    public function getSwitchCardStatus(): void
    {
        if(isset($_SESSION['msgSwitchCardStatus'])) {
            echo $_SESSION['msgSwitchCardStatus'];
            unset($_SESSION['msgSwitchCardStatus']);
        }
    }

    public function setCreateCardMessage(bool $result, string $customerName): void
    {
        $_SESSION['msgCreateCard'] = ($result) ?
            $this->success("Loyalty card for <i>{$customerName}</i> is successfully created!") :
            $this->fail("ERROR: Loyalty card for <i>{$customerName}</i> isn't created!");
    }

    public function getCreateCardMessage(): void
    {
        if(isset($_SESSION['msgCreateCard'])) {
            echo $_SESSION['msgCreateCard'];
            unset($_SESSION['msgCreateCard']);
        }
    }

    public function setUpdateCardDetails(bool $result): void
    {
        if($result === true) {
            $_SESSION['msgUpdateCardDetails'] = $this->success("Loyalty card details are updated!");
        } else {
            $_SESSION['msgUpdateCardDetails'] = $this->fail("ERROR: Loyalty card details aren't updated!");
        }
    }

    public function getUpdateCardDetails(): void
    {
        if(isset($_SESSION['msgUpdateCardDetails'])) {
            echo $_SESSION['msgUpdateCardDetails'];
            unset($_SESSION['msgUpdateCardDetails']);
        }

        $this->getDataMissingOrInvalid();
    }

    public function setUncategorizedError($errorMessage): void
    {
        $_SESSION['msgUncategorizedError'] = $this->fail($errorMessage);
    }

    public function getUncategorizedError(): void
    {
        if(isset($_SESSION['msgUncategorizedError'])) {
            echo $_SESSION['msgUncategorizedError'];
            unset($_SESSION['msgUncategorizedError']);
        }
    }
}