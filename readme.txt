=== My Approach ===

My approach to creating this block relied on using WordPress Create Block tools with some modifications, such as rendering the front-end logic in PHP for dynamic content purposes. This allows all polls to update with ease and efficiency, making it easier to modify markup or make structural changes in future. This would have been more of a challenge if rendered via React.

The plugin allows multiple polls to be added to a post, each having their own unique id/key. If the id is not assigned, the poll will not render. Results are stored in a new table called "{prefix}_poll_votes" which I consider the most effective way to capture and track visits, by using their IP address as the identifier rather than relying on localStorage, which users could clear and make multiple submissions. Storing submissions separately also allows for performing analytics.

I have also attached a video demonstrating how the block works and where I tamper and manipulate data using developer tools i.e. modifying the answer value -  which highlights that invalid submissions throws an error message.

I used WP Rest API, registering a custom route (api/v1/poll/submit), where all poll submissions are handled and all possible scenarios were considered such as:

* Sanitizing data
* Storing results securely
* Nonce validation
* Checking if a user has voted
* Handling poll expiry
* Validating answers
* Checking if the poll exists

This plugin adheres to the outlined specifications and requirements with a complete working WordPress Gutenberg block plugin. All assets are bundled and minified for production, ready for installation and src files as well as php files are formatted using Prettier (npm run format) for code quality and organisation.

=== Improvements ===

* I did not focus much on the cosmetics and styling of the polls, but focused more on the core functionality.
* I would have disabled the Save/Publish button when editing a post if certain fields were not filled, such as Poll Question/ID.