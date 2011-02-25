<?php
/**
 * An associative array of information about this application.
 */
$ApplicationInfo['APIs'] = array(
   'Description' => "enable json output for vanilla",
   'Version' => '0.2',
   'RegisterPermissions' => FALSE, // Permissions that should be added to the application when it is installed.
   'SetupController' => 'setup',
   'AllowEnable' => TRUE, // You can remove this when you create your own application (leaving it will make it so the application can't be enabled by Garden)
   'Author' => "eleith",
   'AuthorEmail' => 'eleith@diffbot.com',
   'AuthorUrl' => 'http://www.diffbot.com',
   'License' => 'MIT'
);
