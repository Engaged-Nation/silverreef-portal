<?php
defined('_init') or die();

# set total
$total = 991538;

# set query
$a = 'SELECT * FROM #__users_details_test WHERE 1 ORDER BY id ASC LIMIT '.$counter.', '.$max;
$get = $db->_query($dbc, 'searchAll', $a);

for($i = 0; $i < $max; $i++){
	if(isset($get[$i])){
		# get their details
		$a = 'SELECT id FROM #__users WHERE id = :uid';
		$b = array('uid' => $get[$i]->uid);
		$chk = $db->_query($dbc, 'searchOne', $a, $b);
		
		if($chk){
			$a = 'INSERT INTO #__users_details (cid, uid, points, insert_date, title, reference) VALUES (:cid, :uid, :points, :insert_date, :title, :reference)';
			$b = array('cid' => 1, 'uid' => $get[$i]->uid, 'points' => $get[$i]->points, 'insert_date' => $get[$i]->insert_date, 'title' => $get[$i]->title, 'reference' => 'dailylogin');
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