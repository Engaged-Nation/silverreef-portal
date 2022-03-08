<?php
defined('_init') or die();

# include files
include root . DS . 'lib/functions/global.php';

# include more files
include root . DS . 'lib/classes/encrypts.php';
include root . DS . 'lib/classes/user_auth.php';
include root . DS . 'lib/classes/user_session.php';

# get config
$cfg = $enConfigClient['frontend'];
