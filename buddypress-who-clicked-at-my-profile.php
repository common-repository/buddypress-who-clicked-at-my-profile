<?php

/**
 * Plugin Name: Buddypress - Who clicked at my Profile?
 * Plugin URI: http://ifs-net.de
 * Description: This Plugin provides at Widget that shows who visited your profile. This increases networking and communication at your community website!
 * Version: 3.6
 * Author: Florian Schiessl
 * Author URI: http://ifs-net.de
 * License: GPL2
 * Text Domain: buddypress-wcamp
 * Domain Path: /languages/
 */

/**
 * Load Plugin only if buddypress is active and available
 * https://codex.buddypress.org/plugindev/checking-buddypress-is-active/
 */
function bpwcamp_init() {
    require( dirname(__FILE__) . '/plugin.php' );
}

add_action('bp_include', 'bpwcamp_init');