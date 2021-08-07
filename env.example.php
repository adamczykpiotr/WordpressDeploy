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
const FTP_HOST = 'ftp.host.com';
const FTP_USER = 'username';
const FTP_PASS = 'password';

/*
 * Remote Wordpress url
 *
 * URL has to include / character at the end
 * */
const REMOTE_URL = 'https://example.com/';

/*
 * Array of paths to be zipped and deployed on the remote server
 * */
const PATHS = [
    'wp-content/plugins/custom-plugin/',        //plugin

    'wp-content/themes/custom-theme/dist',      //sage theme
    'wp-content/themes/custom-theme/resources', //sage theme
    'wp-content/themes/custom-theme/app',       //sage theme
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
 * Array of cache paths - directories/files to be deleted on remote server after deploy
 * */
const CACHE = [
    'wp-content/uploads/cache',
];