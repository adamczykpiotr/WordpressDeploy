<?php
require_once 'preOrPostDeploy.php';

class SagePostDeploy extends PreOrPostDeploy {

    const PRE_DEPLOY = false;

    /**
     * Builds theme & generates translation template for production
     */
    public function production() {
        $pb = new ProgressBar(1);

        //build theme for production
        $result = $this->exec('yarn build:brave-dev', 'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to re-build theme! (Return code: expected 0, got $result)");
        $pb->finish();
    }

    /**
     * Builds theme for dev environment
     */
    public function dev() {
        $pb = new ProgressBar(1);

        //build theme for production
        $result = $this->exec('yarn build:brave-dev', 'wp-content/themes/custom-theme' );
        if($result->code !== 0) die("Failed to re-build theme! (Return code: expected 0, got $result)");
        $pb->finish();
    }
};