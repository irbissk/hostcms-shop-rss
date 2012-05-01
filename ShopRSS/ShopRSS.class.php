<?php
/**
 * Модуль создания RSS лент для интернет магазина
 *
 * @author Medvedev Sergey <irbissk@gmail.com>
 * @copyright Copyright (c) 2011, Medvedev Sergey
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @since 01.05.2012
 * @version 1.1
 */
class ShopRSS {
	
	/**
	 * Инсталяция модуля
	 */
	function Install() {
		$lib = new lib();
		$Constant = new Constants();
		$type = 0;
		$constant_id = 0;
		// Немножко прибираемся
		if($row = $Constant->GetConstantByName('SHOP_RSS_LIB_DIR'))
		{
			show_message('Обнаружена предыдущая инсталяция модуля, немного прибираемся...');
			$type = 1;
			$constant_id = $row['constants_id'];
			$lib->DeleteLibDir($row['constants_value']);
			show_message('Предыдущая инсталяция модуля успешно удалена');
		}
		// Дабавляем группу для типовых динамических страниц
		$param = array();
		$param['lib_dir_parent_id'] = 0;
		$param['lib_dir_name'] = 'RSS для интернет-магазина';
		if(!$dir_id = $lib->InsertLibDir($param)) {
			show_error_message('Не удается добавить группу типовых динамических страниц! Установка модуля остановлена');
			return;
		}
		//Добавляем типовую динамическую страницу
		$param = array();
		$param['lib_dir_id'] = to_int($dir_id);
		$param['lib_name'] = 'RSS для интернет-магазина';
		$param['lib_description'] = 'Выводит в RSS товары интернет магазина';
		if(is_readable(CMS_FOLDER . 'modules/ShopRSS/install/tds.php')) {
			$param['lib_module'] = file_get_contents(CMS_FOLDER . 'modules/ShopRSS/install/tds.php');
		} else {
			show_error_message('Не удается добавить код типовой динамической страницы! Проверьте права доступа на файлы модуля и переустановите его.');
			$param['lib_module'] = '';
		}
		if(is_readable(CMS_FOLDER . 'modules/ShopRSS/install/tds_config.php')) {
			$param['lib_module_config'] = file_get_contents(CMS_FOLDER . 'modules/ShopRSS/install/tds_config.php');
		} else {
			show_error_message('Не удается добавить код настроек типовой динамической страницы! Проверьте права доступа на файлы модуля и переустановите его.');
			$param['lib_module_config'] = '';
		}
		$param['users_id'] = false;
		// Добавляем параметры типовой динамической страницы
		if ($lib_id = $lib->InsertLib($param)) {
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Интернет-магазин';
			$param['lib_property_varible_name'] = 'shop_id';
			$param['lib_property_type'] = 4;
			$param['lib_property_default_value'] = 0;
			$param['lib_property_order'] = 10;
			$param['lib_property_sql_request'] = "SELECT * FROM `shop_shops_table` WHERE `site_id` = '{SITE_ID}' ORDER BY `shop_shops_name`;";
			$param['lib_property_sql_caption_field'] = 'shop_shops_name';
			$param['lib_property_sql_value_field'] = 'shop_shops_id';
			$lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Число выводимых элементов в ленте';
			$param['lib_property_varible_name'] = 'item_count';
			$param['lib_property_type'] = 0;
			$param['lib_property_default_value'] = 15;
			$param['lib_property_order'] = 20;
			$lib->InsertLibProperty($param);

            $param = array();
            $param['lib_id'] = $lib_id;
            $param['lib_property_name'] = 'Группа интернет-магазина, -1 для выбора товаров из всех групп, 0 для выбора из корневой группы';
            $param['lib_property_varible_name'] = 'group_id';
            $param['lib_property_type'] = 0;
            $param['lib_property_default_value'] = '-1';
            $param['lib_property_order'] = 30;
            $lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Заголовок RSS-канала';
			$param['lib_property_varible_name'] = 'rss_title';
			$param['lib_property_type'] = 0;
			$param['lib_property_default_value'] = '';
			$param['lib_property_order'] = 40;
			$lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Описание RSS-канала';
			$param['lib_property_varible_name'] = 'rss_description';
			$param['lib_property_type'] = 0;
			$param['lib_property_default_value'] = '';
			$param['lib_property_order'] = 50;
			$lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Удалять теги из RSS';
			$param['lib_property_varible_name'] = 'strip_tags';
			$param['lib_property_type'] = 1;
			$param['lib_property_default_value'] = 1;
			$param['lib_property_order'] = 60;
			$lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Отображать изображение для товара в RSS';
			$param['lib_property_varible_name'] = 'show_images';
			$param['lib_property_type'] = 1;
			$param['lib_property_default_value'] = 1;
			$param['lib_property_order'] = 70;
			$lib->InsertLibProperty($param);
			
			$param = array();
			$param['lib_id'] = $lib_id;
			$param['lib_property_name'] = 'Включать фото для товара в текст элемента';
			$param['lib_property_varible_name'] = 'image_to_item';
			$param['lib_property_type'] = 1;
			$param['lib_property_default_value'] = 1;
			$param['lib_property_order'] = 80;
			$lib->InsertLibProperty($param);
		} else {
			show_error_message('Не удается добавить типовую динамическую страницу! Проверьте права доступа на файлы модуля и переустановите его.');
		}
		// Добавляем/обновляем константу
		$Constant->AddEditConstants($type, $constant_id, 'SHOP_RSS_LIB_DIR', to_int($dir_id), 'Идентификатор группы ТДС для модуля RSS интернет-магазина', 1);
	}
	
