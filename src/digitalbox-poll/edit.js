import { __ } from "@wordpress/i18n";
import {
    useBlockProps,
    InspectorControls,
    MediaUpload,
    MediaUploadCheck
} from "@wordpress/block-editor";
import {
    TextControl,
    DateTimePicker,
    PanelBody,
    Modal,
    Button,
    Placeholder
} from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import { dispatch } from "@wordpress/data";
import { findIndex, set, times } from "lodash";
import moment from "moment";
import "./editor.scss";

const INIT_ANSWERS = 2; // init 2 answers
const MAX_ANSWERS = 5; // max answers
const ALLOWED_MEDIA_TYPES = ["image"]; // only allow images
// answer model
const ANSWER_MODEL = {
    answer: "",
    image_url: ""
};

export default function Edit({ attributes, setAttributes }) {
    const { poll_id, question, answers, expiry } = attributes;
    const [isModalOpen, setIsModalOpen] = useState(false);

    // on init
    useEffect(() => {
        // prepopulate answer model
        if (!answers.length) {
            // use lodash times to init answers with model
            const initAnswers = times(INIT_ANSWERS, () => ({
                ...ANSWER_MODEL
            }));

            setAttributes({ answers: initAnswers });
        }
    }, []);

    // set poll id
    const setPollId = (value) => {
        if (value.length < 45) {
            setAttributes({ poll_id: value.toUpperCase() });
        }
    };

    // set question
    const setQuestion = (value) => {
        setAttributes({ question: value });
    };

    // set expiry
    const setExpiry = (value) => {
        const now = moment();

        // check if date is in past
        if (moment(value).isBefore(now)) {
            dispatch("core/notices").createNotice(
                "error",
                "You cannot select a old date and time",
                {
                    isDismissable: true,
                    type: "snackbar"
                }
            );
            return;
        }

        setAttributes({ expiry: value });

        // alert toast notice
        dispatch("core/notices").createNotice(
            "success",
            "Poll expiry is now set to: " + value,
            {
                isDismissable: true,
                type: "snackbar"
            }
        );
    };

    // toggle modal
    const toggelModal = () => {
        setIsModalOpen(!isModalOpen);
    };

    // clear expiry
    const clearExpiry = () => {
        setAttributes({ expiry: "" });
    };

    // add new answer option
    const addNewAnswer = () => {
        // set new arr, spread answers and add new model
        setAttributes({ answers: [...answers, { ...ANSWER_MODEL }] });
    };

    // remove answer
    const removeAnswer = (i) => {
        // applies removal to answers greater than min
        if (answers.length > INIT_ANSWERS) {
            const currentAnswers = [...answers];
            currentAnswers.splice(i, 1); // remove by index
            setAttributes({ answers: currentAnswers });
        }
    };

    // update answer
    const updateAnswer = (key, value, index) => {
        const currentAnswers = [...answers];
        set(currentAnswers[index], key, value); // update answer by index, key:val
        setAttributes({ answers: currentAnswers });
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title="Poll Settings">
                    <TextControl
                        label="Poll ID"
                        help="Enter a unique poll ID i.e BEST_ACTOR"
                        value={poll_id}
                        onChange={setPollId}
                    />
                    <TextControl
                        label="Question"
                        help="Enter poll question"
                        value={question}
                        onChange={setQuestion}
                    />
                    {expiry && (
                        <>
                            <p>
                                <strong>Poll expiry set at:</strong>
                            </p>
                            <p>
                                {moment(expiry).format(
                                    "MMMM Do YYYY, h:mm:ss a"
                                )}
                                .
                            </p>
                            <Button
                                style={{
                                    display: "block",
                                    marginBottom: "20px"
                                }}
                                variant="link"
                                onClick={clearExpiry}
                            >
                                Clear
                            </Button>
                        </>
                    )}
                    <Button
                        variant="secondary"
                        disabled={isModalOpen}
                        onClick={toggelModal}
                    >
                        Set Expiry Date/Time
                    </Button>
                    {isModalOpen && (
                        <Modal
                            title="Set Poll Expiration Date"
                            onRequestClose={toggelModal}
                        >
                            <DateTimePicker
                                currentDate={expiry}
                                onChange={setExpiry}
                                is12Hour={true}
                            />
                        </Modal>
                    )}
                </PanelBody>
                <PanelBody title="Poll Answers">
                    {answers.length &&
                        answers.map((answer, index) => (
                            <div style={{ marginBottom: "20px" }} key={index}>
                                <TextControl
                                    value={answer.answer}
                                    label={`Answer ${index + 1}`}
                                    onChange={(value) =>
                                        updateAnswer("answer", value, index)
                                    }
                                />
                                <MediaUploadCheck>
                                    <MediaUpload
                                        onSelect={(media) => {
                                            const image =
                                                media.sizes.thumbnail.url ||
                                                media.url;
                                            updateAnswer(
                                                "image_url",
                                                image,
                                                index
                                            );
                                        }}
                                        value={answer.image_url || ""}
                                        allowedTypes={["image"]}
                                        render={({ open }) => (
                                            <div>
                                                {answer.image_url ? (
                                                    <>
                                                        <figure>
                                                            <img
                                                                src={
                                                                    answer.image_url
                                                                }
                                                                width={150}
                                                                height={150}
                                                                alt="image"
                                                            />
                                                        </figure>
                                                        <Button
                                                            isDestructive
                                                            variant="secondary"
                                                            onClick={() =>
                                                                updateAnswer(
                                                                    "image_url",
                                                                    "",
                                                                    index
                                                                )
                                                            }
                                                        >
                                                            Remove Image
                                                        </Button>
                                                    </>
                                                ) : (
                                                    <Button
                                                        variant="secondary"
                                                        onClick={open}
                                                    >
                                                        Select/Upload Image
                                                    </Button>
                                                )}
                                            </div>
                                        )}
                                    />
                                </MediaUploadCheck>
                                {answers.length > INIT_ANSWERS && (
                                    <Button
                                        style={{ marginTop: "10px" }}
                                        isDestructive
                                        variant="link"
                                        onClick={() => removeAnswer(index)}
                                    >
                                        Remove Answer {index + 1}
                                    </Button>
                                )}
                            </div>
                        ))}
                    {answers.length < MAX_ANSWERS && (
                        <Button
                            style={{ marginTop: "20px" }}
                            variant="primary"
                            onClick={addNewAnswer}
                        >
                            Add New Answer
                        </Button>
                    )}
                </PanelBody>
            </InspectorControls>

            <div {...useBlockProps()}>
                <Placeholder
                    label={`Poll: ${question}`}
                    instructions="Allows editors to embed interactive polls into entertainment news articles. Readers can vote on entertainment-related questions, and results will display in real-time without page refresh."
                />
            </div>
        </>
    );
}
