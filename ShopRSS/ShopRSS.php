<?php
/**
 * Модуль создания RSS лент для интернет магазина
 *
 * @author Medvedev Sergey <irbissk@gmail.com>
 * @copyright Copyright (c) 2011, Medvedev Sergey
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @since 15.01.2011
 * @version 1.0
 */
/* Путь к модулю */
$module_path_name = 'ShopRSS';
/* Имя модуля */
$module_name = 'RSS для интернет магазина';
// Указание соответствия имени класса и модуля
$GLOBALS['HOSTCMS_CLASS']['shoprss'] = $module_path_name;
$kernel = & singleton('kernel');
/* Список файлов для загрузки */
$kernel->AddModuleFile($module_path_name, CMS_FOLDER . "modules/{$module_path_name}/{$module_path_name}.class.php");
// Добавляем версию модуля
$kernel->add_modules_version($module_path_name, '1.0', '15.01.2011');