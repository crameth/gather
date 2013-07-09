<?php
// handles captain/map voting
include('./includes/functions.php');

if ( !isset($_GET['type']) || !isset($_GET['id']) ) {
	die('ERROR: no type or id defined');
}
if ( !isset($_COOKIE['steamID']) ) {
	die('ERROR: not logged in');
}
if ( !isset($_COOKIE['gather']) ) {
	die('ERROR: not in gather');
}

$cookie = $_COOKIE['steamID'];
$gather = $_COOKIE['gather'];

$type = $_GET['type'];
$id = $_GET['id'];

$f = file_get_contents('./gathers/'.$gather.'.json');
$d = json_decode($f, true);


$players = $d['players'];
$p_length = count($players);
$maps = $d['maps'];
$m_length = count($maps);

$info = $d['gather'];

$target = 12;

foreach ($players as $player) {
	if ( $player['id'] == $cookie ) {
		$target = (int)key($players);
	}
}

if ($target > 11) {
	die('ERROR: voting player not found in gather');
}

if ($type == 'map') {
	if ($id < $m_length) {
		$d['players'][$target]['map'] = $id;
	}
} else {
	header('Location: /gather');
}

$d = tally($d);
$d = json_encode($d);

$f = './gathers/'.$gather.'.json';
$fh = fopen($f, 'w') or die('ERROR: cannot open/create js');
fwrite($fh, $d);
fclose($fh);

header('Location: /gather');

compile($gather);
?>