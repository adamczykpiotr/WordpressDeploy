<?php
require_once 'preOrPostDeploy.php';

class SagePreDeploy extends PreOrPostDeploy {

    /**
     * Builds theme & generates translation template for production
     */
    public function production() {
        $pb = new ProgressBar(2);

        //build theme for production
        $result = $this->exec('yarn build:production', 'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to build theme! (Return code: expected 0, got $result)");
        $pb->tick();

        //generate translation template
        $result = $this->exec(
            $this->os === static::OS_WIN
                ? 'yarn pot:win'
                : 'yarn pot',
            'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to generate translation file! (Return code: expected 0, got $result)");
        $pb->finish();
    }

    /**
     * Builds theme for dev environment
     */
    public function dev() {
        $pb = new ProgressBar(2);

        //build theme for production
        $result = $this->exec('yarn build', 'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to build theme! (Return code: expected 0, got $result)");
        $pb->tick();

        //generate translation template
        $result = $this->exec(
            $this->os === static::OS_WIN
                ? 'yarn pot:win'
                : 'yarn pot',
            'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to generate translation file! (Return code: expected 0, got $result)");
        $pb->finish();
    }
};