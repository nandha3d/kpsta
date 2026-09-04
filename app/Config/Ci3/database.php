<?php

/*
 | CI3-format view of the database settings, for the one caller that reads them
 | as config: the admin Backup controller, which shells out to mysqldump and
 | needs the host/user/password/name.
 |
 | Values are read back from CodeIgniter 4's own Config\Database so there is a
 | single source of truth (.env), rather than a second copy to keep in step.
 */

$ci4 = new \Config\Database();

$config['db_config'] = [
    'default'    => [
        'hostname' => $ci4->default['hostname'] ?? 'localhost',
        'username' => $ci4->default['username'] ?? '',
        'password' => $ci4->default['password'] ?? '',
        'database' => $ci4->default['database'] ?? '',
        'port'     => $ci4->default['port'] ?? 3306,
    ],
    'membership' => [
        'hostname' => $ci4->membership['hostname'] ?? 'localhost',
        'username' => $ci4->membership['username'] ?? '',
        'password' => $ci4->membership['password'] ?? '',
        'database' => $ci4->membership['database'] ?? '',
        'port'     => $ci4->membership['port'] ?? 3306,
    ],
];
