<?php
if (!defined('XOOPS_ROOT_PATH')) {
    include_once "../../mainfile.php";
} else {
    include_once XOOPS_ROOT_PATH . "/mainfile.php";
}

if (!defined("UGM_TOOLS2_PATH")) {
    define("UGM_TOOLS2_PATH", XOOPS_ROOT_PATH . "/modules/ugm_tools2");
}

if (!defined("UGM_TOOLS2_URL")) {
    define("UGM_TOOLS2_URL", XOOPS_URL . "/modules/ugm_tools2");
}

global $xoopsConfig;
include_once UGM_TOOLS2_PATH . "/language/{$xoopsConfig['language']}/main.php";

//取得ugm_tools2的$XoopsModuleConfig
if (!function_exists('ugm_tools2XoopsModuleConfig')) {
    function ugm_tools2XoopsModuleConfig()
    {
        $modhandler  = xoops_getHandler('module');
        $xoopsModule = $modhandler->getByDirname("ugm_tools2");
        if (is_object($xoopsModule)) {
            $config_handler    = xoops_getHandler('config');
            $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

            return $xoopsModuleConfig;
        }

        return false;
    }
}
