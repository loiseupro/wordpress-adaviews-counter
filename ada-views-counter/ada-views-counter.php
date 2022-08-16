<?php

/**
 * @package Ada views counter
 */
/*
* Plugin Name: Ada views counter
* Plugin URI: https://adaweb.es/
* Description: Posts views counter
* Version: 1.0
* Author: Lois
* Author URI: https://adaweb.es/
* License: GPLv2 or later
* Text Domain: ada_post_views
* Domain Path: /languages
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action') or !defined('ABSPATH')) {
    wp_die("Sorry you can't call me directly");
}

function ada_translate() {
    load_plugin_textdomain('ada_post_views', false, dirname( plugin_basename( __FILE__ )).'/languages');
}
add_action( 'plugins_loaded', 'ada_translate' );

/************************ FRONTEND ************************/ 

/**
 * Get post view count
 * Only for post_type = post
 * 
 * @return int Counter 
 */
function ada_get_post_views() {
    global $post;
    $count_key = 'post_views_count_test';
    $count = 0;

    if (is_single() and $post->post_type == 'post') {
        $count = get_post_meta($post->ID, $count_key, true);
        if (!$count) {
            $count = 0;
        }
    }
    return $count;
}
add_shortcode("ada_get_post_views", "ada_get_post_views");

/**
 * Update post view count
 * Only for post_type = post
 * 
 */
function ada_update_post_views() {
    global $post;
    $count_key = 'post_views_count_test';

    if (is_single() and $post->post_type == 'post') {
        $count = get_post_meta($post->ID, $count_key, true);
        if ($count === false) {
            add_post_meta($post->ID, $count_key, 0);
        } else {
            $count = (int)$count + 1;
            update_post_meta($post->ID, $count_key, $count);
        }
    }
}
add_action('the_content', 'ada_update_post_views');


/************************ BACKEND ************************/ 

/**
 * Add menú
 * 
 */
function ada_views__menu() {
	$the_info_path = plugin_dir_path(__FILE__).'admin/templates/info.php';	
	add_menu_page('Ada views counter', 'Ada views counter', 'manage_options', $the_info_path,'','dashicons-welcome-view-site');
}
add_action('admin_menu', 'ada_views__menu');

/**
 * Add count column head in posts list
 * 
 */
function ada_post_views_column_head($columns) {
    $columns['views'] = __('Views', 'ada_post_views');
    return $columns;
}
add_filter('manage_posts_columns', 'ada_post_views_column_head');

/**
 * Add count column value in posts list
 * 
 */
function ada_post_views_column_value($column, $post_id) {
    $count_key = 'post_views_count_test';
    if ('views' === $column) {
        $count = get_post_meta($post_id, $count_key, true);
        if ($count === false or $count == '') {
            echo 0;
        } else {
            echo $count;
        }
    }
}
add_action('manage_posts_custom_column', 'ada_post_views_column_value', 10, 2);

/**
 * Get post view count
 * Only for post_type = post
 * 
 * @return int Counter 
 */
function ada_echo_post_views(){
    global $post;
    $count_key = 'post_views_count_test';
    $count = 0;

    if ( $post->post_type == 'post') {
        $count = get_post_meta($post->ID, $count_key, true);
        if (!$count) {
            $count = 0;
        }
    }
    echo $count;
}

/**
 * Add count to admin post page
 * 
 */
function ada_post_views_admin_post() {
    if (is_admin() && get_post_type() == 'post') {
        add_meta_box('ada_post_views_value', __( 'Views', 'ada_post_views' ), 'ada_echo_post_views', 'post' );
    }
}
add_action('the_post', 'ada_post_views_admin_post');
