<?php
session_start();
require_once "../classes/article.php";

if(!isset($_POST['update_article'])) {
    die(header("location:../articles.php"));
}

if(!empty($_POST['articleId']) &&
    !empty($_POST['articleName']) &&
    !empty($_POST['articleType']) &&
    !empty($_POST['articleCode']) &&
    !empty($_POST['articlePrice']) &&
    !empty($_POST['articleUnit']) &&
    !empty($_POST['articleStatus'])
) {
    $article = new Article();
    $article->updateArticle();
} else {
    require_once "../classes/notification.php";
    $message = new Notification();
    $message->setDataMissingOrInvalid();
    header("location:../editarticle.php?editid=" . (clean(filter_input(INPUT_POST, 'articleId', FILTER_SANITIZE_NUMBER_INT))));
}