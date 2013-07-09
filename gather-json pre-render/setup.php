<?php
$f_0 = './global.txt';
$fh_0 = fopen($f_0, 'w');
fwrite($fh_0, (string)1);
fclose($fh_0);

$f_1 = './gathers/1.json';
$fh_1 = fopen($f_1, 'w');
$c_1 = '{"gather":{"id":1,"game_server":"255.255.255.255:27015 pug","voice_server":"255.255.255.255:27016 voice","voice_type":"mumble","status":"wait"},"players":{},"maps":[{"name":"ns2_summit","votes":0},{"name":"ns2_veil","votes":0},{"name":"ns2_tram","votes":0},{"name":"ns2_refinery","votes":0},{"name":"ns2_mineshaft","votes":0},{"name":"ns2_docking","votes":0}]}';
fwrite($fh_1, $c_1);
fclose($fh_1);

$f_2 = './gathers/1.txt';
$fh_2 = fopen($f_2, 'w');
$c_2 = 'Welcome to Gather #1.<br />';
fwrite($fh_2, $c_2);
fclose($fh_2);

$f_3 = './gathers/1.js';
$f_h3 = fopen($f_3, 'w');
$c_3 = 'var g = false;var r = \'wait\';if ($.cookie(\'gather\') !== null) {g = true;}$(\'div#gameserver\').html(\'<a href="steam://run/4920//connect 255.255.255.255:27015 pug">steam://run/4920//connect 255.255.255.255:27015 pug</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#voiceserver\').html(\'<a href="mumble://255.255.255.255:27016 voice">mumble://255.255.255.255:27016 voice</a> (<a class="edit" href="javascript:;">Edit</a>)\');$(\'div#status\').html(\'Waiting for 12 more.\');$(\'span#playercount\').html(\'0\');$(\'ul#playerlist\').html(\'\');if (g) {$(\'ul#maplist\').html(\'<li><a href="vote.php?id=0&type=map">ns2_summit</a> (0)</li><li><a href="vote.php?id=0&type=map">ns2_veil</a> (0)</li><li><a href="vote.php?id=0&type=map">ns2_tram</a> (0)</li><li><a href="vote.php?id=0&type=map">ns2_refinery</a> (0)</li><li><a href="vote.php?id=0&type=map">ns2_mineshaft</a> (0)</li><li><a href="vote.php?id=0&type=map">ns2_docking</a> (0)</li>\');} else {$(\'ul#maplist\').html(\'<li>ns2_summit</a> (0)</li><li>ns2_veil</a> (0)</li><li>ns2_tram</a> (0)</li><li>ns2_refinery</a> (0)</li><li>ns2_mineshaft</a> (0)</li><li>ns2_docking</a> (0)</li>\');}';
fwrite($f_h3, $c_3);
fclose($f_h3);

chmod($f_0, 0766);
chmod($f_1, 0766);
chmod($f_2, 0766);
chmod($f_3, 0766);
?>