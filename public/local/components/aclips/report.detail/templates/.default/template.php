<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\UI\Extension;

Extension::load('ui.buttons');
Extension::load('jquery');
?>

<?php
    if (!empty($arResult['FILTER_PARAMS'])) {
        \Bitrix\UI\Toolbar\Facade\Toolbar::addFilter([
            'FILTER_ID' => $arResult['REPORT_ID'],
            'GRID_ID' => $arResult['REPORT_ID'],
            'FILTER' => $arResult['FILTER_PARAMS']['FILTER'],
            'FILTER_PRESETS' => $arResult['FILTER_PARAMS']['FILTER_PRESETS'],
            'ENABLE_LIVE_SEARCH' => false,
            'ENABLE_LABEL' => true
        ]);
    }

    $button = new \Bitrix\UI\Buttons\Button([
        'color' => \Bitrix\UI\Buttons\Color::LIGHT_BORDER,
        'icon' => \Bitrix\UI\Buttons\Icon::DISK,
        'click' => new \Bitrix\UI\Buttons\JsCode(
            'BX.Aclips.Report.applyFilter()'
        ),
        'text' => 'Сформировать отчёт'
    ]);

    \Bitrix\UI\Toolbar\Facade\Toolbar::addButton($button);
?>

    <div id="errors"></div>
    <div id="report-wrapper"></div>

    <script>
        BX.ready(function () {
            BX.addClass(BX('workarea-content'), 'no-report')
            BX.Aclips.Report.reportId = '<?=$arResult['REPORT_ID']?>'
            BX.Aclips.Report.signedParameters = '<?=$arResult['SIGNED_PARAMETERS']?>'
        })
    </script>

<?php if (!empty($arResult['CUSTOM_STYLE'])) {
    print $arResult['CUSTOM_STYLE'];
} ?>