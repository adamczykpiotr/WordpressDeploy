<?php

const HASH = '%%MD5%%';
const SCRIPT = '%%SCRIPT%%';
const ARCHIVE = '%%ARCHIVE%%';
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

        //prepare data for class methods
        $this->paths = unserialize(PATHS_JSON);

        //extract archive
        $this->unzip();
        $this->setPermissions();

        //TODO: Delete all files in requested directories that were not modified by zip

        //save last deployment creation date
        @file_put_contents('.lastdeploy', DATETIME);

        //delete archive file & deployment script
        @unlink(ARCHIVE);
        @unlink(SCRIPT);

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