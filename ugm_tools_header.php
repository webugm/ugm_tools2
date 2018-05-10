<?php
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
include_once XOOPS_ROOT_PATH . "/mainfile.php";

//include_once "common/xoops.php";
if (!defined("_UGM_TOOLS2_PATH")) {
	define("_UGM_TOOLS2_PATH", XOOPS_ROOT_PATH . "/modules/ugm_tools2");
}

if (!defined("_UGM_TOOLS2_URL")) {
	define("_UGM_TOOLS2_URL", XOOPS_URL . "/modules/ugm_tools2");
}

global $xoopsConfig;
include_once _UGM_TOOLS2_PATH . "/language/{$xoopsConfig['language']}/main.php";

//取得UgmTools2的$UgmToolsXoopsModuleConfig
if (!function_exists('UgmTools2XoopsModuleConfig')) {
	function UgmTools2XoopsModuleConfig() {
		$modhandler = xoops_gethandler('module');
		$xoopsModule = $modhandler->getByDirname("ugm_tools2");
		if (is_object($xoopsModule)) {
			$config_handler = xoops_gethandler('config');
			$xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

			return $xoopsModuleConfig;
		}
		return false;
	}
}
