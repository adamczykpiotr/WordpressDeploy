<?php
require_once 'env.php';
require_once 'etc/progressBar.php';
require_once 'etc/remoteEnvironment.php';

abstract class PreOrPostDeploy {

    const OS_WIN = 1;
    const OS_LINUX = 2;
    const PRE_DEPLOY = true;

    protected $os = self::OS_WIN;

    public function __construct($environment) {
        echo static::PRE_DEPLOY
            ? "[Pre-deploy actions]\n"
            : "[Post-deploy actions]\n";
        $this->detectOS();

        if($environment === RemoteEnvironment::PRODUCTION) {
            $this->production();
        } else if($environment === RemoteEnvironment::DEV) {
            $this->dev();
        } else {
            die('Could not trigger pre-deployment actions for unknown environment!');
        }
    }

    /**
     * Builds theme & generates translation template for production
     */
    abstract public function production();

    /**
     * Builds theme for dev environment
     */
    abstract public function dev();

    /**
     * @param string $command
     * @param string $path
     * @param bool $inWpPath
     * @param bool $suppressOutput
     * @return object
     */
    protected function exec($command, $path = '', $inWpPath = true, $suppressOutput = true) {
        $wpPath = $inWpPath ? WP_PATH : '';
        $cd = ($path) ? "cd {$wpPath}{$path} && " : '';

        $suppressOutput = $suppressOutput
            ? ($this->os === static::OS_WIN ? ' 2> nul' : ' > /dev/null')
            : '';

        $exec = "{$cd}{$command}{$suppressOutput}";

        $code = null;
        $output = [];
        exec($exec, $output, $code);

        return (object) [
            'code' => $code,
            'output' => $output
        ];
    }

    /**
     * Detects current operating system
     */
    protected function detectOS() {
        $this->os = (strtoupper( substr(PHP_OS, 0, 3) ) === 'WIN')
            ? static::OS_WIN
            : static::OS_LINUX;
    }
}