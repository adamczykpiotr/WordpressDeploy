<?php

const HASH = '%%MD5%%';
const SCRIPT = '%%SCRIPT%%';
const ARCHIVE = '%%ARCHIVE%%';
const INDEX = '%%INDEX%%';
const DATETIME = '%%DATETIME%%';
const ARCHIVED_PATHS = '%%ARCHIVED_PATHS%%';

const PRE_CACHE = '%%PRE_CACHE%%';
const POST_CACHE = '%%POST_CACHE%%';


class WordpressRemoteDeploy
{
    protected $archivedPaths = [];
    protected $indexContents = null;

    protected $preCache = [];
    protected $postCache = [];

    /**
     * WordpressRemoteDeploy constructor.
     */
    public function __construct()
    {
        if (md5_file(ARCHIVE) !== HASH) {
            $this->response('INVALID FILE HASH', false);
        }
        $this->archivedPaths = unserialize(ARCHIVED_PATHS);
        $this->preCache = unserialize(PRE_CACHE);
        $this->postCache = unserialize(POST_CACHE);

        $this->replaceIndex();

        $this->removePreCache();

        //extract archive
        $this->unzip();
        $this->setPermissions();

        //TODO: Delete all files in requested directories that were not modified by zip

        $this->removePostCache();

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
    protected function unzip()
    {
        $zip = new ZipArchive();
        $zip->open(ARCHIVE);
        $zip->extractTo('.');
    }

    /**
     * Sets file permissions
     *
     * @param int $level
     */
    protected function setPermissions($level = 0755)
    {
        foreach ($this->paths as $path) {
            chmod($path, $level);
        }
    }

    /**
     * Replaces index.php with prepared placeholder
     */
    protected function replaceIndex()
    {
        $this->indexContents = file_get_contents('index.php');
        rename(INDEX, 'index.php');
    }

    /**
     * Restores original index.php
     */
    protected function restoreIndex()
    {
        file_put_contents('index.php', $this->indexContents);
    }

    /**
     * Generates JSON response
     *
     * @param string $message
     * @param bool $success
     */
    protected function response($message, $success = true)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool) $success,
            'message' => (string) $message
        ]);
        exit;
    }

    /**
     * Removes pre-deploy cache
     */
    private function removePreCache()
    {
        if( !is_array($this->preCache) ) return;

        foreach($this->preCache as $path) {
            $this->recursiveDeletePath($path);
        }
    }

    /**
     * Removes post-deploy cache
     */
    private function removePostCache()
    {
        if( !is_array($this->postCache) ) return;

        foreach($this->postCache as $path) {
            $this->recursiveDeletePath($path);
        }
    }

    /**
     * Recursively deletes everything in path
     *
     * @param $path
     * @return bool
     */
    protected function recursiveDeletePath($path): bool {
        $files = array_diff( scandir($path), ['.','..'] );
        foreach ($files as $file) {
            is_dir("$path/$file")
                ? $this->recursiveDeletePath("$path/$file")
                : unlink("$path/$file");
        }
        return rmdir($path);
    }
}

new WordpressRemoteDeploy();