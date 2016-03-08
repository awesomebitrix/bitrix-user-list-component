<?php
/**
 * Файл описания компонента
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME'        => Loc::getMessage('USER_LIST_NAME'),
    'DESCRIPTION' => Loc::getMessage('USER_LIST_DESCRIPTION'),
    'SORT'        => 20,
    'CACHE_PATH'  => 'Y',
    'PATH'        => [
        'ID' => 'content',
    ],
];
