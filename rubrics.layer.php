<?php

use infrajs\path\Path;
use infrajs\load\Load;
use infrajs\ans\Ans;
use infrajs\config\Config;
use infrajs\nostore\Nostore;

/**
 * Формирует созданные в конфиге план рубрик, в виде слоёв
 * Кэшируется как статика
 **/
//Nostore::pubStat(); В режиме Router::$main главная страница тоже становится статикой

$conf = Config::get('rubrics');


$layer = Load::loadJSON('-rubrics/layer.json');




$types = $layer['childs'];
$layer['childs'] = array();

$list = $conf['list'];
foreach ($list as $rub => $param) {
	if (!$param) {
		continue;
	}
	
	if (empty($types[$param['type']])) continue;
	
	$layer['childs'][$rub] = $types[$param['type']];
	if ($conf['main'] == $rub) {
		$layer['childs'][$rub]['config']['main'] = true;
	}
}

return Ans::ans($layer);
