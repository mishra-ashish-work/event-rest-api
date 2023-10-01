# Event Rest API WordPress Plugin

The **Event Rest API** is a WordPress plugin that enables the creation, management, and retrieval of events through a RESTful API. It allows you to perform CRUD (Create, Read, Update, Delete) operations on events.

## Features

- Create, update, and delete events.
- Retrieve a list of events based on various criteria.
- Secure JSON basic authentication for API access.
- Custom meta box for managing event details.

## Installation

1. Download the plugin ZIP file from the [plugin repository](#) or GitHub.
2. Log in to your WordPress admin dashboard.
3. Navigate to "Plugins" -> "Add New" and click on the "Upload Plugin" button.
4. Choose the ZIP file you downloaded in step 1 and click "Install Now."
5. After installation, click "Activate Plugin" to enable the Event Rest API.

## Configuration

No additional configuration is required for the Event Rest API plugin. It is ready to use once activated.

## Usage

### Creating an Event

To create an event, you can make a POST request to the following endpoint:

/wp-json/era/v1/events/create


Include the following parameters in your request:

- `title`: The title of the event.
- `description`: The event description.
- `start`: The start date and time of the event (format: YYYY-MM-DDTHH:MM:SS).
- `end`: The end date and time of the event (format: YYYY-MM-DDTHH:MM:SS).
- `category`: The category of the event.

### Updating an Event

To update an event, make a POST request to the following endpoint:

/wp-json/era/v1/events/update


Include the following parameters in your request:

- `id`: The ID of the event you want to update.
- `title`: The updated title of the event.
- `description`: The updated event description.
- `start`: The updated start date and time of the event.
- `end`: The updated end date and time of the event.
- `category`: The updated category of the event.

### Deleting an Event

To delete an event, make a DELETE request to the following endpoint:

/wp-json/era/v1/events/delete


Include the following parameter in your request:

- `id`: The ID of the event you want to delete.

### Retrieving Events

To retrieve a list of events based on specific criteria, make a GET request to the following endpoint:

/wp-json/era/v1/events/list


Include query parameters to filter the events:

- `date`: Retrieve events on a specific date (format: YYYY-MM-DDTHH:MM:SS).
- `datebtw`: Retrieve events between two dates (format: YYYY-MM-DDTHH:MM:SS TO YYYY-MM-DDTHH:MM:SS).
- `cat`: Retrieve events by category.
- `title`: Retrieve events with a specific title.

### Basic Authentication

The Event Rest API uses JSON basic authentication for security. Ensure that you have administrator privileges to access the API endpoints.

## Custom Meta Box

The plugin adds a custom meta box to the WordPress post editor for managing event details. You can set the start and end dates of events using this meta box.

## License

This plugin is licensed under the GPLv2 license. For more details, please refer to the [GNU General Public License, version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

## Author

- **Author:** Ashish Mishra
- **Author URI:** [https://www.storeapps.org/](https://www.storeapps.org/)

## Version

- **Version:** 1.0