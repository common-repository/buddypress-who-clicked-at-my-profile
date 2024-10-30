<?php
// recreate pot file? excute this in the plugin's directory  
// xgettext --language=PHP --from-code=utf-8 --keyword=__ --keyword=_e *.php -o languages/buddypresswcamp.pot
// Load translations and text domain
add_action('init', 'buddypresswcamp_load_textdomain');

/**
 * This function just loads language files
 */
function buddypresswcamp_load_textdomain() {
    load_plugin_textdomain('buddypresswcamp', false, dirname(plugin_basename(__FILE__)) . "/languages/");
}

// Register Action for this PLugin
add_action('bp_before_member_header', 'buddypresswcamp_action');

/**
 * This function loggs visits at your profile page
 * @global type $bp
 * @global type $wpdb
 */
function buddypresswcamp_action() {
    global $bp;
    $numberOfTrackedVisits = apply_filters('buddypress_wcamp_quantity', 25);
    $useBuddypressNotifications = apply_filters('buddypress_wcamp_usenotifications', true);
    $current_user = wp_get_current_user();
    $displayed_user_id = $bp->displayed_user->id;
    $excludeUsers = apply_filters('buddypress_wcamp_excludeUsers', array());
    $viewing_user_id = $current_user->ID;
    if (($displayed_user_id != $viewing_user_id) && ($viewing_user_id > 0) && (!in_array($current_user->ID, $excludeUsers))) {
        // get user meta data (clickedme_tracking is a serialized array containing the last visits)
        $meta = get_user_meta($displayed_user_id, 'clickedme_tracking', true);
        $trackingList = unserialize($meta);
        // there is no trackinglist yet? create one now...
        if (!is_array($trackingList)) {
            $trackingList = array();
        }
        // remove double clicks. latest click will be the interesting click for us
        // double clicks are only possible if actual user that should be added as new
        // click already clicked the profile before. 
        // In this case we also have to remove the (maybe) existing notification
        // viewing user should be first of array list.
        $newTrackingList = array('user_id' => $viewing_user_id, 'ts' => time());
        $i = 1;
        foreach ($trackingList as $trackingListItem) {
            // version < 3.0 compatiility
            if (!is_array($trackingListItem)) {
                $trackingListItem = array('user_id' => $trackingListItem, 'ts' => 0);
            }
            // remove double clicks
            if (($viewing_user_id != $trackingListItem['user_id']) && (!in_array($trackingListItem['user_id'], $excludeUsers))) {
                $newTrackingList[] = $trackingListItem;
            } else if ($useBuddypressNotifications) {
                // remove old notification if double click was made because new 
                // notification (maybe old one was not read yet) will be set up
                bp_notifications_mark_notifications_by_item_id($displayed_user_id, $viewing_user_id, 'bp_wcamp', 'visit');
            }
            $i++;
            if ($i == $numberOfTrackedVisits) {
                break;
            }
        }

        // Store new user meta data
        update_user_meta($displayed_user_id, 'clickedme_tracking', serialize($newTrackingList), $meta);

        // Buddypress Notification for new profile visitor
        if ($useBuddypressNotifications) {
            $args = array(
                'user_id' => $displayed_user_id,
                'item_id' => $viewing_user_id,
                'component_name' => 'bp_wcamp',
                'component_action' => 'visit',
                'date_notified' => date("Y-m-d H:i:s", time())
            );
            bp_notifications_add_notification($args);
            // remove possible notification for this profile
            bp_notifications_mark_notifications_by_item_id($viewing_user_id, $displayed_user_id, 'bp_wcamp', 'visit');
        }
    }
}

add_shortcode('buddypresswcamp_show_visits', 'buddypresswcamp_show_visits');

/**
 * 
 * @param array 
 * $instance['showAvatars'] default 0, set to 1 to use avatar display
 * $instance['amount'] default 12, set to number how many visits should be displayed
 * @return output
 */
