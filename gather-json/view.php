<?php
// don't touch header
$gmt_mtime = gmdate('r', $timestamp);
header('ETag: "'.md5($timestamp.$file).'"');
header('Last-Modified: '.$gmt_mtime);
header('Cache-Control: public');

if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
	if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($timestamp.$file)) {
		header('HTTP/1.1 304 Not Modified');
		exit();
	}
}

// session
session_start();

$auth = true;

// init
include('./inc/gather.php');

if ( isset($_COOKIE['gather']) ) {
	$g_id = $_COOKIE['gather'];
} else {
	$g_id = (int)file_get_contents('./global.txt') or die('Cannot open global.txt.');
}
$g = new Gather($g_id);

// start generation
$ps = $g->getAllPlayers();
$ms = $g->getAllMaps();

$num_ps = count($ps);
$num_ps_missing = 12 - $num_ps;
$num_ms = count($ms);

$status = $g->status();
$status_text = null;

switch ($status) {
	case 'wait':
	$status_text = 'Waiting for '.$num_ps_missing.' more.';
	break;
	case 'vote':
	$status_text = 'Vote for captains by clicking on their names.';
	break;
	case 'pick':
	$status_text = 'Wait for captains to pick players.';
	break;
	case 'play':
	$status_text = 'Gather is in progress.';
	break;
	default:
	$status_text = 'Gather status error.';
}
?>
var r = '<?php echo $status; ?>';
$('div#gameserver').html('<a href="steam://run/4920//connect <?php echo $g->getGameServer(); ?>">steam://run/4920//connect <?php echo $g->getGameServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)');
$('div#voiceserver').html('<a href="<?php echo $g->voiceType(); ?>://<?php echo $g->getVoiceServer(); ?>"><?php echo $g->voiceType(); ?>://<?php echo $g->getVoiceServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)');
$('div#status').html('<?php echo $status_text; ?>');
$('span#playercount').html('<?php echo $num_ps; ?>');

$('ul#playerlist').html('
<?php // add user auth here for captain picking ?>
<?php // for player pool picking, we will modify 2 new columns (to be implemented in index.php) ?>
<?php if ($auth) { ?>
	<?php foreach ($ps as $p) { ?>
		<li><img src="<?php echo $p['avatar']; ?>" alt="" /><a href="vote.php?id=<?php echo key($ps); ?>&type=cap"><?php echo $p['name']; ?></a></li>
	<?php } ?>
<?php } else { ?>
	<?php foreach ($ps as $p) { ?>
		<li><img src="<?php echo $p['avatar']; ?>" alt="" /><a href="http://steamcommunity.com/profiles/<?php echo $p['id']; ?>"><?php echo $p['name']; ?></a></li>
	<?php } ?>
<?php } ?>
');

<?php // add user auth here for map picking ?>
$('ul#maplist').html(';
<?php if ($auth) { ?>
	<?php foreach ($ms as $m) { ?>
		<li><a href="vote.php?id=<?php echo key($ms); ?>&type=map"><?php echo $m['name']; ?></a> (<?php echo $m['votes']; ?>)</li>
	<?php } ?>
<?php } else { ?>
	<?php foreach ($ms as $m) { ?>
		<li><?php echo $m['name']; ?> (<?php echo $m['votes']; ?>)</li>
	<?php } ?>
<?php } ?>
');



<div id="gather" class="col">
	<h1>Gather #<?php echo $g_id; ?></h1>
	<h3>Game Server:</h3>
	<div id="editgameserver" style="display:none;">
		<form action="server.php" method="POST">
			<input name="gather" type="hidden" value="<?php echo $g_id; ?>" />
			<input name="type" type="hidden" value="game" />
			<input name="ip" type="text" value="<?php echo $g->getGameServer('ip'); ?>" />
			<input name="port" type="text" value="<?php echo $g->getGameServer('port'); ?>" />
			<input name="password" type="text" value="<?php echo $g->getGameServer('password'); ?>" />
			<input type="submit" value="Update" />
		</form>
	</div>
	<div id="gameserver">
		<a href="steam://run/4920//connect <?php echo $g->getGameServer(); ?>">steam://run/4920//connect <?php echo $g->getGameServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)
	</div>
	<h3>Voice Server:</h3>
	<div id="editvoiceserver" style="display:none;">
		<form action="server.php" method="POST">
			<input name="gather" type="hidden" value="<?php echo $g_id; ?>" />
			<input name="type" type="hidden" value="voice" />
			<select name="voice_type">
				<option value="0">Mumble</option>
				<option value="1">Teamspeak</option>
			</select>
			<input name="ip" type="text" value="<?php echo $g->getVoiceServer('ip'); ?>" />
			<input name="port" type="text" value="<?php echo $g->getVoiceServer('port'); ?>" />
			<input name="password" type="text" value="<?php echo $g->getVoiceServer('password'); ?>" />
			<input type="submit" value="Update" />
		</form>
	</div>
	<div id="voiceserver">
		<a href="<?php echo $g->voiceType(); ?>://<?php echo $g->getVoiceServer(); ?>"><?php echo $g->voiceType(); ?>://<?php echo $g->getVoiceServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)
	</div>
	<h3>Status:</h3>
	<div id="status">
		<?php $status = $g->status(); ?>
		<?php if ( $status === 'wait' ) { ?>
		Waiting for <?php echo (string)(12 - (int)$g->getNumPlayers()); ?> more.
		<?php } elseif ( $status === 'play' ) { ?>
		Game is in progress.
		<?php } elseif ( $status === 'played' ) { ?>
		Game played.
		<?php } ?>
	</div>
</div>
<div id="players" class="col">
	<h1>Players (<span id="playercount"><?php echo $g->getNumPlayers(); ?></span>)</h1>
	<ul id="playerlist">
		<?php $players = $g->getAllPlayers(); ?>
		<?php foreach ($players as $player) { ?>
		<li><img src="<?php echo $player['avatar']; ?>" alt="" /><a href="http://steamcommunity.com/profiles/<?php echo $player['id']; ?>"><?php echo $player['name']; ?></a></li>
		<?php } ?>
	</ul>
</div>
<div id="maps" class="col">
	<h1>Maps</h1>
	<ul id="maplist">
		<?php $maps = $g->getAllMaps(); ?>
		<?php if ( isset($_COOKIE['gather']) ) { ?>
			<?php foreach ($maps as $map) { ?>
			<li><a href="vote.php?id=<?php echo $map['id']; ?>&type=map"><?php echo $map['name']; ?></a> (<?php echo $map['votes']; ?>)</li>
			<?php } ?>
		<?php } else { ?>
			<?php foreach ($maps as $map) { ?>
			<li><?php echo $map['name']; ?> (<?php echo $map['votes']; ?>)</li>
			<?php } ?>
		<?php } ?>
	</ul>
</div>
<div class="clear"></div>

