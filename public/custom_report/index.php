<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>

<?php
    $APPLICATION->includeComponent('aclips:report.detail', '', [
        'CODE' => 'Test'
    ]);
?>

<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>