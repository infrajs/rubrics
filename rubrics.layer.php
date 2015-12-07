<?php

infra_admin_modified();

Path::req('*rubrics/rubrics.inc.php');

$layer = Load::loadJSON('*rubrics/rubrics.layer.json');

$conf = Infra::config();
if (empty($conf['rubrics'])) {
	return infra_ans($layer);
}

$types = $layer['childs'];
$layer['childs'] = array();

$list = $conf['rubrics']['list'];
foreach ($list as $rub => $param) {
	if (!$param) {
		continue;
	}
	if (!$types[$param['type']]) {
		continue;
	}
	$layer['childs'][$rub] = $types[$param['type']];
	if ($conf['rubrics']['main'] == $rub) {
		$layer['childs'][$rub]['config']['main'] = true;
	}
}

return infra_ans($layer);
