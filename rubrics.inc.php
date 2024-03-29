<?php
use infrajs\path\Path;
use infrajs\load\Load;
use infrajs\template\Template;
use infrajs\cache\Cache;
use infrajs\doc\Docx;
use infrajs\config\Config;
use infrajs\doc\Mht;
use infrajs\rubrics\Rubrics;

function rub_search($dir, $str, $exts, $lang = false)
{
	$files = rub_list($dir, 0, 0, $exts, $lang);

	if (!empty($files[$str])) {
		$files[$str]['idfinded'] = true;//Найдено по id
			   return $files[$str];
	}
	foreach ($files as $d) {

		if (mb_strtolower($d['name']) == mb_strtolower($str)) {
			return $d;
		}
	}

	return array();
}
function rub_ptube()
{
	$ptube = 'http.*youtube\.com.*watch.*=([\w\-]+).*';

	return $ptube;
}
function rub_ptube2()
{
	$ptube = 'http.{0,1}:\/\/youtu\.be\/([\w\-]+)';

	return $ptube;
}
function rub_article($src)
{
	return Rubrics::article($src);
}
function rub_getdir($type) {
	$conf = Config::get('rubrics');
	if (isset($conf['list'][$type]['dir'])) {
		$dir = $conf['list'][$type]['dir'];
	} else {
		$dir = '~'.$type.'/';
	}
	return $dir;
}
function rub_get($type, $id, $exts)
{
	if (!$type) return;
	$dir = rub_getdir($type);
	$files = rub_list($dir, 0, 0, $exts);
	
	if (empty($files[$id])) {
		$res = array();
	} else {
		$res = $files[$id];
	}

	return $res;
}
function rub_list($src, $start = 0, $count = 0, $exts = array(), $lang = false)
{

	$files = Cache::exec([$src], 'Содержимое рубрик', function ($src, $start, $count, $exts, $lang) {
		return _rub_list($src, $start, $count, $exts, $lang);
	}, array($src, $start, $count, $exts, $lang));

	return $files;
}
function _rub_list($src, $start, $count, $exts, $lang)
{
	$res = array();
	$dir = Path::theme($src);
	
	if (is_dir($dir) && $dh = opendir($dir)) {
		$files = array();
	
		while (($file = readdir($dh)) !== false) {
			if ($file[0] == '.') {
				continue;
			}
			if ($file[0] == '~') {
				continue;
			}
			if ($file == 'Thumbs.db') {
				continue;
			}
			//depricated -> Rubrics::info();
			$rr = Load::nameInfo(Path::toutf($file));
			if ($lang && $rr['lang'] && $rr['lang'] != $lang) continue;
			$ext = $rr['ext'];
			if ($exts && !in_array($ext, $exts)) continue;
			$size = filesize($dir.$file);
			
			$file = Path::toutf($file);
			

			if (in_array($ext, array('mht', 'tpl', 'html', 'txt','php'))) {
				$rr = Mht::preview($src.$file);
				
			} elseif (in_array($ext, array('docx'))) {
				$rr = Docx::preview($src.$file);
			}

			$rr['size'] = round($size / 1000000, 2);
			$links = isset($rr['links'])? $rr['links']: null;
			if ($links) {
				unset($rr['links']);
				$ptube = rub_ptube();
				$ptube2 = rub_ptube();

				foreach ($links as $v) {
					$r = preg_match('/'.$ptube.'/', $v['href'], $match);
					$r2 = preg_match('/'.$ptube2.'/', $v['href'], $match);
					if ($r) {
						if (empty($rr['video'])) $rr['video'] = array();
						$v['id'] = $match[1];
						$rr['video'][] = $v;
					} elseif ($r2) {
						if (empty($rr['video'])) $rr['video'] = array();
						$v['id'] = $match[1];
						$rr['video'][] = $v;
					} else {
						if (empty($rr['links'])) $rr['links'] = array();
						$rr['links'][] = $v;
					}
				}
			}
			$files[] = $rr;
		}
		usort($files, function ($b, $a) {
			$a = isset($a['date']) ? $a['date'] : null;
			$b = isset($b['date']) ? $b['date'] : null;
			return $a < $b ? +1 : -1;
		});
		$maxid = 0;
		foreach ($files as $fdata) {
			if (!$fdata['id']) {
				continue;
			}
			if ($fdata['id'] > $maxid) {
				$maxid = $fdata['id'];
			}
		}
		foreach ($files as &$fdata) {
			if ($fdata['id'] && $fdata['date']) {
				continue;
			}
			if (!$fdata['id']) {
				$fdata['id'] = ++$maxid;
			}
		}
		$files = array_reverse($files);
		if ($count || $start) {
			$files = array_splice($files, $start, $count);
		}
		foreach ($files as $fdata) {
			$res[$fdata['id']] = $fdata;
		}
	}
	return $res;
}
