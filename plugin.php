<?php
/*
Plugin Name: Gravity Forms Post Content Merge Tags
Plugin URI: http://example.com/
Description: Adds support for using Gravity Form merge tags in your post content. This functionality requires that the entry ID is passed to the post via the "eid" parameter.
Version: 0.1
Author: David Smith <david@gravitywiz.com>
Author URI: http://gravitywiz.com/
*/

defined('ABSPATH') or exit;

function gforms_post_content_merge_tags_load() {

  if( ! class_exists( 'GFForms' ) )
      return;

  require_once __DIR__ . '/gw-gravity-forms-post-content-merge-tags.php';
}

add_action( 'plugins_loaded', 'gforms_post_content_merge_tags_load' );
