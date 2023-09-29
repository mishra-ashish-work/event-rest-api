<?php
class ERA_Event_MetaBox {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_event_meta_box'));
        add_action('save_post', array($this, 'save_event_meta'));
    }

    public function add_event_meta_box() {
        add_meta_box(
            'event_meta_box',
            'Event Details',
            array($this, 'render_event_meta_box'),
            'events',
            'normal',
            'high'
        );
    }

    public function render_event_meta_box($post) {
        $event_start_date = get_post_meta($post->ID, '_event_start_date', true);
        $event_end_date = get_post_meta($post->ID, '_event_end_date', true);

        ?>
        <p>
            <label for="event_start_date">Event Start Date:</label>
            <input type="datetime-local" id="event_start_date" name="event_start_date" value="<?php echo esc_attr($event_start_date); ?>" />
        </p>
        <p>
            <label for="event_end_date">Event End Date:</label>
            <input type="datetime-local" id="event_end_date" name="event_end_date" value="<?php echo esc_attr($event_end_date); ?>" />
        </p>
        <?php
    }

    public function save_event_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['event_start_date'])) {
            update_post_meta($post_id, '_event_start_date', sanitize_text_field($_POST['event_start_date']));
        }

        if (isset($_POST['event_end_date'])) {
            update_post_meta($post_id, '_event_end_date', sanitize_text_field($_POST['event_end_date']));
        }
    }
}

new ERA_Event_MetaBox();