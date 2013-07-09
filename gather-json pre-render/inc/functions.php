<?php

function regather() {
	// get current gather number
	$f_0 = file_get_contents('global.txt') or die('ERROR: cannot open global.txt');
	$gather = $f_0;
	
	// increase gather number
	++$gather;
	
	// create new gather json
	$f_1 = './gathers/'.$gather.'.json';
	$fh_1 = fopen($f_1, 'w') or die('ERROR: cannot create new gather json');
	
	$c_1 = '{"gather":{"id":'.$gather.',"server":"0.0.0.0:27015 password","voice":"113.252.201.168:64738","mumble":true,"status":"wait"},"players":[],"maps":[{"name":"ns2_summit","votes":0},{"name":"ns2_veil","votes":0},{"name":"ns2_tram","votes":0},{"name":"ns2_refinery","votes":0},{"name":"ns2_mineshaft","votes":0},{"name":"ns2_docking","votes":0}]}';
	fwrite($fh_1, $c_1);
	
	fclose($fh_1);
	
	// create new chat textfile
	$f_2 = './gathers/'.$gather.'.txt';
	$fh_2 = fopen($f_2, 'w') or die('ERROR: cannot create new chat textfile');
	
	$c_2 = 'Welcome to Gather #'.$gather.'.<br />';
	fwrite($fh_2, $c_2);
	
	fclose($fh_2);
	
	compile($gather);

	// update global.txt
	$f_3 = './global.txt';
	$fh_3 = fopen($f_3, 'w') or die('ERROR: cannot open global.txt to update index');
	fwrite($fh_3, (string)$gather);
	fclose($fh_3);

	// chmod
	chmod($f_1, 0766);
	chmod($f_2, 0766);
	chmod($f_3, 0766);
}

function compile($gather) {
	$f = file_get_contents('./gathers/'.$gather.'.json');
	$d = json_decode($f, true);
	
	$players = $d['players'];
	$maps = $d['maps'];
	$info = $d['gather'];
	
	$p_length = count($players);
	$p_req = 12 - $p_length;
	
	$c = "var g = false,p = '',m = '',i = '';r = 'status:::{$info['status']}';if ($.cookie('gather') !== null){g = true;}$('span#playercount').html('{$p_length}');";
	if ($p_length < 1) {
		$c = $c.'p += \'There are no players in the gather.\';';
	} else {
		$c = $c.'p += \'';
		foreach ($players as $player) {
			$c = $c."<img src=\"{$player['avatar']}\" alt=\"\" /><a href=\"http://steamcommunity.com/profiles/{$player['id']}\">{$player['user']}</a><br />";
		}
		$c = $c.'\';';
	}
	$c = $c.'$(\'div#playerlist\').html(p);if (g) {m += \'';
	foreach ($maps as $map) {
		$c = $c.'<a href="vote.php?type=map&id='.key($maps).'">'.$map['name']."</a> ({$map['votes']})<br />";
	}
	$c = $c.'\';} else {m += \'';
	foreach ($maps as $map) {
		$c = $c.$map['name']." ({$map['votes']})<br />";
	}
	$c = $c.'\';}$(\'div#maplist\').html(m);i += \'<h1>Gather Information</h1>\';if (g) {i += \'<div id="help">Click on the number next to map name to vote for it.';
	$c = $c.'</div><div id="server"><a href="steam://run/4920//connect '.$info['server'].'">steam://run/4920//connect '.$info['server'].'</a></div><div id="voice"><a href="';
	if ($info['mumble']) {
		$v = 'mumble://';
	} else {
		$v = 'teamspeak://';
	}
	$c = $c.$v.$info['voice']."\">{$v}{$info['voice']}</a></div>";
	if ($info['status'] != 'wait') {
		$c = $c.'<div id="help">This gather is considered played as 12 players have been reached. Leave this gather to join a new gather.</div>';
	} else {
		$c = $c.'<div id="help">'.$p_req.' more players are required to start the gather.</div>';
	}
	$c = $c.'\';} else {i += \'';
	if ($info['status'] != 'wait') {
		$c = $c.'<div id="help">This gather is considered played as 12 players have been reached. Refresh this page to view a new gather.</div>';
	} else {
		$c = $c.'<div id="help">Join this gather to see information about it.</div>';
	}
	$c = $c.'\';}$(\'div#info\').html(i);';
	
	$f = './gathers/'.$gather.'.js';
	$fh = fopen($f, 'w') or die('ERROR: cannot open/create js');
	fwrite($fh, $c);
	fclose($fh);
}

function tally($d) {
	$players = $d['players'];
	$maps = $d['maps'];
	$m_length = count($maps);
	
	$m = array_fill(0, $m_length, 0);
	
	foreach($players as $player) {
		if ($player['map'] < $m_length) {
			++$m[$player['map']];
		}
	}
	foreach ($m as $key => $value) {
		$d['maps'][$key]['votes'] = $value;
	}
	
	return $d;
}
function reindex($a) {
    $new = array();

    foreach ($a as $key => $value) {
        if (is_array($value)) {
           $new[$key] = array_values($value);
        }
    }

    return $new;
}
?>