<?php
defined('_init') or die();

# set total
$total = 217905;

# set query
$a = 'SELECT uid, players_card, vpc_issued FROM #__users_test_ext WHERE 1 ORDER BY id ASC LIMIT '.$counter.', '.$max;
$get = $db->_query($dbc, 'searchAll', $a);

for($i = 0; $i < $max; $i++){
	if(isset($get[$i])){
		# get their details
		$a = 'SELECT id FROM #__users WHERE id = :uid';
		$b = array('uid' => $get[$i]->uid);
		$chk = $db->_query($dbc, 'searchOne', $a, $b);
		
		if($chk){
			$a = 'INSERT INTO #__users_playerscard (cid, uid, playerscard, verified_at, updated_at) VALUES (:cid, :uid, :playerscard, :verified_at, :updated_at)';
			$b = array('cid' => 1, 'uid' => $get[$i]->uid, 'playerscard' => $get[$i]->players_card, 'verified_at' => date('Y-m-d', strtotime($get[$i]->vpc_issued)), 'updated_at' => $get[$i]->vpc_issued);
			$insert = $db->_query($dbc, 'insert', $a, $b);
			
			if($insert){
				$opt['body'] .= $get[$i]->uid.' - completed';
				$opt['body'] .= '<br/>';
			}
			else{
				$opt['body'] .= $get[$i]->uid.' - incomplete';
				$opt['body'] .= '<br/>';
			}
		}
		
	}
	
	#
	# no need to mod below
	#
	$data = $counter;
	$counter++;
	
	if($counter > $total){
		break;
	}
}