<?php

namespace WP\Controller;

class Poll
{
    private $wpdb;
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = "{$wpdb->prefix}poll_votes";
    }

    /**
     * Extract block data by poll id and post id.
     *
     * @param string $poll_id Poll ID
     * @param int $post_id Post ID
     * @return array Block array
     */
    public function get_block_data(string $poll_id, int $post_id): array
    {
        $parse_blocks = parse_blocks(get_post($post_id)->post_content);
        $block_data = [];

        if ($parse_blocks) {
            foreach ($parse_blocks as $block) {
                if ($block["blockName"] === "create-block/digitalbox-poll") {
                    if (
                        !empty($block["attrs"]["poll_id"]) &&
                        $block["attrs"]["poll_id"] === $poll_id
                    ) {
                        $block_data = $block["attrs"];
                    }
                }
            }
        }

        return $block_data;
    }

    /**
     * Get poll answers by poll id and post id.
     *
     * @param string $poll_id Poll ID
     * @param int $post_id Post ID
     * @return array Block array answers
     */
    public function get_answers(string $poll_id, int $post_id): array
    {
        return $this->get_block_data($poll_id, $post_id)["answers"] ?? [];
    }

    /**
     * Has poll expired
     *
     * @param string $poll_id Poll ID
     * @param int $post_id Post ID
     * @return boolean true/false
     */
    public function is_expired(string $poll_id, int $post_id): bool
    {
        $expiry = $this->get_block_data($poll_id, $post_id)["expiry"] ?? "";
        $now = time();

        if ($expiry && $now > strtotime($expiry)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user has voted on poll id
     *
     * @param string $poll_id Poll ID
     * @param string $user_ip User IP
     * @return boolean true/false
     */
    public function has_user_voted(string $poll_id, string $user_ip)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                {$this->table_name}
            WHERE
                poll_id = %s
                AND user_ip = %s
        ";

        $vote_count = (int) $this->wpdb->get_var(
            $this->wpdb->prepare($sql, $poll_id, $user_ip)
        );

        return $vote_count === 1;
    }

    /**
     * Insert a vote
     *
     * @param int $post_id ID of post
     * @param string $poll_id ID of poll
     * @param string $answer Selected answer
     * @param string $user_ip The users IP
     * @return int|false The number of rows inserted, or false on error
     */
    public function insert_vote(
        int $post_id,
        string $poll_id,
        string $answer,
        string $user_ip
    ) {
        return $this->wpdb->insert(
            $this->table_name,
            [
                "post_id" => $post_id,
                "poll_id" => $poll_id,
                "answer" => $answer,
                "user_ip" => $user_ip,
            ],
            ["%d", "%s", "%s", "%s"]
        );
    }

    /**
     * Count total votes by poll id
     *
     * @param string $poll_id Poll ID
     * @return integer Amount of votes
     */
    public function count_total_votes(string $poll_id): int
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                {$this->table_name}
            WHERE
                poll_id = %s
        ";

        $vote_count = (int) $this->wpdb->get_var(
            $this->wpdb->prepare($sql, $poll_id)
        );

        return $vote_count;
    }
    /**
     * Count total votes by answer
     *
     * @param string $poll_id Poll ID
     * @param string $answer Answer
     * @return integer Amount of votes
     */
    public function count_total_votes_by_answer(string $poll_id, string $answer)
    {
        $sql = "
            SELECT
                COUNT(*)
            FROM
                {$this->table_name}
            WHERE
                poll_id = %s
                AND answer = %s
        ";

        $vote_count = (int) $this->wpdb->get_var(
            $this->wpdb->prepare($sql, $poll_id, $answer)
        );

        return $vote_count;
    }
}
