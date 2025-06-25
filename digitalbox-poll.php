<?php
/**
 * Plugin Name:       Digitalbox Poll
 * Description:       Embeds interactive poll for readers to vote
 * Version:           0.1.0
 * Author:            Sukhy
 * Text Domain:       digitalbox-poll
 *
 * @package CreateBlock
 */

// exit if accessed directly
if (!defined("ABSPATH")) {
    exit();
}

// require lib files
require_once __DIR__ . "/lib/wp-poll-table.php";
require_once __DIR__ . "/lib/wp-poll-controller.php";
require_once __DIR__ . "/lib/wp-rest-api.php";
require_once __DIR__ . "/lib/wp-poll-api.php";

// on activate, create wp poll table
register_activation_hook(__FILE__, [new WP\Poll\DB(), "create_table"]);

// register routes
$Api = new WP\Rest\Api();

// register block
function create_block_digitalbox_poll_block_init()
{
    register_block_type(__DIR__ . "/build/digitalbox-poll");
}
add_action("init", "create_block_digitalbox_poll_block_init");
