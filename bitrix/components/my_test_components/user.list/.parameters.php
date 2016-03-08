<?php
/**
 * Файл описания параметров компонента
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

//Массив стандартных полей пользователя
$arFields = [
    'ID',
    'ACTIVE',
    'LAST_LOGIN',
    'LOGIN',
    'EMAIL',
    'NAME',
    'LAST_NAME',
    'SECOND_NAME',
    'TIMESTAMP_X',
    'PERSONAL_BIRTHDAY',
    'DATE_REGISTER',
    'PERSONAL_PROFESSION',
    'CHECKWORD_TIME',
    'LAST_ACTIVITY_DATE',
    'CHECKWORD',
    'PERSONAL_WWW',
    'PERSONAL_ICQ',
    'PERSONAL_GENDER',
    'PERSONAL_PHOTO',
    'PERSONAL_PHONE',
    'PERSONAL_FAX',
    'PERSONAL_MOBILE',
    'PERSONAL_PAGER',
    'PERSONAL_STREET',
    'PERSONAL_MAILBOX',
    'PERSONAL_CITY',
    'PERSONAL_STATE',
    'EXTERNAL_AUTH_ID',
    'AUTO_TIME_ZONE',
    'LID',
    'PERSONAL_ZIP',
    'PERSONAL_COUNTRY',
    'PERSONAL_NOTES',
    'WORK_COMPANY',
    'WORK_DEPARTMENT',
    'WORK_POSITION',
    'WORK_WWW',
    'WORK_PHONE',
    'WORK_FAX',
    'WORK_PAGER',
    'WORK_STREET',
    'WORK_MAILBOX',
    'CONFIRM_CODE',
    'TIME_ZONE',
    'IS_ONLINE',
    'WORK_CITY',
    'WORK_STATE',
    'WORK_ZIP',
    'WORK_COUNTRY',
    'WORK_PROFILE',
    'WORK_NOTES',
    'ADMIN_NOTES',
    'XML_ID',
    'STORED_HASH',
    'LOGIN_ATTEMPTS',
    'PASSWORD'
];

//Получить список пользовательских (нестандартных) полей
global $USER_FIELD_MANAGER;
$arUserFields = array_keys($USER_FIELD_MANAGER->GetUserFields('main', 0));

$arComponentParameters = [
    'GROUPS'     => [
        'PAGER_SETTINGS' => [
            'NAME' => Loc::getMessage('USER_LIST_PARAMS_PAGER_SETTINGS'),
            'SORT' => '500',
        ]
    ],
    'PARAMETERS' => [

        //Общие настройки
        'SHOW_ACTIVE'     => [
            'PARENT'   => 'BASE',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_SHOW_ONLY_ACTIVE_USERS'),
            'TYPE'     => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'Y',
            'REFRESH'  => 'N'
        ],
        'SORT_BY'         => [
            'PARENT'   => 'BASE',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_SORT_BY'),
            'TYPE'     => 'LIST',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'ID',
            'REFRESH'  => 'N',
            'VALUES'   => $arFields
        ],
        'SORT_ORDER'      => [
            'PARENT'   => 'BASE',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_SORT_ORDER'),
            'TYPE'     => 'LIST',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'ASC',
            'REFRESH'  => 'N',
            'VALUES'   => ['ASC', 'DESC']
        ],
        'FIELDS'          => [
            'PARENT'   => 'BASE',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_FIELDS'),
            'TYPE'     => 'LIST',
            'MULTIPLE' => 'Y',
            'REFRESH'  => 'N',
            'VALUES'   => $arFields
        ],
        'USER_FIELDS' => [
            'PARENT'   => 'BASE',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_USER_FIELDS'),
            'TYPE'     => 'LIST',
            'MULTIPLE' => 'Y',
            'REFRESH'  => 'N',
            'VALUES'   => $arUserFields
        ],

        //Настройки постраничной навигации
        'SHOW_ALL'        => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_SHOW_ALL'),
            'TYPE'     => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'N',
            'REFRESH'  => 'N'
        ],
        'SHOW_NAV'        => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_SHOW_NAV'),
            'TYPE'     => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'Y',
            'REFRESH'  => 'N'
        ],
        'PER_PAGE'        => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_PER_PAGE'),
            'TYPE'     => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT'  => '10',
            'REFRESH'  => 'N'
        ],
        'NAV_TITLE'       => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_NAV_TITLE'),
            'TYPE'     => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT'  => '',
            'REFRESH'  => 'N'
        ],
        'NAV_TEMPLATE'    => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_NAV_TEMPLATE'),
            'TYPE'     => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT'  => '',
            'REFRESH'  => 'N'
        ],
        'AJAX_MODE'       => [
            'PARENT'   => 'PAGER_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_AJAX_MODE'),
            'TYPE'     => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'Y',
            'REFRESH'  => 'N'
        ],

        //Настройки кеширования
        'CACHE_TIME'      => [
            'PARENT'   => 'CACHE_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_CACHE_TIME'),
            'TYPE'     => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT'  => '3600',
            'REFRESH'  => 'N'
        ],
        'CACHE_TYPE'      => [
            'PARENT'   => 'CACHE_SETTINGS',
            'NAME'     => Loc::getMessage('USER_LIST_PARAMS_CACHE_TYPE'),
            'TYPE'     => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT'  => 'Y',
            'REFRESH'  => 'N'
        ]
    ]
];
