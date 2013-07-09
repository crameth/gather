<?php
if ( !isset($_POST['gather']) || !isset($_POST['name']) || !isset($_POST['message']) ) {
	header('Location: index.php');
}

$message = '['.date('H:i').'] '.htmlspecialchars($_POST['name'].': '.$_POST['message']).'<br />';

$f = './gathers/'.$_POST['gather'].'.txt';
$f_h = fopen($f, 'a');
fwrite($f_h, $message);
fclose($f_h);

$r = file_get_contents('./gathers/'.$_POST['gather'].'.txt') or die('ERROR: cannot open chat log');
echo $r;
?>