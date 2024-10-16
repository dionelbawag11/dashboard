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
            'db'    => '@DB_MYSQL_DB@',
            'user'  => '@DB_MYSQL_USER@',
            'pass'  => '@DB_MYSQL_PASS@',
            'host'  => '@DB_MYSQL_HOST@',
            'prefix' => '@DB_MYSQL_PREFIX@',
            'port'  => '@DB_MYSQL_PORT@',
            'pdo_options' => [],
        ],
    ],
    'cache' => [
        'type' => '',
        'options' => []
    ]
];