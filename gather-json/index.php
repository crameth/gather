<?php
include('./inc/functions.php');
include('./inc/user.php');
include('./inc/gather.php');

$user = new User;
$user->key = '14328A86A4238CE9B9E37EB4D5729C9A';
$user->domain = 'neue.vtsuki.net/gather';

if ( isset($_GET['login']) ) {
	$user->login();
}
if ( array_key_exists('logout', $_POST) ) {
	if ( isset($_COOKIE['gather']) ) {
		die('Cannot leave as user is in a gather');
	} else {
		setcookie('steamID', '', -1, '/');
		header('Location: /gather');
	}
}
if ( isset($_COOKIE['steamID']) ) {
	$user_id = $_COOKIE['steamID'];
}

if ( isset($_COOKIE['gather']) ) {
	$gather_id = $_COOKIE['gather'];
} elseif ( isset($_GET['gather'] ) ) {
	$gather_id = $_GET['gather'];
} else {
	$gather_id = (int)file_get_contents('./global.txt') or die('Cannot open globals.txt.');
}

$gather = new Gather($gather_id);
?>
<!DOCTYPE html>
<head>
<title>Asia NS2 Gather #<?php echo $gather_id; ?></title>

<link rel="stylesheet" type="text/css" media="all" href="style.css" />
<link href='http://fonts.googleapis.com/css?family=Metrophobic' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="assets/jquery.cookie.min.js"></script>
<script type="text/javascript" src="assets/jquery.periodicalupdater.min.js"></script>
<script type="text/javascript" src="assets/jquery.jplayer.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	var p = false;
	$.PeriodicalUpdater('view.php', {
		method: 'GET',
		type: 'script',
		minTimeout: 4000,
		maxTimeout: 16000,
		success: function() {
			if (r == 'vote') {
				if (!p) {
					$('div#jplayer').jPlayer({
						ready: function() {
							$(this).jPlayer('setMedia', {mp3: 'assets/power.mp3'}).jPlayer('play');
							var c = document.ontouchstart === undefined ? 'c' : 'touchstart';
							var k = function () {
								$('div#jplayer').jPlayer('play');
								document.documentElement.removeEventListener(c, k, true);
							};
							document.documentElement.addEventListener(c, k, true);
						},
						loop: false,
						volume: 1.0,
						swfPath: 'assets/'
					});
					p = true;
					alert('Gather started. Close this alert to disable music.');
					$('div#jplayer').jPlayer('stop');
				}
			}
		}
    });
	$.PeriodicalUpdater('gathers/'+<?php echo $gather_id; ?>+'.txt', {
		method: 'GET',
		minTimeout: 1000,
		success: function(log) {
			$('div#log').html(log);
			$('div#log').scrollTop = $('div#log').scrollHeight;
		}
    });
	
	$('div#log').scrollTop = $('div#log').scrollHeight;

	$('div#gameserver a.edit').click(function(e) {
		$obj = $(this).parent();
		$obj.unbind('click');
		$obj.hide();
		$('div#editgameserver').show();
	});
	
	$('div#voiceserver a.edit').click(function(e) {
		$obj = $(this).parent();
		$obj.unbind('click');
		$obj.hide();
		$('div#editvoiceserver').show();
	});
});
</script>
</head>

<body>

<div id="jplayer"></div>

<header>
	<h1>Asia NS2 Gathers</h1>
</header>

<section id="head">
	<div>
	<?php if ( isset($user_id) ) { ?>
		<?php $profile = $user->getPlayer($user_id);
		
		$name = $profile->personaname;
		$avatar = $profile->avatar;
		$avatar_full = $profile->avatarmedium;
		?>
		<img src="<?php echo $avatar_full; ?>" />
		You're logged in as: <?php echo $name; ?> (<?php echo $user_id; ?>)
		<?php if ( isset($_COOKIE['gather']) ) { ?>
		<form action="player.php?action=leave" method="POST">
			<input name="gather" type="hidden" value="<?php echo $gather_id; ?>" />
			<input name="id" type="hidden" value="<?php echo $user_id; ?>" />
			<input type="submit" value="Leave Gather" />
		</form>
		<?php } else { ?>
		<form action="player.php?action=join" method="POST">
			<input name="gather" type="hidden" value="<?php echo $gather_id; ?>" />
			<input name="id" type="hidden" value="<?php echo $user_id; ?>" />
			<input name="name" type="hidden" value="<?php echo $name; ?>" />
			<input name="avatar" type="hidden" value="<?php echo $avatar; ?>" />
			<input type="submit" value="Join Gather" />
		</form>
		<form action="?logout" method="GET">
			<button title="Logout" name="logout">Logout</button>
		</form>
		<?php } ?>
	<?php } else { ?>
		<form action="?login" method="POST">
			<input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_border.png"/>
		</form>
	<?php } ?>
	</div>
	<div class="clear"></div>