function buddypresswcamp_show_visits($instance) {
    $content = "";
    // shortcode attributes seem to be passed lowercase.
    if ($instance['showavatars'] == 1) {
        $instance['showAvatars'] = 1;
    }
    $showAvatars = apply_filters('widget_avatar', $instance['showAvatars']);
    $amount = apply_filters('widget_amount', $instance['amount']);
    if ((int) $amount == 0) {
        $amount = 12;
    }
    // Main content
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $meta = get_user_meta($current_user->ID, 'clickedme_tracking', true);
        $trackingList = unserialize($meta);
        // tracking list is array with arrays (user_id and unix timestamp). 
        // for stored data < 2.1 tracking list only was array of user-Ids
        if (empty($trackingList)) {
            $content = __('Your profile has not been visited yet by another member of the community.', 'buddypresswcamp');
        } else {
            $content = "";
            $i = 0;
            foreach ($trackingList as $trackingListItem) {
                if (!is_array($trackingListItem)) {
                    $item = $trackingListItem;
                } else {
                    $item = $trackingListItem['user_id'];
                }
                if ($current_user->ID != $item) {
                    $data = get_userdata($item);
                    if (!empty($data)) {
                        if ($i >= $amount) {
                            break;
                        } else {
                            $i++;
                        }
                        //if ($current_user->ID == 1) die(var_dump ($data));
                        if ($data->ID > 0) {
                            $current_user = wp_get_current_user();
                            if ($showAvatars == 1) {
                                $content.= '<a href="' . bp_core_get_userlink($data->ID, false, true) . '">' . bp_core_fetch_avatar(array('object' => 'user', 'item_id' => $data->ID)) . '</a>';
                            } else {
                                $resultLinks[] = str_replace('href=', 'class="avatar" rel="user_' . $data->ID . '" href=', bp_core_get_userlink($data->ID));
                            }
                        }
                    }
                }
            }
            if ($showAvatars == 0) {
                $content = __('Your profile has been visited by:', 'buddypresswcamp') . ' ' . implode(', ', $resultLinks);
            } else {
                $content.='<br style="clear:both;">';
            }
        }
    } else {
        $content.=__('Please log in to view the visitors of your profile', 'buddypresswcamp');
    }
    return '<p>' . $content . '</p>';
}

add_action('widgets_init', 'buddypresswcamp_widget_showMyVisitors');

function buddypresswcamp_widget_showMyVisitors() {
    register_widget('BuddypressWCAMP_Widget_showMyVisitors');
}

class BuddypressWCAMP_Widget_showMyVisitors extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'buddypresswcamp', 'description' => __('Show visitors of my buddypress profile page', 'buddypresswcamp'));
        parent::__construct('buddypresswcamp-widget-showMyVisitors', __('Show bp profile visitors', 'buddypresswcamp'), $widget_ops);
    }

    function widget($args, $instance) {
        extract($args);

        global $bp;

        //Our variables from the widget settings.
        $title = apply_filters('widget_title', $instance['title']);
        $output = buddypresswcamp_show_visits($instance);
        echo $before_widget;

        // Display the widget title 
        if ($title)
            echo $before_title . $title . $after_title;

        echo $output;
        echo $after_widget;
    }

    //Update the widget 

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        //Strip tags from title and name to remove HTML 
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['showAvatars'] = strip_tags($new_instance['showAvatars']);
        $instance['amount'] = strip_tags($new_instance['amount']);

        return $instance;
    }

    function form($instance) {

        //Set up some default widget settings.
        $defaults = array(
            'title' => __('Last visitors of your profile', 'buddypresswcamp'),
            'showAvatars' => 0,
            'amount' => 12
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'buddypresswcamp'); ?>:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('showAvatars'); ?>" name="<?php echo $this->get_field_name('showAvatars'); ?>" value="1" <?php if ($instance['showAvatars'] == 1) echo 'checked="checked" ' ?> />
            <label for="<?php echo $this->get_field_id('showAvatars'); ?>"><?php _e('Show Avatars instead of links to last visitors profile pages', 'buddypresswcamp'); ?>:</label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('Amount of users that should be shown', 'buddypresswcamp'); ?>:</label>
            <input type="number" id="<?php echo $this->get_field_id('amount'); ?>" value="<?php echo $instance['amount']; ?>" name="<?php echo $this->get_field_name('amount'); ?>" />
        </p>
        <?php
    }

}

