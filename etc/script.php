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
        $pb = new ProgressBar(8);

        $fileContent = file_get_contents('template/' . SCRIPT_FILENAME);
        $pb->tick();

        //update archive name
        $fileContent = str_replace('%%ARCHIVE%%', ARCHIVE_FILENAME, $fileContent);
        $pb->tick();

        //update script name
        $fileContent = str_replace('%%SCRIPT%%', SCRIPT_FILENAME, $fileContent);
        $pb->tick();

        //update generated md5 from archive
        $hash = md5_file('temp/' . ARCHIVE_FILENAME);
        $fileContent = str_replace('%%MD5%%', $hash, $fileContent);
        $pb->tick();

        //update last deploy date
        $now = new DateTime();
        $fileContent = str_replace('%%DATETIME%%', $now->format(DATE_ATOM), $fileContent);
        $pb->tick();

        /*//update found file list
        $fileContent = str_replace('%%PATHS_JSON%%', serialize($this->paths), $fileContent);
        $pb->tick();

        //update requested file list
        $fileContent = str_replace('%%REQUESTED_PATHS%%', serialize(PATHS), $fileContent);
        $pb->tick();*/

        file_put_contents('temp/' . SCRIPT_FILENAME, $fileContent);
        $pb->finish();
    }
};