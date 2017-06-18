<?php use Slice\Core\Kernel;?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=  $title; ?></title>
    <style>
        h2, p, a {
            text-align: center;
            color: #212121;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        h2 {
            font-size: 24px;

        }
        p {
            font-size: 16px;
        }
        footer a {
            display: block;
            text-decoration: none;
            font-size: 14px;
            color: #CCC;

        }
    </style>
</head>
<body>
<main>
<?= $content; ?>
</main>

<footer>
    <a href="https://github.com/pizzamindedlabs">Slice Framework <?= Kernel::VERSION?> </a>
</footer>
</body>
</html>