<?php
/*
Plugin Name: WP-Resizely
Plugin URI: http://usabilitydynamics.com/products/wp-resizely/
Description: Dynamic image resizing.
Author: Usability Dynamics, Inc.
Version: 0.1.0
Author URI: http://usabilitydynamics.com

Copyright 2013  Usability Dynamics, Inc.    (email : info@usabilitydynamics.com)

Created by Usability Dynamics, Inc (website: usabilitydynamics.com       email : info@usabilitydynamics.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Plugin Version
define( 'WP_Resizely_Version', '0.1.0' );

// Path for Includes
define( 'WP_Resizely_Path', untrailingslashit( plugin_dir_path( __FILE__ ) ));

// Path for front-end links
define( 'WP_Resizely_URL', untrailingslashit(plugin_dir_url( __FILE__ )));

// Locale Name
define( 'WP_Resizely_Locale', 'wp-resizely' );

/** Loads general functions used by WP-crm */
include_once WP_Resizely_Path . '/core/class_functions.php';

/** Loads all the metaboxes for the crm page */
include_once WP_Resizely_Path . '/core/class_core.php';

// Register activation hook -> has to be in the main plugin file
register_activation_hook( __FILE__, array( 'WP_Resizely_Functions', 'activation' ));

// Register activation hook -> has to be in the main plugin file
register_deactivation_hook( __FILE__, array( 'WP_Resizely_Functions', 'deactivation' ));

// Initiate the plugin
add_action( 'plugins_loaded', create_function('', 'new WP_Resizely_Core;'));

