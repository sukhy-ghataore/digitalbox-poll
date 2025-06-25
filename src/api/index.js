import axios from "axios";

const namespace = "api";
const version = "v1";

const wpx = axios.create({
    baseURL: `${wpApiSettings.root}${namespace}/${version}`,
    headers: {
        "X-WP-Nonce": wpApiSettings.nonce
    }
});

const pollApi = {
    submit(data) {
        return wpx.post("/poll/submit", data);
    }
};

export { pollApi };
