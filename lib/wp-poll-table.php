<?php

namespace WP\Poll;

class DB
{
    /**
     * Create Poll Table
     *
     * @return void creates table if not exists
     */
    public function create_table(): void
    {
        global $wpdb;

        $table_name = "{$wpdb->prefix}poll_votes";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "
            CREATE TABLE IF NOT EXISTS {$table_name} (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT(20) UNSIGNED NOT NULL,
                poll_id VARCHAR(45) NOT NULL,
                answer VARCHAR(45) NOT NULL,
                user_ip VARCHAR(45) NOT NULL,
                vote_datetime DATETIME DEFAULT CURRENT_TIMESTAMP
            ) {$charset_collate};
        ";

        require_once ABSPATH . "wp-admin/includes/upgrade.php";

        dbDelta($sql);
    }
}
