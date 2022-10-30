<?php

require_once('config.php');
require_once('functions.php');

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/app.css" />
</head>
<body>
    <header>
        <div class="container">
            <nav class="menu">
                <ul>
                    <li>
                        <?=$ob_data->getUrl()?>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main role="main" class="<?=$ob_data->page?>">
        <div class="container">
            <?=$ob_data->content?>
        </div>
    </main>
    <script src="/js/app.js"></script>
</body>
</html>