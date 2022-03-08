<?php
defined('_init') or die();

# set total
$total = 1505;

# set query
$a = 'SELECT uid, vid, issued_date, expire_date, expire_details, expire_details_date, code, visited, printed, pop, received FROM #__users_vouchers_test WHERE :date <= expire_date ORDER BY id ASC LIMIT '.$counter.', '.$max;
$b = array('date' => $date);
$get = $db->_query($dbc, 'searchAll', $a, $b);

for($i = 0; $i < $max; $i++){
    if(isset($get[$i])){
        # get their details
        /*
        $a = 'SELECT id FROM #__users WHERE id = :uid';
        $b = array('uid' => $get[$i]->uid);
        $chk = $db->_query($dbc, 'searchOne', $a, $b);
        */

        # if($chk){
            $a = 'INSERT INTO #__users_vouchers (cid, uid, vid, issued_date, expire_date, expire_details, expire_details_date, code, visited, printed, pop, received) VALUES (:cid, :uid, :vid, :issued_date, :expire_date, :expire_details, :expire_details_date, :code, :visited, :printed, :pop, :received)';
            $b = array('cid' => 1, 'uid' => $get[$i]->uid, 'vid' => $get[$i]->vid, 'issued_date' => $get[$i]->issued_date, 'expire_date' => $get[$i]->expire_date, 'expire_details' => $get[$i]->expire_details, 'expire_details_date' => $get[$i]->expire_details_date, 'code' => $get[$i]->code, 'visited' => $get[$i]->visited, 'printed' => $get[$i]->printed, 'pop' => $get[$i]->pop, 'received' => $get[$i]->received);
            $insert = $db->_query($dbc, 'insert', $a, $b);

            if($insert){
                $opt['body'] .= $get[$i]->uid.' - completed';
                $opt['body'] .= '<br/>';
            }
            else{
                $opt['body'] .= $get[$i]->uid.' - incomplete';
                $opt['body'] .= '<br/>';
            }
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