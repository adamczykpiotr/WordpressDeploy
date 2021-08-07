<?php
require_once 'env.php';
require_once 'etc/archive.php';
require_once 'etc/script.php';
require_once 'etc/remoteEnvironment.php';

//determine target remote environment
$remote = new RemoteEnvironment($argc, $argv);

//create Archive
$archive = new Archive();
$archive->createArchive();

//create deployment script
$script = new Script( $archive->getCleanPaths() );
$script->generate();

//upload files to specific remote
$remote->upload();
$remote->deploy();