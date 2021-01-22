<?php declare(strict_types=1);

if (defined('OCREAPER_PHPCS_AUTOLOAD_SET') === false) {

    // Check if this is a stand-alone installation.
    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    define('OCREAPER_PHPCS_AUTOLOAD_SET', true);
}
