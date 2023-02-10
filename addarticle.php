<?php
require_once "classes/article.php";
require_once "classes/notification.php";
require_once "includes/templates/header.php";

$message = new Notification();
$message->getAllArticleMessages();
?>

    <main class="form-main">
        <h2 class="textcentered">Add new article:</h2>
        <div class="form-wrapper">

            <form action="includes/createarticle.php" method="POST">
                <fieldset>
                    <legend>New article</legend>

                    <div class="input-item">
                        <label for="articleName">Name:</label>
                        <input type="text" id="articleName" name="articleName">
                    </div>

                    <div class="input-item">
                        <label for="articleStatus">Status:</label>
                        <select name="articleStatus" id="articleStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="articleType">Type:</label>
                        <select name="articleType" id="articleType">
                            <optgroup label="Food">
                                <?php
                                $aricle = new Article();
                                $articleTypes = $aricle->getArticleTypes();
                                foreach($articleTypes as $key => $value) {
                                    echo "<option value='{$value}'>" . ucfirst($value) . "</option>";
                                }
                                ?>
                            </optgroup>
                            <optgroup label="Beverage - inactive">
                                <option value="cold-drinks">Cold Drinks</option>
                                <option value="hot-drinks">Hot Drinks</option>
                                <option value="beers">Beers</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="articleCode">Code:</label>
                        <input type="text" id="articleCode" name="articleCode">
                    </div>

                    <div class="input-item">
                        <label for="articlePrice">Price:</label>
                        <input type="number" id="articlePrice" name="articlePrice" min=0 step=0.01>
                    </div>

                    <div class="input-item">
                        <label for="articleUnit">Unit:</label>
                        <input type="text" id="articleUnit" name="articleUnit">
                    </div>

                    <label for="comment">Comment:</label>
                    <textarea name="articleComment" id="comment" rows="5"></textarea><br>

                    <div class="input-item">
                        <input type="submit" name="create_article" value="create new article">
                    </div>
                </fieldset>
            </form>
        </div>

    </main>

<?php include_once("includes/templates/footer.php"); ?>