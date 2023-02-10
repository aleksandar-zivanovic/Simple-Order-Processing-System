<?php
!isset($_GET['editid']) ? die(header("location:articles.php")) : "";
require_once "classes/article.php";
require_once "classes/notification.php";
$javaScript = "include";
require_once "includes/templates/header.php";

$articleIdFromGet = clean($_GET['editid']);
$message = new Notification();
$message->getAllArticleMessages();
$article = new Article();
if(!is_numeric($articleIdFromGet) || !$article->articleIdExists($articleIdFromGet)) {
    die(header("location:articles.php"));
}
$articleById = $article->getAllArticles($articleIdFromGet, 'id', "data", null);
?>

    <main class="form-main">
        <h2 class="textcentered">Edit article detals:</h2>
        <div class="form-wrapper">
            <form action="includes/updatearticle.php" method="POST">
                <fieldset>
                    <legend>Update article details</legend>

                    <input type="number" id="articleId" name="articleId" value="<?= $articleById['id']; ?>" hidden>

                    <div class="input-item">
                        <label for="articleName">Name:</label>
                        <input type="text" id="articleName" name="articleName" value="<?= $articleById['aname']; ?>">
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
                                    echo "<option value='{$value}' id='{$value}'>" . ucfirst($value) . "</option>";
                                }
                                ?>
                            </optgroup>
                            <optgroup label="Beverage - inactive">
                                <option value="cold-drinks" id="cold-drinks">Cold Drinks</option>
                                <option value="hot-drinks" id="hot-drinks">Hot Drinks</option>
                                <option value="beers" id="beers">Beers</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="input-item">
                        <label for="articleCode">Code:</label>
                        <input type="text" id="articleCode" name="articleCode" value="<?= $articleById['acode']; ?>">
                    </div>

                    <div class="input-item">
                        <label for="articlePrice">Price:</label>
                        <input type="number" id="articlePrice" name="articlePrice" min=0 step=0.01
                               value="<?= $articleById['aprice']; ?>">
                    </div>

                    <div class="input-item">
                        <label for="articleUnit">Unit:</label>
                        <input type="text" id="articleUnit" name="articleUnit" value="<?= $articleById['aunit']; ?>">
                    </div>

                    <label for="articleComment">Comment:</label>
                    <textarea name="articleComment" id="comment" rows="5"><?php echo $articleById['acomment'] ?? ''; ?></textarea><br>

                    <div class="input-item">
                        <input type="submit" name="update_article" value="update the article">
                    </div>
                </fieldset>
            </form>
        </div>
    </main>

    <script>
        var articleType = "<?= $articleById['atype']; ?>";
        var articleStatus = "<?= $articleById['astatus']; ?>";
        articleDropdown(articleType, articleStatus);
    </script>

<?php include_once "includes/templates/footer.php"; ?>