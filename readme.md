## Digitalbox Poll

This plugin allows editors to embed interactive polls into entertainment news articles utilising Gutenberg blocks. Readers can vote on entertainment-reated questions, and results will display in real-time without page refresh.

This plugin allows for support of multiple polls on a post.

## Features

**Allow editors to set:**

- Poll question (e.g. "Who will win Best Actor?")
- Poll expiration date/time option
- 2-5 answer options (min 2 and max 5 answer options)
    - Optional image for each answer option

**Front-end functionality:**

- Displays poll question
- Answer options with radio buttons
- Submit vote button
- Results display after the user votes without page refresh
- Total number of votes for poll as well as votes per answer
- Tracks one vote per visitor

## Installation

1. Upload the plugin files to the `/wp-content/plugins/digitalbox-poll` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

## How To Add Block & Usage

In WordPress, select any post to edit, and choose a block to add. Search for `digitalbox poll`. Each blocks supports:

- Poll ID
    - A short unique id/key of the poll i.e. BEST_ACTOR
    - Max characters 45
- Poll Question
    - A question of your choosing
    - Defaulted to "Who will win Best Actor?""
- Poll Datetime Expiry
    - Opens as a modal
    - You cannot enter a past datetime
    - Ability to clear datetime
- Poll Answers
    - A answer option
        - A min of 2 answer options are displayed by default
        - A max of 5 answer options can be set
        - If no answer option specified, this will not render on front-end
    - Optional image to be set and removed

## Command Usage

Go to `/wp-content/plugins/digitalbox-poll` and open terminal/cmd to run either commands:

```bash
# to format scripts
npm run format
```

```bash
# to run dev mode and watch files
npm run start
```

```bash
# to build bundle production assets
npm run build
```
