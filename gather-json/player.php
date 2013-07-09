<?php
include('./inc/gather.php');

if ( isset($_GET['action']) && isset($_POST['gather']) &&  isset($_POST['id']) ) {
	$gather = new Gather($_POST['gather']);
	
	if ( $_GET['action'] == 'join' ) {
		if ( isset($_POST['name']) && isset($_POST['avatar']) ) {
			$gather->addPlayer($_POST['id'], $_POST['name'], $_POST['avatar']);
		} else {
			header('Location: index.php');
			exit;
		}
	} elseif ( $_GET['action'] == 'leave' ) {
		$gather->removePlayer($_POST['id']);
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