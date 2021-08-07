<?php
require_once 'env.php';
require_once 'etc/constants.php';
require_once 'etc/progressBar.php';

class Archive {

    protected $paths = [];

    /**
     * Generates paths array for archive output
     */
    public function process() {
        foreach(PATHS as $path) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(WP_PATH . $path, FilesystemIterator::KEY_AS_FILENAME),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $validPaths = [];
            foreach ($files as $name => $fileInfo) {
                if( !$fileInfo->isFile() ) continue;

                $path = $fileInfo->getPathName();
                if( $this->isForbidden($path) ) continue;

                $validPaths[] = $path;
            };

            $this->paths = array_merge(
                $this->paths,
                array_unique($validPaths)
            );
        }
    }

    /**
     * Tests whether given path is forbidden
     *
     * @param string $path
     * @return bool
     */
    protected function isForbidden($path): bool {
        foreach(FORBIDDEN as $forbidden) {
            if( strpos($path, $forbidden) !== false) return true;
        }

        return false;
    }

    /**
     * Generates archive file in temp directory
     */
    public function createArchive() {
        echo "[Creating archive]\n";

        if (empty($this->paths)) $this->process();
        $pb = new ProgressBar(count($this->paths) + 2);

        //remove already existing file
        @unlink('temp/' . ARCHIVE_FILENAME);
        $pb->tick();

        $zip = new ZipArchive();
        if ($zip->open('temp/' . ARCHIVE_FILENAME, ZipArchive::CREATE || ZipArchive::OVERWRITE) !== true) die('Could not create archive file!');

        $wpPathOffset = strlen(WP_PATH);
        foreach ($this->paths as $i => $path) {
            $path = str_replace('\\', '/', $path);
            $zip->addFile($path, substr($path, $wpPathOffset));
            $pb->tick();
        }

        $zip->close();
        $pb->finish();
    }

    /**
     * Return file paths with removed Wordpress path prefix
     *
     * @return array
     */
    public function getCleanPaths(): array {
        $paths = [];
        $wpPathOffset = strlen(WP_PATH);
        foreach ($this->paths as $i => $path) {
            $path = substr($path, $wpPathOffset);
            $paths[] = $path;
        }

        return $paths;
    }
}