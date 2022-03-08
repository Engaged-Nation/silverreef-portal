<?php
/*
$allowed = array('24.234.137.217');
if(!in_array($_SERVER['REMOTE_ADDR'], $allowed)){
	die('What does the fox say?');
}
*/

# extend script life time
set_time_limit(0);
date_default_timezone_set('America/Los_Angeles');

# set headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");

#echo date('Y-m-d h:i:s');

#return;

$pgl = microtime();
$pgl = explode(' ', $pgl);
$spgl = $pgl[1] + $pgl[0];

$x = intval($_GET['x']);
$t = intval($_GET['t']);
$z = intval($_GET['z']);

$min = $x;
$max = $t;
$data = false;
$opt = [];
$opt['body'] = '';
$counter = $z;

include 'xcon.php';

$date = date('Y-m-d');
$datetime =  date('Y-m-d H:i:s');

include 'ajax.include.php';

$db = $dbc = null;

if($data < $total){
	$opt['next'] = '<div style="background: red;">'.($counter -1).' / '.$total;
}
else{
	$opt['body'] .= ($counter -1).' / '.$total;
}

$pgl = microtime();
$pgl = explode(' ', $pgl);
$pgl = $pgl[1] + $pgl[0];
$epgl = $pgl;
$pgl = round(($epgl - $spgl), 4);

$opt['pgl'] = ', '.$pgl.'ms</div>';
$opt['x'] = $i + 1;
$opt['t'] = $max;
$opt['z'] = $counter;
echo json_encode($opt);