function buddypresswcamp_notification_get_registered_components($component_name = array()) {
    if (!is_array($component_name)) {
        $component_name = array();
    }
    array_push($component_name, 'bp_wcamp');
    return $component_name;
}

add_filter('bp_notifications_get_registered_components', 'buddypresswcamp_notification_get_registered_components', 1);

function buddypresswcamp_format_buddypress_notifications($action, $item_id, $secondary_item_id, $total_items, $format = 'string') {

    if ('visit' !== $action) {
        return $action;
    }
    if ($action === 'visit') {
        $visitor = get_user_by('id', $item_id);
        $link = bp_core_get_userlink($visitor->ID, false, true);
        $text = sprintf(__('%s visited your profile', 'buddypresswcamp'), $visitor->user_nicename);
        $text = str_replace(" ", " ", $text);
        $return = '<a href="' . $link . '">' . $text . '</a>';
        return $return;
    }
}

add_filter('bp_notifications_get_notifications_for_user', 'buddypresswcamp_format_buddypress_notifications', 10, 5);

add_action('all_admin_notices', 'buddypresswcamp_upgrade_donation_notification');

function buddypresswcamp_upgrade_donation_notification() {
    $pluginData = get_plugin_data(__FILE__, false, false);
    $pluginVersion = $pluginData['Version'];
    $pluginName = $pluginData['Name'];
    $lastNotification = get_option('bpwcamp_version', 0);
    if ($lastNotification != $pluginVersion) {
        if ((float) $pluginVersion <= 4.0) {
            $additionalContent.= '<b>Attention:</b> bbpress-bug: If you use bbpress < 2.6 please apply the changes described there: <a href="https://bbpress.trac.wordpress.org/ticket/2779" target="_blanc">https://bbpress.trac.wordpress.org/ticket/2779</a> to get the notifications working';
        }
        echo '<div class="updated notice bpwcamp-notice is-dismissible" >'
        . '<p><img src="http://ifs-net.de/grafik/logo_puzzle.jpg" align="right">Thank you for using <b>' . $pluginName . ' (your version ' . $pluginVersion . ')</b><br />'
        . 'It seems as if you freshly installed or upgraded this plugin.</p>'
        . '<p><b>You need support?</b> <a target="_blanc" href="http://wordpress.org/support/plugin/buddypress-who-clicked-at-my-profile">visit the support forum</a> or take a look into the <a href="https://wordpress.org/plugins/buddypress-who-clicked-at-my-profile/faq/" target="_blanc">FAQ</a></p>'
        . '<p><b>You want more of my community plugins?</b> <a target="_blanc" href="https://wordpress.org/plugins/search.php?q=quan_flo">visit my wordpress plugin repository</a></p>'
        . '<p><b style="color: red">I spent a lot of time for this plugin and you like it? Fine!</b><br />'
        . '==&gt; <a target="_blanc" href="https://wordpress.org/support/view/plugin-reviews/buddypress-who-clicked-at-my-profile#postform">Rate my plugin in wordpress plugin repository</a><br />'
        . '==&gt; <a target="_blanc" href="http://ifs-net.de/donate.php">Feel free to donate for it. Even some dollars help - Thank you! Just click here...</a></p>'
        . '<p><b>Thank you very much!</b></p>' . $additionalContent . '</div>';
    }
}

add_action('admin_enqueue_scripts', 'buddypresswcamp_enqueue_scripts');

function buddypresswcamp_enqueue_scripts() {
    wp_enqueue_script('buddypresswcamp-update-notification', plugins_url('/js/update-notification.js', __FILE__), array('jquery'), false, false);
}

add_action('wp_ajax_bpwcamp_notification', 'buddypresswcamp_remove_update_notification');

function buddypresswcamp_remove_update_notification() {
    $pluginData = get_plugin_data(__FILE__, false, false);
    $version = $pluginData['Version'];
    delete_option('bpwcamp_version');
    add_option('bpwcamp_version', $version, '', 'no');
}
?>