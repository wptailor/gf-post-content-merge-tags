<?php

defined('ABSPATH') or exit;

# Basic
gw_post_content_merge_tags();

# With Parameters
gw_post_content_merge_tags( array(
    'auto_append_eid' => false,
    'encrypt_eid'     => true
) );