</section>

<section id="view">
	<div id="gather" class="col">
		<h1>Gather #<?php echo $gather_id; ?></h1>
		<h3>Game Server:</h3>
		<div id="editgameserver" style="display:none;">
			<form action="server.php" method="POST">
				<input name="gather" type="hidden" value="<?php echo $gather_id; ?>" />
				<input name="type" type="hidden" value="game" />
				<input name="ip" type="text" value="<?php echo $gather->getGameServer('ip'); ?>" />
				<input name="port" type="text" value="<?php echo $gather->getGameServer('port'); ?>" />
				<input name="password" type="text" value="<?php echo $gather->getGameServer('password'); ?>" />
				<input type="submit" value="Update" />
			</form>
		</div>
		<div id="gameserver">
			<a href="steam://run/4920//connect <?php echo $gather->getGameServer(); ?>">steam://run/4920//connect <?php echo $gather->getGameServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)
		</div>
		<h3>Voice Server:</h3>
		<div id="editvoiceserver" style="display:none;">
			<form action="server.php" method="POST">
				<input name="gather" type="hidden" value="<?php echo $gather_id; ?>" />
				<input name="type" type="hidden" value="voice" />
				<select name="voice_type">
					<option value="0">Mumble</option>
					<option value="1">Teamspeak</option>
				</select>
				<input name="ip" type="text" value="<?php echo $gather->getVoiceServer('ip'); ?>" />
				<input name="port" type="text" value="<?php echo $gather->getVoiceServer('port'); ?>" />
				<input name="password" type="text" value="<?php echo $gather->getVoiceServer('password'); ?>" />
				<input type="submit" value="Update" />
			</form>
		</div>
		<div id="voiceserver">
			<a href="<?php echo $gather->voiceType(); ?>://<?php echo $gather->getVoiceServer(); ?>"><?php echo $gather->voiceType(); ?>://<?php echo $gather->getVoiceServer(); ?></a> (<a class="edit" href="javascript:;">Edit</a>)
		</div>
		<h3>Status:</h3>
		<div id="status">
			<?php $status = $gather->status(); ?>
			<?php if ( $status === 'wait' ) { ?>
			Waiting for <?php echo (string)(12 - (int)$gather->getNumPlayers()); ?> more.
			<?php } elseif ( $status === 'play' ) { ?>
			Game is in progress.
			<?php } elseif ( $status === 'played' ) { ?>
			Game played.
			<?php } ?>
		</div>
	</div>
	<div id="players" class="col">
		<h1>Players (<span id="playercount"><?php echo $gather->getNumPlayers(); ?></span>)</h1>
		<ul id="playerlist">
			<?php $players = $gather->getAllPlayers(); ?>
			<?php foreach ($players as $player) { ?>
			<li><img src="<?php echo $player['avatar']; ?>" alt="" /><a href="http://steamcommunity.com/profiles/<?php echo $player['id']; ?>"><?php echo $player['name']; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div id="maps" class="col">
		<h1>Maps</h1>
		<ul id="maplist">
			<?php $maps = $gather->getAllMaps(); ?>
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
</section>

<section id="chat">
	<div id="log">
		<?php $chat = file_get_contents('./gathers/'.$gather_id.'.txt'); echo $chat; ?>
	</div>
	<?php if ( isset($user_id) ) { ?>
	<form id="send">
		<input id="message" name="message" type="text" value="" />
		<input name="gather" type="hidden" value="<?php echo $gather_id; ?>" />
		<input name="name" type="hidden" value="<?php echo $name; ?>" />
		<input type="submit" value="Send" />
	</form>
	<script type="text/javascript">
	$('#send').submit(function(event) {
		if ( $('#message').val().length > 0 ) {
			var $form = $(this);
			var $input = $form.find('input, select, button, textarea');
			var values = $form.serialize();

			$input.prop('disabled', true);
			
			var request = $.ajax({
				url: 'chat.php',
				type: 'post',
				data: values,
				cache: false,
				success: function(log) {
					$('div#log').html(log)
					$('#message').val('');
				}
			});
			request.always(function () {
				$input.prop('disabled', false);
			});
		} else {
			alert('Please enter a proper message.');
		}
		event.preventDefault();
	});
	</script>
	<?php } ?>
	<div class="clear"></div>
</section>

<section id="footer">
	<div id="notice">Javascript and cookies must be enabled.</div>
	<div id="tester">Testers: Help to test performance to see if it is fast enough to update your data once you join or leave the gather. Also looking for proper ideas to help with the layout.</div>
</section>

</body>

</html>