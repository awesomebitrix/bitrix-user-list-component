<?php
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
?>
<?php if(0 !== count($arResult['USERS'])):?>
    <table class="table table-hover">
        <thead>
            <tr>
                <?php foreach($arResult['FIELD_NAMES'] as $header):?>
                    <th class="text-center"><?=$header?></th>
                <?php endforeach;?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($arResult['USERS'] as $arUser):?>
                <tr>
                    <?php foreach($arResult['FIELD_NAMES'] as $code => $header):?>
                        <td class="text-center"><?=$arUser[$code]?></td>
                    <?php endforeach;?>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>

    <?php if('Y' === $arParams['SHOW_NAV']):?>
        <div class="text-center">
            <?=$arResult['NAV_STRING']?>
        </div>
    <?php endif;?>

<?php else:?>
    <div class="alert alert-danger">
        <?=Loc::getMessage('NO_USERS_MESSAGE');?>
    </div>
<?php endif;?>
