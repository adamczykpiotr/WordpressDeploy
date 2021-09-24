<?php
require_once 'env.php';
require_once 'etc/progressBar.php';

class Script {

    protected $paths;

    /**
     * Script constructor.
     * @param array $paths
     */
    public function __construct($paths = []) {
        $this->paths = $paths;
    }

    /**
     * Generates deployment script
     */
    public function generate() {
        echo "[Creating deployment script]\n";
        $pb = new ProgressBar(10);

        $fileContent = file_get_contents('template/' . SCRIPT_FILENAME);
        $pb->tick();

        //update archive name
        $fileContent = str_replace('%%ARCHIVE%%', ARCHIVE_FILENAME, $fileContent);
        $pb->tick();

        //update script name
        $fileContent = str_replace('%%SCRIPT%%', SCRIPT_FILENAME, $fileContent);
        $pb->tick();

        //update placeholder index name
        $fileContent = str_replace('%%INDEX%%', INDEX_FILENAME, $fileContent);
        $pb->tick();

        //update generated md5 from archive
        $hash = md5_file('temp/' . ARCHIVE_FILENAME);
        $fileContent = str_replace('%%MD5%%', $hash, $fileContent);
        $pb->tick();

        //update last deploy date
        $now = new DateTime();
        $fileContent = str_replace('%%DATETIME%%', $now->format(DATE_ATOM), $fileContent);
        $pb->tick();

        //update found file list
        $fileContent = str_replace('%%ARCHIVED_PATHS%%', serialize($this->paths), $fileContent);
        $pb->tick();

        //update pre-deploy cache list
        $fileContent = str_replace('%%PRE_CACHE%%', serialize(PRE_CACHE), $fileContent);
        $pb->tick();

        //update post-deploy cache list
        $fileContent = str_replace('%%POST_CACHE%%', serialize(POST_CACHE), $fileContent);
        $pb->tick();

        /*//update requested file list
        $fileContent = str_replace('%%REQUESTED_PATHS%%', serialize(PATHS), $fileContent);
        $pb->tick();*/

        file_put_contents('temp/' . SCRIPT_FILENAME, $fileContent);
        $pb->finish();
    }

    /**
     * Adds placeholder index.php file for minimizing deployment effects
     */
    public function addPlaceholderIndex() {
        echo "[Creating placeholder index.php]\n";
        $pb = new ProgressBar(1);

        copy('template/index.php', 'temp/' . INDEX_FILENAME);

        $pb->finish();
    }
};