<?php

/**
 * Initializes the appropriate configuration from either a PHP file
 * or a module configuration value
 * @return Instance of HTMLPurifier_Config
 */
function phorum_htmlpurifier_get_config($default = false)
{
    global $PHORUM;
    $config_exists = phorum_htmlpurifier_config_file_exists();
    if ($default || $config_exists || !isset($PHORUM['mod_htmlpurifier']['customer-config'])) {
        $config = HTMLPurifier_Config::createDefault();
        include(dirname(__FILE__) . '/config.default.php');
        if ($config_exists) {
            include(dirname(__FILE__) . '/customer-config.php');
        }
        unset($PHORUM['mod_htmlpurifier']['customer-config']); // unnecessary
    } else {
        $config = HTMLPurifier_Config::create($PHORUM['mod_htmlpurifier']['customer-config']);
    }
    return $config;
}

function phorum_htmlpurifier_config_file_exists()
{
    return file_exists(dirname(__FILE__) . '/customer-config.php');
}

// vim: et sw=4 sts=4
