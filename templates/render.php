<?php

use WP\Controller\Poll;

$post_id = get_the_ID();
$user_ip = $_SERVER["REMOTE_ADDR"];
$pollController = new Poll();

$poll_id = $attributes["poll_id"];
$question = $attributes["question"];
$answers = $attributes["answers"];
$expiry = $attributes["expiry"] ?? "";

$is_expired = $pollController->is_expired($poll_id, $post_id);
$has_user_voted = $pollController->has_user_voted($poll_id, $user_ip);
?>

<?php if ($poll_id && $question && !empty($answers)): ?>
    <div <?php echo get_block_wrapper_attributes(); ?>>
        <h3><?php echo $question; ?></h3>
        <?php if (!$is_expired): ?>
            <form class="poll-form" data-post-id="<?php echo $post_id; ?>" data-poll-id="<?php echo $poll_id; ?>">
                <div class="answers">
                    <?php foreach ($answers as $answer): ?>
                        <?php if ($value = $answer["answer"]): ?>
                            <div class="answer">
                                <figure class="img">
                                    <?php if (
                                        $image_url = $answer["image_url"]
                                    ): ?>
                                        <img src="<?php echo $image_url; ?>" width="80" height="80" alt="image">
                                    <?php endif; ?>
                                </figure>
                                <label>
                                    <?php if (!$has_user_voted): ?>
                                        <input type="radio" name="<?php echo $poll_id; ?>" value="<?php echo $value; ?>">
                                    <?php endif; ?>
                                    <?php echo $value; ?>
                                    <span class="total-answer-votes">
                                        <?php echo $has_user_voted
                                            ? "- {$pollController->count_total_votes_by_answer(
                                                $poll_id,
                                                $value
                                            )} vote(s)"
                                            : ""; ?>
                                    </span>
                                </label>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php if (!$has_user_voted): ?>
                    <div class="submit-poll-btn">
                        <button type="submit">Vote</button>
                    </div>
                <?php endif; ?>
                <div class="total-votes">
                    <h4>
                        <?php if ($has_user_voted): ?>
                            Total number of votes:
                        <?php endif; ?>
                        <span class="count"><?php echo $has_user_voted
                            ? $pollController->count_total_votes($poll_id)
                            : ""; ?></span>
                        <br>
                        <?php echo $has_user_voted
                            ? "You have already voted for this poll"
                            : ""; ?>
                    </h4>
                </div>
            </form>
        <?php else: ?>
            <p>Sorry, this poll has expired!</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
