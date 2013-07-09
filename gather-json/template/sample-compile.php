<?php
$players = $this->data['players'];
$maps = $this->data['maps'];

$num_players = count($players);
$num_players_missing = 12 - $num_players;
$num_maps = count($maps);

$c = '';

// init
$c = $c.'var g = false,if ($.cookie(\'gather\') !== null) {g = true;}';

// game server
$c = $c.'$(\'div#gameserver\').html(\'<a href="steam://run/4920//connect '.$gather->getGameServer().'">steam://run/4920//connect '.$gather->getGameServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');';

// voice server
$c = $c.'$(\'div#voiceserver\').html(\'<a href="'.$gather->voiceType().'://'.$gather->getVoiceServer().'">'.$gather->voiceType.'://'.$gather->getVoiceServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');';

// status
$c = $c.'$(\'div#status\').html(\'';
$status = $gather->status();
if ( $status === 'wait' ) {
	$c = $c.'$(\'div#status\').html(\'Waiting for '.$num_players_missing.' more.';
} elseif ( $status === 'play' ) {
	$c = $c.'Game in progress.';
} elseif ( $status === 'played' ) {
	$c = $c.'Game played.';
}
$c = $c.'\');';

// player count
$c = $c.'$(\'span#playercount\').html(\''.$num_players.'\');';

// player list
$c = $c.'$(\'ul#playerlist\').html(\'';
foreach ($players as $player) {
	$c = $c.'<li><img src="'.$player['avatar'].'" alt="" /><a href="http://steamcommunity.com/profiles/'.$player['id'].'">'.$player['name'].'</a></li>';
}
$c = $c.'\');';

// map list
$c = $c.'if (g) {$(\'ul#maplist\').html(\'';
foreach ($maps as $map) {
	$c = $c.'<li><a href="vote.php?id='.$map['id'].'&type=map">'.$map['name'].'</a> ('.$map['votes'].')</li>';
}
$c = $c.'\');} else {$(\'ul#maplist\').html(\'';
foreach ($maps as $map) {
	$c = $c.'<li>'.$map['name'].'</a> ('.$map['votes'].')</li>';
}
$c = $c.'\');}';
?>