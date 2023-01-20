<?php

/**
 * - /local/classes/{Path|raw}/{*|raw}.php
 * - /local/classes/{Path|ucfirst,lowercase}/{*|ucfirst,lowercase}.php
 */
spl_autoload_register(function ($sClassName) {

    $sClassFile = __DIR__ . '/classes';

    if (file_exists($sClassFile . '/' . str_replace('\\', '/', $sClassName) . '.php')) {
        require_once($sClassFile . '/' . str_replace('\\', '/', $sClassName) . '.php');
        return;
    }

    $arClass = explode('\\', strtolower($sClassName));
    foreach ($arClass as $sPath) {
        $sClassFile .= '/' . ucfirst($sPath);
    }
    $sClassFile .= '.php';
    if (file_exists($sClassFile)) {
        require_once($sClassFile);
    }
});

/**
 * For composer projects
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}
