<?php
require_once 'etc/archive.php';
require_once 'etc/script.php';
require_once 'etc/sagePreDeploy.php';
require_once 'etc/sagePostDeploy.php';
require_once 'etc/remoteEnvironment.php';

//determine target remote environment
$remote = new RemoteEnvironment($argc, $argv, SagePreDeploy::class, SagePostDeploy::class );

//trigger pre deploy action(s)
$remote->preDeploy();

//create Archive
$archive = new Archive();
$archive->createArchive();

//create deployment script
$script = new Script( $archive->getCleanPaths() );
$script->generate();
$script->addPlaceholderIndex();

//upload files to specific remote
$remote->upload();
$remote->deploy();

//trigger post deploy action(s)
$remote->postDeploy();