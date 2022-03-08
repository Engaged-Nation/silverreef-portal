<?php
defined('_drawing') or die();

#
# set live
# define prizes
# define amount of winners
# define emails
#
#

# is it live ?
$live = $enConfigClient['environment']['live'];

# set prize - format - number of winners, voucher id
$prizes = array(
    array(1, 4821),
    array(2, 4822),
    array(5, 4823),
    array(10, 4824),
    array(10, 4825),
    array(5, 4826),
    array(10, 4827),
    array(15, 4828),
    array(20, 4829),
);

# title of drawing
$title = 'Wheel Game';

# header on wheel game tab
$tab_header = '<h3>Some Title Goes Here</h3>';

# description on wheel game tab
$tab_description = 'Spin daily to earn entries into our Friday drawings for prizes';

# current clientid;
$cid = 1;

# which component will it consider for drawings ?
$reference = 'wheelgame';

# set access
$access = 'qxTqF3etTCq98Egr';

# email to, separated by commas
$email = array(
    'developers@engagednation.com' => '',
    'stella@engagednation.com' => '',
    'kristan@engagednation.com' => '',
    );

# test email
/*
$email = 'developers@engagednation.com';
*/
