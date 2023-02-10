<?php
session_start();
require_once "../classes/article.php";
require_once "../classes/notification.php";
$message = new Notification();

if(isset($_POST['create_article'])) {
    if(!empty($_POST['articleName']) &&
        !empty($_POST['articleType']) &&
        !empty($_POST['articleCode']) &&
        !empty($_POST['articlePrice']) &&
        !empty($_POST['articleUnit']) &&
        !empty($_POST['articleStatus'])) {
        $newArticle = new Article();
        $newArticle->createArticle();
    } else {
        $message->setDataMissingOrInvalid();
        die(header("location:../addarticle.php"));
    }
}