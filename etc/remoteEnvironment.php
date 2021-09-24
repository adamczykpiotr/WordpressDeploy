<?php
require_once 'env.php';
require_once 'etc/upload.php';
require_once 'etc/constants.php';
require_once 'etc/progressBar.php';

class RemoteEnvironment {

    const DEV = 0;
    const PRODUCTION = 1;
    const UNKNOWN = 2;

    protected $environment = self::UNKNOWN;
    protected $start = 0;
    protected $preDeployClass = null;
    protected $postDeployClass = null;

    /**
     * RemoteEnvironment constructor.
     *
     * @param int $argc
     * @param string[] $argv
     * @param class-string $preDeployClass
     * @param class-string $postDeployClass
     */
    public function __construct($argc, $argv, $preDeployClass, $postDeployClass) {
        $this->start = microtime(true);
        $this->preDeployClass = $preDeployClass;
        $this->postDeployClass = $postDeployClass;

        echo "[Environment]\n";
        if($argc > 1) {
            if (in_array(strtolower($argv[1]), ['prod', 'production'])) $this->environment = static::PRODUCTION;
            if (in_array(strtolower($argv[1]), ['dev', 'beta'])) $this->environment = static::DEV;
        }

        switch($this->environment ?? -1) {
            case static::PRODUCTION:
            {
                echo "Deploying on PRODUCTION environment. Continue? [y/n]: ";
                $decision = readline();
                if (strtolower($decision) !== 'y') die('Exiting...');
                break;
            }

            case static::DEV:
            {
                echo "Deploying on DEV environment.\n";
                break;
            }

            case static::UNKNOWN:
            default:
            {
                echo "No environment specified, DEV assumed.\n";
                $this->environment = static::DEV;
                break;
            }
        }

        return $this->environment;
    }

    /**
     * Triggers pre-deploy action(s) for specific environment
     */
    public function preDeploy() {
        if($this->preDeployClass === null) return;
        new $this->preDeployClass($this->environment);
    }

    /**
     * Triggers post-deploy action(s) for specific environment
     */
    public function postDeploy() {
        if($this->postDeployClass === null) return;
        new $this->postDeployClass($this->environment);
    }

    /**
     * Uploads files
     */
    public function upload() {
        echo "[Uploading files]\n";
        $hasPlaceholderIndex = file_exists('temp/' . INDEX_FILENAME);

        $pb = new ProgressBar($hasPlaceholderIndex ? 5 : 4);

        $upload = new Upload($this->environment === static::PRODUCTION);
        $pb->tick();

        $upload->setDirectory( $this->environment === static::PRODUCTION ? FTP_PROD_PATH : FTP_DEV_PATH );
        $pb->tick();

        //upload archive
        $upload->file('temp/' . ARCHIVE_FILENAME, ARCHIVE_FILENAME);
        $pb->tick();

        //upload script
        $upload->file('temp/' . SCRIPT_FILENAME, SCRIPT_FILENAME);
        $pb->tick();

        //upload placeholder index php if exists
        if( $hasPlaceholderIndex ) {
            $upload->file('temp/' . INDEX_FILENAME, INDEX_FILENAME);
        }

        $pb->finish();
    }

    /**
     * Runs deployment script on remote server
     */
    public function deploy() {
        echo "[Executing deployment script]\n";
        $pb = new ProgressBar(2);

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
        echo "Deployment successfully finished at {$now->format('Y-m-d H:i:s')} in $totalTime seconds\n";
        echo "\n";
    }
}