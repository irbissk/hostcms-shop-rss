<?php 
$ShopRSS = & singleton('ShopRSS');
/* Код интернет-магазина */
$shop_id = to_int($GLOBALS['LA']['shop_id']);
/* Число выводимых элементов в ленте */
$items_on_page = to_int($GLOBALS['LA']['item_count']);

$property=array();
/* Удалять теги из RSS */
$property['strip_tags'] = to_bool($GLOBALS['LA']['strip_tags']);
/* Отображать изображение для товара в RSS */
$property['show_images'] = to_bool($GLOBALS['LA']['show_images']);
/* Включить изображение в описание товара */
$property['image_to_item'] = to_bool($GLOBALS['LA']['image_to_item']);
/* Заголовок RSS-канала */
if (!empty($GLOBALS['LA']['rss_title'])) {
	$property['title'] = to_str($GLOBALS['LA']['rss_title']);
}
/* Описание RSS-канала */
if (!empty($GLOBALS['LA']['rss_description'])) {
	$property['description'] = to_str($GLOBALS['LA']['rss_description']);
}

$ShopRSS->ShowRSS($shop_id,$items_on_page,$property);
exit();