import { pollApi } from "../api";

document.addEventListener("DOMContentLoaded", () => {
    const polls = document.querySelectorAll("form[data-poll-id]");

    // check if polls exist
    if (polls.length) {
        // handle each poll
        polls.forEach((poll, index) => {
            // on form subimt
            poll.addEventListener("submit", (e) => {
                e.preventDefault();

                const post_id = poll.getAttribute("data-post-id");
                const poll_id = poll.getAttribute("data-poll-id");
                const answer = poll.querySelector("input:checked")?.value;
                const submit_btn = poll.querySelector(
                    ".submit-poll-btn button"
                );

                // if no answer, throw alert
                if (!answer) {
                    alert("Please select a answer");
                    return;
                }

                // do loading state
                submit_btn.setAttribute("disabled", true);
                submit_btn.innerText = "Voting...";

                // assign model
                const model = {
                    poll_id: poll_id,
                    post_id: parseInt(post_id),
                    answer: answer
                };

                // do api request
                pollApi.submit(model).then(
                    (res) => {
                        if (!res.data.error) {
                            // show results
                            poll.querySelector(
                                ".total-votes .count"
                            ).innerText =
                                `Total number of votes: ${res.data.poll_results.total_votes}`;

                            // update each answer vote too
                            Object.entries(
                                res.data.poll_results.answer_total_votes
                            ).forEach(([key, value]) => {
                                poll.querySelector(
                                    `input[type="radio"][value="${key}"] ~ .total-answer-votes`
                                ).innerText = `- ${value} vote(s)`;
                            });

                            // remove btns/radios
                            submit_btn.remove();
                            poll.querySelectorAll(
                                'input[type="radio"]'
                            ).forEach((radio) => radio.remove());

                            // then alert thank you msg to user
                            setTimeout(() => {
                                alert("Thank you for votting!");
                            }, 1000);
                        }
                    },
                    (error) => {
                        submit_btn.removeAttribute("disabled");
                        submit_btn.innerText = "Vote";

                        // throw poll error messages
                        alert(
                            error.response.data.error
                                ? error.response.data.message
                                : "There was a problem, please try again later"
                        );
                        console.log(error);
                    }
                );
            });
        });
    }
});
