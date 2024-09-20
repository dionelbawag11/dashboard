<?php
/**
*  aMember Pro Config File
*
*     Author: Alex Scott
*      Email: alex@cgi-central.net
*        Web: http://www.amember.com/
*    FileName $RCSfile$
*    Release: 6.3.20 ($Revision$)
*
* Please direct bug reports,suggestions or feedback to the cgi-central forums.
* http://www.cgi-central.net/forums
*
* aMember PRO is a commercial software. Any distribution is strictly prohibited.
*/

const AM_USE_NEW_CSS = 1;
define('AM_SESSION_NAME', @session_name() ?: 'PHPSESSID');

return [
    'db' => [
        'mysql' => [
            'db'    => 'moodle400',
            'user'  => 'moodle',
            'pass'  => 'moodle',
            'host'  => 'localhost',
            'prefix' => 'am_',
            'port'  => '',
            'pdo_options' => [],
        ],
    ],
    'cache' => [
        'type' => '',
        'options' => []
    ]
];