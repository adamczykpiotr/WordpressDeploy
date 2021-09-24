<?php

/*
 * Base Wordpress path on your disk
 *
 * Path has to include / character at the end
 *
 * */
const WP_PATH = 'C:/xampp/htdocs/wordpress/';

/*
 * FTP Config
 * */
const FTP_DEV_HOST = 'ftp.host.com';
const FTP_DEV_USER = 'username';
const FTP_DEV_PASS = 'password';
const FTP_DEV_PATH = 'public_html/www/dev';

const FTP_PROD_HOST = 'ftp.host.com';
const FTP_PROD_USER = 'username';
const FTP_PROD_PASS = 'password';
const FTP_PROD_PATH = 'public_html/www';
/*
 * Remote Wordpress url
 *
 * URL has to include / character at the end
 * */
const REMOTE_DEV_URL = 'https://dev.example.com/';
const REMOTE_PROD_URL = 'https://example.com/';

/*
 * Array of paths to be zipped and deployed on the remote server
 * */
const PATHS = [
    'wp-content/plugins/custom-plugin/',                    //plugin

    'wp-content/themes/custom-theme/dist',                  //dist
    'wp-content/themes/custom-theme/resources',             //resources
    'wp-content/themes/custom-theme/app',                   //app
    'wp-content/themes/custom-theme/lang/custom-theme.pot', //language template
];

/*
 * Array of forbidden directories/files/paths
 * Path is rejected when contains any of strings below
 * */
const FORBIDDEN = [
    '.git',
    '.idea',
    'node-modules',
    'custom-plugin\languages'
];


/*
 * Array of paths to be deleted on remote server before unzipping
 * */
const PRE_CACHE = [
    'wp-content/themes/sample-theme/dist',
];

/*
 * Array of cache paths to be deleted on remote server after deploy
 * */
const POST_CACHE = [
    'wp-content/uploads/cache',
];