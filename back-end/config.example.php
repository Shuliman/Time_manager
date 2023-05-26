<?php
return [
    'db' => [
        'host' => '',
        'dbname' => '',
        'username' => '',
        'password' => '',
        'tableName' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ],
    ],
    'time' => [ // time spended on project and learning after usining this time manager
        'project' => 0,
        'learning' => 0,
    ]
];
?>