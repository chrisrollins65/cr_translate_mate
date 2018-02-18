<?php

spl_autoload_register( function ($className) {
    $filename = str_replace('CrTranslateMate', __DIR__, $className) . '.php';
    if (file_exists($filename)) {
        include_once($filename);
    }
});

require_once 'helpers.php';