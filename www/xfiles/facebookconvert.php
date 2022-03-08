<?php
defined('_init') or die();

# set total
$total = 5505;

# set query
$a = 'SELECT * FROM #__users_facebook_test WHERE 1 ORDER BY id ASC LIMIT '.$counter.', '.$max;
$get = $db->_query($dbc, 'searchAll', $a);

for($i = 0; $i < $max; $i++){
	if(isset($get[$i])){
		# get their details
		$a = 'SELECT id FROM #__users WHERE id = :uid';
		$b = array('uid' => $get[$i]->uid);
		$chk = $db->_query($dbc, 'searchOne', $a, $b);
		
		if($chk){
			$a = 'INSERT INTO #__facebook_users (uid, fid, fname, mname, lname, email, status, issued) VALUES (:uid, :fid, :fname, :mname, :lname, :email, :status, :issued)';
			$b = array('uid' => $get[$i]->uid, 'fid' => $get[$i]->fid, 'fname' => $get[$i]->fname, 'mname' => $get[$i]->mname, 'lname' => $get[$i]->lname, 'email' => $get[$i]->email, 'status' => $get[$i]->status, 'issued' => $get[$i]->insert_date);
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