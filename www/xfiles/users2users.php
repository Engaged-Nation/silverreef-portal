<?php
defined('_init') or die();

# set total
$total = 76930;

# set query
$a = 'SELECT * FROM #__users_test WHERE 1 ORDER BY id ASC LIMIT '.$counter.', '.$max;
$get = $db->_query($dbc, 'searchAll', $a);

for($i = 0; $i < $max; $i++){
	if(isset($get[$i])){
		# get their details
		$a = 'SELECT * FROM #__users_test_ext WHERE uid = :uid';
		$b = array('uid' => $get[$i]->id);
		$ext = $db->_query($dbc, 'searchOne', $a, $b);
		
		# if($ext){
            $dob = '0000-00-00';
			if($ext && $ext->birthdate){
				$dob = $ext->birthdate;
			}

            $zipcode = '';
			if($ext && $ext->zipcode){
				$zipcode = $ext->zipcode;
			}

			$fname = '';
			if($ext && $ext->first_name){
                $fname = $ext->first_name;
            }

            $lname = '';
            if($ext && $ext->last_name){
                $lname = $ext->last_name;
            }

            $mname = '';
            if($ext && $ext->mid_name){
                $mname = $ext->mid_name;
            }

            $opt_sms = 0;
            if($ext && $ext->opt_sms){
                $opt_sms = $ext->opt_sms;
            }

            $phone = '';
            if($ext && $ext->phone){
                $phone = $ext->phone;
            }

            $selfx = 1;
            if($ext && $ext->self_exclusion){
                $selfx = $ext->self_exclusion;
            }

			
			# insert into new table
			$a = 'INSERT INTO #__users_new (id, cid, type, grp, points, name, fname, mname, lname, nickname, username, email, password, dob, zipcode, optin_sms, phone, register_date, last_login, comments, ip, browser, self_exclusion, enabled) VALUES (:id, :cid, :type, :grp, :points, :name, :fname, :mname, :lname, :nickname, :username, :email, :password, :dob, :zipcode, :optin_sms, :phone, :register_date, :last_login, :comments, :ip, :browser, :self_exclusion, :enabled)';
			$b = array(
				'id' => $get[$i]->id,
				'cid' => 1,
				'type' => strtolower($get[$i]->type),
				'grp' => $get[$i]->grp_name,
				'points' => $get[$i]->points,
				'name' => ucwords($fname).' '.ucwords($lname),
				'fname' => ucwords($fname),
				'mname' => $mname,
				'lname' => ucwords($lname),
				'nickname' => $get[$i]->nickname,
				'username' => $get[$i]->username,
				'email' => $get[$i]->email,
				'password' => $get[$i]->password,
				'dob' => $dob,
				'zipcode' => $zipcode,
				'optin_sms' => $opt_sms,
				'phone' => $phone,
				'register_date' => $get[$i]->register_date,
				'last_login' => $get[$i]->last_login,
				'comments' => $get[$i]->comments,
				'ip' => $get[$i]->ip,
				'browser' => $get[$i]->platform,
				'self_exclusion' => $selfx,
				'enabled' => $get[$i]->enabled,
			);
			$insert = $db->_query($dbc, 'insert', $a, $b);
			
			if($insert){
				$opt['body'] .= $get[$i]->id.' - completed';
				$opt['body'] .= '<br/>';
			}
			else{
				$opt['body'] .= $get[$i]->id.' - incomplete';
				$opt['body'] .= '<br/>';
			}
			
			
		# }
		# else{
			$opt['body'] .= $get[$i]->id.' - ext not found';
			$opt['body'] .= '<br/>';
		# }
		
		
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