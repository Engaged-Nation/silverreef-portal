<?php
$allowed = array('174.71.253.244');
if(!in_array($_SERVER['REMOTE_ADDR'], $allowed)){
	die('What does the fox say?');
}

# extend script life time
set_time_limit(0);
date_default_timezone_set('America/Los_Angeles');

# set headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");

return;

include 'xcon.php';

$date = date('Y-m-d');
$datetime =  date('Y-m-d H:i:s');

$array = array(
	'Gamification Solution',
	'Leadership Plan',
	'Brand Recognition',
	'Redeemable Program',
	'Customer Loyalty',
	'Interactive Website',
	'Informative Experience',
	'Educate Players',
	'Fun Platform',
	'Social Interaction',
);

$x = 0;
$num = 1;
for($i = 0; $i < count($array); $i++){
	# process registration
	$cid = 1;
	$group = 'Group 0';
	$points = 475000;
	$username = 'kiosk'.sprintf('%02d', $num);
	$password = hash_password::hash($username, $enConfigClient['environment']['database_secret_key']);
	
	$n = explode(' ', $array[$i]);
	$nickname = $username.'.'.$n[0];
	
	
	$a = 'INSERT INTO #__users (cid, type, grp, points, name, fname, lname, nickname, username, email, password, dob, city, state, register_date, enabled) VALUES (:cid, :type, :grp, :points, :name, :fname, :lname, :nickname, :username, :email, :password, :dob, :city, :state, :register_date, :enabled)';
	$b = array('cid' => $cid, 'type' => 'registered', 'grp' => $group, 'points' => $points, 'name' => $array[$i], 'fname' => $n[0], 'lname' => $n[1], 'nickname' => $nickname, 'username' => $username, 'email' => $username.'@engagednation.com', 'password' => $password, 'dob' => $date, 'city' => 'Las Vegas', 'state' => 'Nevada', 'register_date' => $datetime, 'enabled' => 1);
	# $insert = $db->_query($dbc, 'insert', $a, $b, true);

	if($insert){
		# insert to players card
		$a = 'INSERT INTO #__users_playerscard (cid, uid, updated_at) VALUES (:cid, :uid, :updated_at)';
		$b = array('cid' => $cid, 'uid' => $insert, 'updated_at' => $datetime);
		# $insert = $db->_query($dbc, 'insert', $a, $b);
	}
	else{
		echo 'e'.$i.'<br/>';
		break;
	}
	
	$num++;
}


$db = $dbc = null;

echo $i.'/'.count($array);
