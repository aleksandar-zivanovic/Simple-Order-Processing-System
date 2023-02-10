<?php
session_start();
require_once 'includes/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php getPageName(); ?></title>
    <link rel="stylesheet" href="style.css">

    <?php if(isset($javaScript) && $javaScript == 'include'): ?>
        <script src="js/custom.js"></script>
    <?php endif; ?>

    <?php if(isset($jquery) && $jquery == 'include'): ?>
        <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <?php endif; ?>
</head>
<body>

<?php require_once 'navigation.php'; ?>