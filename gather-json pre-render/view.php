<?php // a js file in disguise
$players = $this->data['players'];
$maps = $this->data['maps'];

$num_players = count($players);
$num_players_missing = 12 - $num_players;
$num_maps = count($maps);
$status = $this->status();

$c = 'var g = false;var r = \''.$status.'\';if ($.cookie(\'gather\') !== null) {g = true;}$(\'div#gameserver\').html(\'<a href="steam://run/4920//connect '.$this->getGameServer().'">steam://run/4920//connect '.$this->getGameServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#voiceserver\').html(\'<a href="'.$this->voiceType().'://'.$this->getVoiceServer().'">'.$this->voiceType().'://'.$this->getVoiceServer().'</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#status\').html(\'';

if ( $status === 'wait' ) {
	$c = $c.'Waiting for '.$num_players_missing.' more.';
} elseif ( $status === 'vote' ) {
	$c = $c.'Voting for captains.';
} elseif ( $status === 'play' ) {
	$c = $c.'Game played.';
}
$c = $c.'\');$(\'span#playercount\').html(\''.$num_players.'\');$(\'ul#playerlist\').html(\'';
foreach ($players as $player) {
	$c = $c.'<li><img src="'.$player['avatar'].'" alt="" /><a href="http://steamcommunity.com/profiles/'.$player['id'].'">'.$player['name'].'</a></li>';
}
$c = $c.'\');if (g) {$(\'ul#maplist\').html(\'';
foreach ($maps as $map) {
	$c = $c.'<li><a href="vote.php?id='.key($maps).'&type=map">'.$map['name'].'</a> ('.$map['votes'].')</li>';
}
$c = $c.'\');} else {$(\'ul#maplist\').html(\'';
foreach ($maps as $map) {
	$c = $c.'<li>'.$map['name'].'</a> ('.$map['votes'].')</li>';
}
$c = $c.'\');}';
?>