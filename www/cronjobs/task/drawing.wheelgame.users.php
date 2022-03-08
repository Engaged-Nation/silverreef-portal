<?php
use EN\PortalCore\Mailer\Client\Factory as MailerClientFactory;
use EN\PortalCore\Mailer\Message\Client as ClientMessage;

# set time limit - extend scripts lifetime
set_time_limit(0);

# set headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

define('_drawing', true);

# include settings
include 'drawing.wheelgame.users.settings.php';

# set timezone
date_default_timezone_set('America/Los_Angeles');

if(strtolower(date('l')) == 'friday' || !$live) {
    if(empty($reference)){
        die('reference is not defined');
    }

    include root . DS . 'lib/functions/global.php';

    if(get('x') != $access){
        die('you have no power here');
    }

    # get config
    $cfg = $enConfigClient['frontend'];

    /*
    if(!in_array(get_ip(), $cfg->allowedIPs)){
        die('you shall not pass');
    }
    */

    # check if this script already ran on the same day
    $statement = 'SELECT id FROM #__users_drawings WHERE drawings = :drawings AND drawn_date = :date';
    $b = array('drawings' => $title, 'date' => date('Y-m-d'));
    $check = $db->_query($dbc, 'searchOne', $statement, $b);

    if($check){
        die('todays drawing has already been completed');
    }

    # clean bucket table first
    $a = 'TRUNCATE #__users_drawing_bucket';
    $trunc = $db->_query($dbc, 'delete', $a);

    # if($live){
        # pull all entries
        $a = 'SELECT uid, SUM(points) AS total FROM #__users_details WHERE cid = :cid AND reference = :reference GROUP BY uid ORDER BY total DESC';
        $b = array('cid' => $cid, 'reference' => $reference);
        $details = $db->_query($dbc, 'searchAll', $a, $b);

        if($details){
        }
        else{
            die('no details found');
        }
    # }
    /*
    else{
        #
        # test mode
        # lets create imaginary entries
        # order by highest to lowest

        $details = array(
            array('uid' => 1, 'total' => 2500),
            array('uid' => 2, 'total' => 1500),
            array('uid' => 3, 'total' => 500),
        );

        # convert multidimensional array to object
        $details = json_decode(json_encode($details));
    }
    */

    if(isset($details)){
        $min = 0;
        $b = [];
        $total = count($details);
        $counter = 0;

        # insert their record first
        $a = 'INSERT INTO #__users_drawing_bucket (uid, min, max) VALUES ';
        $a1 = '';
        for($i = 0; $i < $total; $i++){
            # check if they are in filter
            $statement = 'SELECT id FROM #__users_filters WHERE uid = :uid';
            $b_tmp = array('uid' => $details[$i]->uid);
            $check = $db->_query($dbc, 'searchOne', $statement, $b_tmp);

            if($check){
                # if found do nothing
            }
            else {
                if ($a1 == ''){
                    $a1 .= '(?, ?, ?)';
                } else {
                    $a1 .= ', (?, ?, ?)';
                }

                $max = $min + $details[$i]->total;
                $tmp = array($details[$i]->uid, $min, $max);
                $b = array_merge($b, $tmp);
                $min = $max + 1;
            }
        }

        unset($details);

        $a .= $a1;
        $insert = $db->_query($dbc, 'insert', $a, $b);

        # set winner container
        $winner = [];
        $end = false;
        $table = '';

        for($a = 0; $a < count($prizes); $a++){
            for($x = 0; $x < $prizes[$a][0]; $x++){
                # generate a random number
                $random = mt_rand(1, $max);

                # return result based on min and max
                $statement = 'SELECT uid, min, max FROM #__users_drawing_bucket WHERE :num BETWEEN min AND max';
                $b = array('num' => $random);
                $getUid = $db->_query($dbc, 'searchOne', $statement, $b);

                $uid = $getUid->uid;
                $min_ = $getUid->min;
                $max_ = $getUid->max;

                while(in_array($uid, $winner)){
                    # generate a random number
                    $random = mt_rand(1, $max);

                    # return result based on min and max
                    $statement = 'SELECT uid, min, max FROM #__users_drawing_bucket WHERE :num BETWEEN min AND max';
                    $b = array('num' => $random);
                    $getUid = $db->_query($dbc, 'searchOne', $statement, $b);

                    $uid = $getUid->uid;
                    $min_ = $getUid->min;
                    $max_ = $getUid->max;
                }

                # get entries
                $entries = $max_ - $min_;

                # check to make sure their voucher is ready
                $statement = 'SELECT title, limit_time, limit_days FROM #__vouchers WHERE id = :vid';
                $b = array('vid' => $prizes[$a][1]);
                $voucher = $db->_query($dbc, 'searchOne', $statement, $b);

                if($voucher){
                    $issued = date('Y-m-d H:i:s');

                    # begin hash barcode
                    $vcode = substr(explode(':', generateIdHashSalt($uid, date('Ymdhis')))[1], 0, 15).'u'.$uid;

                    # insert to drawings
                    $statement = 'INSERT INTO #__users_drawings (cid, uid, vid, drawings, entries, code, issued_date, drawn_date) VALUES (:cid, :uid, :vid, :drawings, :entries, :code, :issued_date, :drawn_date)';
                    $b = array('cid' => $cid, 'uid' => $uid, 'vid' => $prizes[$a][1], 'drawings' => ucwords($title), 'entries' => $entries, 'code' => $vcode, 'issued_date' => $issued, 'drawn_date' => date('Y-m-d'));
                    $insert = $db->_query($dbc, 'insert', $statement, $b);

                    if($insert){
                        # insert to users vouchers
                        $statement = 'INSERT INTO #__users_vouchers (cid, uid, vid, issued_date, expire_date, expire_details, drawing, code) VALUES (:cid, :uid, :vid, :issued_date, :expire_date, :expire_details, :drawing, :code)';
                        $b = array('cid' => $cid, 'uid' => $uid, 'vid' => $prizes[$a][1], 'issued_date' => $issued, 'expire_date' => date('Y-m-d', strtotime('+' . $voucher->limit_days . 'days')), 'expire_details' => $voucher->limit_time, 'drawing' => 1, 'code' => $vcode);
                        $insert = $db->_query($dbc, 'insert', $statement, $b);

                        # get user info
                        $statement = 'SELECT u.name, u.email, u.grp, p.playerscard FROM #__users AS u LEFT JOIN #__users_playerscard AS p ON u.id = p.uid WHERE u.id = :id';
                        $b = array('id' => $uid);
                        $user = $db->_query($dbc, 'searchOne', $statement, $b);

                        if($a == 0){
                            $table .= '<table>';
                            $table .= '<tr>';
                            $table .= '<td>#</td>';
                            $table .= '<td>Prize Id</td>';
                            $table .= '<td>Prize Title</td>';
                            $table .= '<td>Name</td>';
                            $table .= '<td>Email</td>';
                            $table .= '<td>Group</td>';
                            $table .= '<td>Playerscard</td>';
                            $table .= '<td>Entries</td>';
                            $table .= '<tr>';
                        }

                        $table .= '<tr>';
                        $table .= '<td>'.($counter + 1).'</td>';
                        $table .= '<td>'.$prizes[$a][1].'</td>';
                        $table .= '<td>'.$voucher->title.'</td>';
                        $table .= '<td>'.$user->name.'</td>';
                        $table .= '<td>'.$user->email.'</td>';
                        $table .= '<td>'.$user->grp.'</td>';
                        $table .= '<td>'.$user->playerscard.'</td>';
                        $table .= '<td>'.$entries.'</td>';
                        $table .= '</tr>';
                    }
                    else {
                        echo 'database went away on voucher id '.$prizes[$a][1].' and user id '.$uid.'<br/>';
                    }
                }
                else{
                    echo 'voucher not found id '.$prizes[$a][1].'<br/>';
                }

                $winner[] = $uid;

                if(count($winner) >= $total){
                    $end = true;
                    break;
                }

                $counter++;
            }

            if($end){
                break;
            }
        }

        $table .= '</table>';

        $emailStr = '';
        foreach($email as $key => $val){
            $emailStr .= $key.', ';
        }
        echo 'successfully loaded. Check email sent to '.$emailStr;

        $isEmailSent = MailerClientFactory::get('client')->send((new ClientMessage($title.' Every Friday Drawings at '.date('Y-m-d')))->setBody($table)->setTo($email));

        if (!$isEmailSent) {
            echo 'failed sending email';
            return;
        }
    }

    # close connection
    $db = $dbc = null;
}
else{
    echo 'Not yet...';
}
