<?php
require_once 'env.php';
require_once 'etc/upload.php';
require_once 'etc/constants.php';
require_once 'etc/progressBar.php';

class RemoteEnvironment {

    const DEV = 0;
    const PRODUCTION = 1;

    protected $environment;
    protected $start = 0;

    /**
     * RemoteEnvironment constructor.
     * @param int $argc
     * @param string[] $argv
     */
    public function __construct($argc, $argv) {
        $this->start = microtime(true);

        echo "[Environment]\n";
        if($argc < 2) {
            echo "No environment specified, DEV assumed.\n";
            return $this->environment = static::DEV;
        }

        if( in_array(strtolower($argv[1]), ['prod', 'production'])) $this->environment = static::PRODUCTION;
        if( in_array(strtolower($argv[1]), ['dev', 'beta'])) $this->environment = static::DEV;

        if($this->environment === static::PRODUCTION) {
            echo "Deploying on PRODUCTION environment. Continue? [y/n]: ";
            $decision = readline();
            if( strtolower($decision) !== 'y' ) die('Exiting...');
        }

        return $this->environment;
    }

    /**
     * Uploads files
     */
    public function upload() {
        echo "[Upload]\n";
        $pb = new ProgressBar(4);

        $upload = new Upload($this->environment === static::PRODUCTION);
        $pb->tick();

        $upload->setDirectory( $this->environment === self::PRODUCTION ? FTP_PROD_PATH : FTP_DEV_PATH );
        $pb->tick();

        //upload archive
        $upload->file('temp/' . ARCHIVE_FILENAME, ARCHIVE_FILENAME);
        $pb->tick();

        //upload script
        $upload->file('temp/' . SCRIPT_FILENAME, SCRIPT_FILENAME);
        $pb->finish();
    }

    /**
     * Runs deployment script on remote server
     */
    public function deploy() {
        echo "[Deploy script]\n";
        $pb = new ProgressBar(4);

        $url = $this->environment === static::PRODUCTION ? REMOTE_PROD_URL : REMOTE_DEV_URL;
        $url .= '/' . SCRIPT_FILENAME;

        $json = file_get_contents($url);
        $pb->tick();

        $response = json_decode($json);
        if( !isset($response->success) || !$response->success ) {
            $message = $response->message ?? 'Server did not respond with error message';
            die("Deployment failed: $message\n");
        }

        $pb->finish();
        $now = new DateTime();
        $totalTime = number_format(microtime(true) - $this->start, 1);
        echo "\n";
        echo "Deployment successfully finished at {$now->format('Y-m-d h:i:s')} in $totalTime seconds\n";
    }
}