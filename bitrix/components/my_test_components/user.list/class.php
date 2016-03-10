<?php
/**
 * Компонент user.list 
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\Data\Cache as Cache;
use \Bitrix\Main\Application as Application;

/**
 * Class CUserList
 */
class CUserList extends CBitrixComponent
{
    /**
     * @var string HTML код постраничной навигации
     */
    private $pageNavigationHTML;

    /**
     * @var array Массив отбираемых полей для списка пользователей
     */
    private $arSelectedFields;

    /**
     * Подключение языкового файла
     */
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    /**
     * Подготовка параметров компонента.
     *
     * Подготавливаем параметры компонента, устаналиваем значения по умолчнию, если не заданы.
     *
     * @param array $params Массив параметров компонента
     *
     * @return array
     */
    public function onPrepareComponentParams(array $params)
    {
        $result = [
            'SHOW_NAV'     => $params['SHOW_NAV'] === 'Y' ? 'Y' : 'N',
            'PER_PAGE'     => 0 === (int)$params['PER_PAGE'] ? 10 : (int)$params['PER_PAGE'],
            'SHOW_ALL'     => $params['SHOW_ALL'] === 'Y' ? 'Y' : 'N',
            'SORT_BY'      => strlen($params['SORT_BY']) ? $params['SORT_BY'] : 'ID',
            'SORT_ORDER'   => $params['SORT_ORDER'] === 'ASC' ? 'asc' : 'desc',
            'CACHE_TIME'   => (int)$params['CACHE_TIME'] > 0 ? (int)$params['CACHE_TIME'] : 3600,
            'CACHE_TYPE'   => $params['CACHE_TYPE'] === 'Y' ? 'Y' : 'N',
            'SHOW_ACTIVE'  => $params['SHOW_ACTIVE'] === 'Y' ? 'Y' : 'N',
            'NAV_TITLE'    => strlen($params['NAV_TITLE']) ? $params['NAV_TITLE'] : '',
            'NAV_TEMPLATE' => strlen($params['NAV_TEMPLATE']) ? $params['NAV_TEMPLATE'] : '',
        ];

        if ($params['SHOW_ACTIVE'] === 'Y') {
            $result['FILTER']['SHOW_ACTIVE'] = 'Y';
        } else {
            $result['FILTER'] = [];
        }

        //Если не задан устанавливаем отбираемые по умолчанию поля
        if (!is_array($params['FIELDS'])) {
            $params['FIELDS'] = ['ID', 'LOGIN', 'NAME', 'LAST_NAME', 'EMAIL'];
        }

        //Если не задан - по умолчанию пустой массив пользовательских полей
        if (!is_array($params['USER_FIELDS'])) {
            $params['USER_FIELDS'] = [];
        }

        //Список выбираемых полей
        $this->arSelectedFields = [
            'FIELDS' => $params['FIELDS'],
            'SELECT' => $params['USER_FIELDS']
        ];

        return $result;
    }

    /**
     * Получить список пользователей
     *
     * @return array
     */
    private function getUserList(){
        //Выборка списка пользователей
        $dbRes = CUser::GetList($this->arParams['SORT_BY'], $this->arParams['SORT_ORDER'], $this->arParams['FILTER'],
            $this->arSelectedFields);

        //Для хранения списка пользователей
        $arUsers = [];

        //Из выборки получаем интересующую страницу
        $dbRes->NavStart($this->arParams['PER_PAGE'], $this->arParams['SHOW_ALL']);

        $navComponentObject = null;

        //HTML код постраничной навигации
        $this->pageNavigationHTML = $dbRes->GetPageNavStringEx($navComponentObject, $this->arParams['NAV_TITLE'],
            $this->arParams['NAV_TEMPLATE'], $this->arParams['SHOW_ALL']);

        //Выборка элементов
        while ($arTemp = $dbRes->Fetch()) {
            $arUsers[] = $arTemp;
        }

        return $arUsers;
    }

    /**
     * Получить описания полей указанных в параметрах
     *
     * @param array $arUsers Массив полей пользователя, полученные из выборки
     *
     * @return array Массив символьных кодов полей и их описания
     */
    private function getSelectedFieldsNames(array $arUsers)
    {
        //Если не массив, либо в массив пуст (нет ни одного пользователя)
        if (!is_array($arUsers) && !is_array($arUsers[0])) {
            return [];
        }

        //Получаем список символьных кодов отобранных полей
        $arFieldsCodes = array_keys($arUsers[0]);
        $arFieldsNames = [];

        //Получаем все пользовательские поля (UF_*) пользователей для соотвествующего языка публичной части сайта
        global $USER_FIELD_MANAGER;
        $arUserFields = $USER_FIELD_MANAGER->GetUserFields('main', 0, LANGUAGE_ID);

        //Ищем соответствующее полю описание
        foreach ($arFieldsCodes as $field) {
            //Для типовых полей описание поля находится в языковом файле. Состав типовых полей не изменяется
            $arFieldsNames[$field] = Loc::getMessage($field);

            //Если поле не найдено в типовых, то оно задано пользователем при создании поля
            if (null === $arFieldsNames[$field]) {
                $arFieldsNames[$field] = $arUserFields[$field]['EDIT_FORM_LABEL'];
            }
        }

        return $arFieldsNames;
    }

