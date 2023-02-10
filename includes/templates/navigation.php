<div id="navigation-wrapper">

    <div class="button-holder">
        <button id="home-button" class="nav-button" onclick="document.location='/order_processing_system'">Home</button>
    </div>

    <?php
    $elements = ['customers', 'articles', 'orders', 'loyalty cards'];
    foreach($elements as $singleElement):
        $buttonTitle = ucfirst($singleElement);
        $elementName = substr($singleElement, 0, strlen($singleElement) - 1);
        $elementLink = str_replace(" ", "-", $singleElement) . ".php";
        $createLink = str_replace(" ", "", $elementName) . ".php";
        ?>

        <div class="button-holder">
            <button class="nav-button"><?= $buttonTitle; ?></button>
            <div class="nav-list">
                <a class="nav-link" href="<?= $elementLink; ?>">View all <?= $singleElement; ?></a>
                <a class="nav-link" href="add<?= $createLink; ?>">Create a new <?= $elementName; ?></a>
            </div>
        </div>

    <?php endforeach; ?>
</div>