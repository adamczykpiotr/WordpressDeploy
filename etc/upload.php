<?php

class Upload {

    protected $connection;

    /**
     * Upload constructor.
     * @param bool $production
     */
    public function __construct($production = false) {
        $this->connection = ftp_connect($production ? FTP_PROD_HOST : FTP_DEV_HOST);
        $status = ftp_login(
            $this->connection,
            $production ? FTP_PROD_USER : FTP_DEV_USER,
            $production ? FTP_PROD_PASS : FTP_DEV_PASS);
        if($status === false) die('Could not connect to FTP server!');
        if(ftp_pasv($this->connection, true) === false) die('Could not enable FTP PASV mode!');
    }

    /**
     * Sets current FTP directory
     *
     * @param string $path
     */
    public function setDirectory($path) {
        if(ftp_chdir($this->connection, $path) === false) die('Could not change FTP directory!');
    }

    /**
     * Uploads file
     *
     * @param string $path local file path
     * @param string $remote remote file path
     * @return bool
     */
    public function file($path, $remote) {
        return ftp_put($this->connection, $remote, $path);
    }

    /**
     * Upload destructor
     */
    public function __destruct() {
        ftp_close($this->connection);
    }
};