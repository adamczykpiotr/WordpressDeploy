<?php

const HASH = '%%MD5%%';
const SCRIPT = '%%SCRIPT%%';
const ARCHIVE = '%%ARCHIVE%%';
const INDEX = '%%INDEX%%';
CONST DATETIME = '%%DATETIME%%';
CONST ARCHIVED_PATHS = '%%ARCHIVED_PATHS%%';
CONST REQUESTED_PATHS = '%%REQUESTED_PATHS%%';

class WordpressRemoteDeploy {

    protected $paths = [];

    /**
     * WordpressRemoteDeploy constructor.
     */
    public function __construct() {
        if( md5_file(ARCHIVE) !== HASH ) {
            $this->response('INVALID FILE HASH', false);
        }

        $this->replaceIndex();

        //prepare data for class methods
        $this->paths = [];//unserialize(PATHS_JSON);

        //extract archive
        $this->unzip();
        $this->setPermissions();

        //TODO: Delete all files in requested directories that were not modified by zip

        $this->restoreIndex();

        //save last deployment creation date
        @file_put_contents('.lastdeploy', DATETIME);

        //delete archive file & deployment script
        @unlink(ARCHIVE);
        @unlink(SCRIPT);
        @unlink(INDEX);

        $this->response('Success');
    }

    /**
     * Unzips archive to current directory
     */
    protected function unzip() {
        $zip = new ZipArchive();
        $zip->open(ARCHIVE);
        $zip->extractTo('.');
    }

    /**
     * Sets file permissions
     *
     * @param int $level
     */
    protected function setPermissions($level = 0755) {
        foreach($this->paths as $path) {
            chmod($path, $level);
        }
    }

    /**
     * Replaces index.php with prepared placeholder
     */
    protected function replaceIndex() {
        copy('index.php', 'index.bak.php');
        rename(INDEX, 'index.php');
    }

    /**
     * Restores original index.php
     */
    protected function restoreIndex() {
        @unlink('index.php');
        rename('index.bak.php', 'index.php');
    }

    /**
     * Generates JSON response
     *
     * @param string $message
     * @param bool $success
     */
    protected function response($message, $success = true) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool) $success,
            'message' => (string) $message
        ]);
        exit;
    }
};
new WordpressRemoteDeploy();