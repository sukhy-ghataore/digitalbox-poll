<?php

namespace WP\Rest;

use WP_REST_Request;
use WP_REST_Response;
use WP\Controller\Poll;

class PollApi
{
    /**
     * Method handler for post form submission
     *
     * @param WP_REST_Request $request The WP Request
     */
    public function handle_poll_submission(WP_REST_Request $request)
    {
        $nonce = $request->get_header("X-WP-Nonce");

        if (!wp_verify_nonce($nonce, "wp_rest")) {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "unauthorized",
                    "message" => "Invalid nonce",
                ],
                401
            );
        }

        $data = json_decode($request->get_body());
        $pollController = new Poll();

        // sanitize and clean data
        $poll_id = strtoupper(sanitize_text_field($data->poll_id));
        $post_id = (int) $data->post_id;
        $answer = sanitize_text_field($data->answer);
        $user_ip = $_SERVER["REMOTE_ADDR"];
        //$vote_datetime = current_time("mysql");

        $debug_data = [$post_id, $poll_id, $answer, $user_ip];

        // check to see if poll is legit
        if (
            !$poll_id ||
            !$post_id ||
            !$pollController->get_block_data($poll_id, $post_id)
        ) {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "bad_request",
                    "message" => "Missing/invalid poll id or post id",
                ],
                400
            );
        }

        // has poll expired?
        if ($pollController->is_expired($poll_id, $post_id)) {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "forbidden",
                    "message" => "Poll has expired",
                ],
                403
            );
        }

        // check to see if answer is in polls array of answers
        if (
            !$answer ||
            !in_array(
                $answer,
                array_column(
                    $pollController->get_answers($poll_id, $post_id),
                    "answer"
                )
            )
        ) {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "bad_request",
                    "message" => "Answer is missing or invalid",
                ],
                400
            );
        }

        // check to see if user has voted
        if ($pollController->has_user_voted($poll_id, $user_ip)) {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "already_voted",
                    "message" => "You have already voted for this poll",
                ],
                403
            );
        }

        // if all good, proceed to insert
        if (
            $pollController->insert_vote($post_id, $poll_id, $answer, $user_ip)
        ) {
            $poll_answers = $pollController->get_answers($poll_id, $post_id);
            $poll_answers_count = [];

            if ($poll_answers) {
                foreach ($poll_answers as $poll_answer) {
                    if ($poll_a = $poll_answer["answer"]) {
                        $poll_answers_count[
                            $poll_a
                        ] = $pollController->count_total_votes_by_answer(
                            $poll_id,
                            $poll_a
                        );
                    }
                }
            }

            return new WP_REST_Response(
                [
                    "error" => false,
                    "code" => "success",
                    "message" => "Successfully saved vote",
                    "poll_results" => [
                        "total_votes" => $pollController->count_total_votes(
                            $poll_id
                        ),
                        "answer_total_votes" => $poll_answers_count,
                    ],
                ],
                200
            );
        } else {
            return new WP_REST_Response(
                [
                    "error" => true,
                    "code" => "failed",
                    "message" => "Failed to save vote",
                    //"debug" => $debug_data
                ],
                500
            );
        }
    }
}
