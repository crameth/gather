<?php
if ( isset($_COOKIE['steamID']) ) {
	$steamID = $_COOKIE['steamID'];
}
if ( isset($_COOKIE['gather']) ) {
	$gather_id = $_COOKIE['gather'];
}



if ($auth) {
	return true;
} else {
	return false;
}
?>