	/**
	 * Удаление модуля
	 */
	function Uninstall()
	{
		$lib = new lib();
		$Constant = new Constants();
		$constants_name = 'SHOP_RSS_LIB_DIR'; 
		if($row = $Constant->GetConstantByName($constants_name)) {
			$lib->DeleteLibDir($row['constants_value']);
			$Constant->DelConstants($row['constants_id']);
		}
	}
	
	/**
	 * Генерация RSS ленты
	 * @param int $shop_id идентификатор интернет магазина
     * @param int $group_id идентификатор группы интернет магазина
	 * @param int $items_on_page количество товаров в ленте
	 * @param array $property дополнительные параметры
	 * @return string сформированная RSS лента
	 */
	function ShowRSS($shop_id, $group_id, $items_on_page,$property)
	{
		$shop = & singleton('shop');
		$RssWrite = & singleton('RssWrite');
		$site = & singleton('site');
		$structure = & singleton('Structure');
		
		$shop_id = to_int($shop_id);
		$items_on_page = to_int($items_on_page);
		$property = to_array($property);
        $group_id = to_int($group_id);
        if($group_id < 0) {
            $group_id = false;
        }
		if($items_on_page <= 0 or $items_on_page > 100) $items_on_page = 15;
		
		if(!$shop_info = $shop->GetShop($shop_id)) return 'Shop not found';
		if(!$shop_path = $structure->GetStructurePath($shop_info['structure_id'], 0)) return 'Not defined site structure web shop';
		if( $site_url = $site->GetCurrentAlias(CURRENT_SITE) ) {
			$site_url = 'http://' . str_replace('*.', '', $site_url);
		} else {
			return 'Undefined primary domain';
		}
		$shop_path = $shop_path == '/' ? '/' : '/'.$shop_path;
		
		$headers = array();
		if(!empty($property['title'])) {
			$headers['title'] = $RssWrite->str_for_rss($property['title']);
		} else {
			$headers['title'] = $RssWrite->str_for_rss($shop_info['shop_shops_name']);
		}
		if(!empty($property['description'])) {
			$headers['description'] = $RssWrite->str_for_rss($property['description']);
		} else {
			$headers['description'] = $RssWrite->str_for_rss(strip_tags($shop_info['shop_shops_description']));
		}
		$headers['link'] = $site_url.$shop_path;
		
		$param = array();
		$param['items_begin'] = 0;
		$param['items_on_page'] = $items_on_page;
		$param['items_field_order'] = 'shop_items_catalog_date_time';
		$param['items_order'] = 'Desc';
		$rows = $shop->GetAllItems($shop_id, $group_id, $param);
		
		$items = array();
		if($rows) {
			foreach($rows as $row) {
				$item_url = $site_url.$shop_path.$shop->GetPathGroup($row['shop_groups_id']).$row['shop_items_catalog_path'].'/';
				$image_path = ($property['show_images'] and !empty($row['shop_items_catalog_small_image'])) ? $shop->GetItemDir($row['shop_items_catalog_item_id']).$row['shop_items_catalog_small_image'] : '';
				$image = ($image_path and $property['image_to_item']) ? '<img src="'.$site_url.'/'.$image_path.'" alt="'.htmlspecialchars($row['shop_items_catalog_name']).'" />' : '';
				$items[] = array(
					'title'=>$property['strip_tags'] ? $RssWrite->str_for_rss(strip_tags($row['shop_items_catalog_name'])) : $RssWrite->str_for_rss($row['shop_items_catalog_name']),
					'description'=>$property['strip_tags'] ? $RssWrite->str_for_rss($image.strip_tags($row['shop_items_catalog_description'])) : $RssWrite->str_for_rss($image.$row['shop_items_catalog_description']),
					'pubDate'=>$row['shop_items_catalog_date_time'],
					'link'=>$item_url,
					'guid'=>$item_url,
					'enclosure'=>($image_path and !$property['image_to_item']) ? array(0=>array('url'=>$site_url.'/'.$image_path, 'length'=>filesize(CMS_FOLDER.$image_path))) : array()
				);
			}
		}
		
		echo $RssWrite->CreateRSS($headers, $items);
	}
	
}