    /**
     * Получить дополнительные идентификатор для формирования идентификатора кеша компонента
     *
     * @return string
     */
    private function getAdditionalCacheID()
    {
        $request = Application::getInstance()->getContext()->getRequest();

        $query = parse_url($request->getRequestUri(), PHP_URL_QUERY);

        $arQuery = [];
        parse_str($query, $arQuery);

        $id = '';

        //Выбираем параметры постраничной навигации
        foreach($arQuery as $key => $value){
            $id .= false !== strpos($key, 'PAGEN', 0) ? "$key=$value" : '';
            $id .= false !== strpos($key, 'SHOWALL', 0) ? "$key=$value" : '';
        }

        return md5($id);
    }

    /**
     * Выполнить логику компонента
     */
    public function executeComponent()
    {
        //Экспорт списка в файл
        try {
            $this->exportToFile();
        } catch (\Bitrix\Main\SystemException $e) {
            $this->arResult['ERROR'] = Loc::getMessage('USER_LIST_ERROR_EXPORT_MESSAGE');
            $this->includeComponentTemplate();

            return false;
        }

        $cache = Cache::createInstance();

        if ($this->arParams['CACHE_TYPE'] === 'Y' && $cache->initCache($this->arParams['CACHE_TIME'], $this->getCacheID($this->getAdditionalCacheID()), SITE_ID.'/'.$this->getRelativePath())
        ) {
            $this->arResult = $cache->getVars();
        } elseif ($cache->startDataCache()) {

            //Получить список пользоватsалей
            $this->arResult['USERS'] = $this->getUserList();

            //Получить выбранные заголовки
            $this->arResult['FIELD_NAMES'] = $this->getSelectedFieldsNames($this->arResult['USERS']);

            //Получить HTML постраничной навигации
            $this->arResult['NAV_STRING'] = $this->pageNavigationHTML;

            //Если пользоваталей нет, не кешируем
            if (0 === count($this->arResult['USERS'])) {
                $cache->abortDataCache();
            }

            $cache->endDataCache($this->arResult);
        }

        $this->includeComponentTemplate();
    }


    /**
     * Экспорт списка пользователей в файл
     * 
     * @return bool
     *
     * @throws \Bitrix\Main\SystemException
     */
    public function exportToFile()
    {
        //Получаем значение GET параметра export
        $request = Application::getInstance()->getContext()->getRequest();
        $export = htmlspecialchars($request->getQuery('export'));

        //Если задан неддопустимый формат импорта
        if (!in_array($export, ['xls', 'csv'], true)) {
            return false;
        }

        //Увеличиваем время выполнения скрипта
        set_time_limit(30);
        //Даем скрипту больше памяти на выполнение
        ini_set('memory_limit', '1024M');

        //Получаем весь список пользователей
        $this->arParams['SHOW_ALL'] = 'Y';
        $this->arParams['PER_PAGE'] = PHP_INT_MAX;
        $this->arResult['USERS'] = $this->getUserList();
        $this->arResult['FIELD_NAMES'] = $this->getSelectedFieldsNames($this->arResult['USERS']);

        //Добавляем названия столбцов на языке сайта
        $this->arResult['USERS'] = array_merge([$this->arResult['FIELD_NAMES']], $this->arResult['USERS']);

        $this->createFile($this->arResult['USERS'], $export);

        return true;
    }


    /**
     * Создать файл и отдать его пользователю
     * 
     * @param $data
     * @param $extension
     */
    private function createFile($data, $extension)
    {
        //Прервываем буфер вывода, чтобы лишняя информация не попала в конечный файл
        ob_end_clean();

        //Формируем название файла
        $fileName = 'user_list_' . date('Y_m_d') . '.' . $extension;

        //Устанавливаем заголовки
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Type: application/vnd.ms-excel');

        //Флаг для обработки первой строки массива
        $flag = false;

        foreach ($data as $row) {
            if (!$flag) {
                //Вывод заголовков столбцов
                echo implode("\t", $row) . "\n";
                //Отмечаем что заголовки выведены
                $flag = true;
                //Прекращаем обработку первого элемента массива
                continue;
            }
            //Подготавливаем к выводу очередной элемент массива (строку)
            array_walk($row,
                function (&$str) {
                    $str = preg_replace("/\t/", "\\t", $str);
                    $str = preg_replace("/\r?\n/", "\\n", $str);
                    if (false !== strstr($str, '"')) {
                        $str = '"' . str_replace('"', '""', $str) . '"';
                    }
                }
            );
            //Вывести строку
            echo implode("\t", array_values($row)) . "\n";
        }
        //Прекращаем выполнение скрипта
        die;
    }
}
