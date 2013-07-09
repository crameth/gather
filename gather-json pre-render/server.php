<?php
include('./inc/gather.php');

if ( isset($_POST['gather']) &&
	isset($_POST['type']) &&
	isset($_POST['ip']) ) {
	
	$gather = new Gather($_POST['gather']);
	
	if ( $_POST['type'] === 'voice' ) {
		if ( isset($_POST['voice_type']) ) {
			$gather->voiceType($_POST['voice_type']);
		}
		$gather->setVoiceServer($_POST['ip'], $_POST['port'], $_POST['password']);
		
	} elseif ( $_POST['type'] === 'game' ) {
		$gather->setGameServer($_POST['ip'], $_POST['port'], $_POST['password']);
	} else {
		header('Location: index.php');
		exit;
	}
	
	$gather->write($_POST['gather']);
	
	header('Location: index.php');
	exit;
} else {
	header('Location: index.php');
	exit;
}
?>