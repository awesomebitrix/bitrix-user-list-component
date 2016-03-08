<?php
/**
 * Страница с примером вызова компонента
 */
//Подключение пролога Битрикс
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
?>
<html>
    <head>
        <title>Список пользователей</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h2>Список пользователей</h2>
            <?php $APPLICATION->IncludeComponent(
                'my_test_components:user.list',
                '',
                [
                    'SHOW_ACTIVE'  => 'Y',
                    'SORT_BY'      => 'ID',
                    'SORT_ORDER'   => 'ASC',
                    'FIELDS'       => [
                        'ID',
                        'LOGIN',
                        'NAME'
                    ],
                    'USER_FIELDS'  => [
                        'UF_VK_LINK'
                    ],
                    'SHOW_ALL'     => 'N',
                    'SHOW_NAV'     => 'Y',
                    'PER_PAGE'     => 5,
                    'NAV_TITLE'    => 'Страницы',
                    'NAV_TEMPLATE' => '',
                    'AJAX_MODE'    => 'Y',
                    'CACHE_TYPE'   => 'Y',
                    'CACHE_TIME'   => 3600
                ]
            );?>
        </div>
    </body>
</html>
