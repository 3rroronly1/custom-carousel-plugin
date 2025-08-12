<?php
/*
Plugin Name: Custom Carousel Plugin
Description: A premium carousel plugin to create multiple carousels with customizable dimensions, gaps, and colors using shortcodes.
Version: 1.8
Author: 3rrorOnly1
Author URI: https://buymeacoffee.com/3rrorOnly1
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function ccp3o1_enqueue_scripts() {
    wp_enqueue_style('ccp3o1-slick-css', plugins_url('assets/slick/slick.css', __FILE__), [], '1.8.1');
    wp_enqueue_style('ccp3o1-slick-theme-css', plugins_url('assets/slick/slick-theme.css', __FILE__), [], '1.8.1');
    wp_enqueue_script('ccp3o1-slick-js', plugins_url('assets/slick/slick.min.js', __FILE__), ['jquery'], '1.8.1', true);
    wp_enqueue_style('ccp3o1-styles', plugins_url('assets/css/ccp3o1-styles.css', __FILE__), [], '1.8');
    wp_enqueue_script('ccp3o1-scripts', plugins_url('assets/js/ccp3o1-scripts.js', __FILE__), ['jquery', 'ccp3o1-slick-js'], '1.8', true);
    // Ensure dashicons for arrows on frontend
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'ccp3o1_enqueue_scripts');

// Admin scripts
function ccp3o1_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_ccp3o1-custom-carousel') {
        return;
    }
    wp_enqueue_style('ccp3o1-admin-css', plugins_url('assets/css/ccp3o1-admin.css', __FILE__), [], '1.8');
    wp_enqueue_script('ccp3o1-admin-js', plugins_url('assets/js/ccp3o1-admin.js', __FILE__), ['jquery', 'wp-color-picker'], '1.8', true);
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'ccp3o1_admin_scripts');

// Create admin menu
function ccp3o1_admin_menu() {
    add_menu_page(
        'Custom Carousel',
        'Custom Carousel',
        'manage_options',
        'ccp3o1-custom-carousel',
        'ccp3o1_admin_page',
        'dashicons-images-alt2',
        20
    );
}
add_action('admin_menu', 'ccp3o1_admin_menu');

// Admin page
function ccp3o1_admin_page() {
    global $wpdb;
    // Prevent caching
    // Headers removed to avoid issues in admin rendering

    // Handle manual reset
    if (isset($_POST['ccp3o1_reset_carousels']) && check_admin_referer('ccp3o1_reset_carousels_nonce', 'ccp3o1_reset_nonce')) {
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name = 'ccp3o1_carousels'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ccp3o1_%' OR option_name LIKE '_transient_timeout_ccp3o1_%'");
        error_log('Carousels option reset manually at ' . date('Y-m-d H:i:s'));
        add_settings_error('ccp3o1_messages', 'ccp3o1_reset_success', 'Carousels reset successfully. Create a new carousel to continue.', 'success');
    }

    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true' && !get_settings_errors()) {
        add_settings_error('ccp3o1_messages', 'ccp3o1_success', 'Carousel saved successfully!', 'success');
    }
    if (isset($_GET['ccp3o1_action']) && $_GET['ccp3o1_action'] === 'updated' && !get_settings_errors()) {
        add_settings_error('ccp3o1_messages', 'ccp3o1_update_success', 'Carousel updated successfully!', 'success');
    }
    if (isset($_GET['ccp3o1_action']) && $_GET['ccp3o1_action'] === 'delete' && !get_settings_errors()) {
        add_settings_error('ccp3o1_messages', 'ccp3o1_delete_success', 'Carousel deleted successfully!', 'success');
    }
    ?>
    <div class="wrap ccp3o1-premium-wrap">
        <h1><span class="dashicons dashicons-images-alt2"></span> Custom Carousel Plugin</h1>
        <div class="ccp3o1-donate">
            <p>Love this plugin? Support the developer!</p>
            <a href="https://buymeacoffee.com/3rrorOnly1" target="_blank" class="ccp3o1-donate-button">
                <span class="dashicons dashicons-coffee"></span> Buy Me a Coffee
            </a>
        </div>
        <?php settings_errors('ccp3o1_messages'); ?>
        <form method="post" action="options.php" id="ccp3o1-form">
            <?php
            settings_fields('ccp3o1_settings_group');
            do_settings_sections('ccp3o1-custom-carousel');
            submit_button('Save Carousel', 'primary', 'submit', true, ['class' => 'ccp3o1-submit-button']);
            ?>
        </form>
        <?php
        // Edit existing carousel form (if requested)
        if (isset($_GET['edit'])) {
            $edit_id = sanitize_text_field(wp_unslash($_GET['edit']));
            $all_carousels = get_option('ccp3o1_carousels', []);
            if (is_array($all_carousels) && isset($all_carousels[$edit_id])) {
                $c = $all_carousels[$edit_id];
                $items = isset($c['items']) && is_array($c['items']) ? array_map('absint', $c['items']) : [];
                $dimensions = isset($c['dimensions']) ? $c['dimensions'] : '500x800';
                $visible_items = isset($c['visible_items']) ? absint($c['visible_items']) : 3;
                $speed = isset($c['speed']) ? absint($c['speed']) : 300;
                $gap = isset($c['gap']) ? absint($c['gap']) : 10;
                $gap_color = isset($c['gap_color']) ? $c['gap_color'] : '#ffffff';
                $smooth = !empty($c['smooth']) ? 1 : 0;
                $items_value = implode(',', $items);
                ?>
                <div id="ccp3o1-carousel-edit" class="ccp3o1-form-card">
                    <h2><span class="dashicons dashicons-edit"></span> Edit Carousel</h2>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="ccp3o1-edit-form">
                        <input type="hidden" name="action" value="ccp3o1_update_carousel" />
                        <input type="hidden" name="carousel_id" value="<?php echo esc_attr($edit_id); ?>" />
                        <?php wp_nonce_field('ccp3o1_update_carousel_nonce', 'ccp3o1_update_nonce'); ?>

                        <p><strong><span class="dashicons dashicons-format-gallery"></span> Edit Media (Images/Videos):</strong></p>
                        <input type="hidden" id="ccp3o1-edit-media-ids" name="ccp3o1_edit_carousel[items]" value="<?php echo esc_attr($items_value); ?>">
                        <div id="ccp3o1-edit-media-preview">
                            <?php
                            foreach ($items as $item_id) {
                                $url = wp_get_attachment_url($item_id);
                                if (!$url) { continue; }
                                $type = wp_check_filetype($url);
                                if (strpos($type['type'], 'image') !== false) {
                                    echo '<img src="' . esc_url($url) . '" style="max-width:100px; margin:5px;" />';
                                } elseif (strpos($type['type'], 'video') !== false) {
                                    echo '<video src="' . esc_url($url) . '" style="max-width:100px; margin:5px;" controls></video>';
                                }
                            }
                            ?>
                        </div>
                        <button type="button" id="ccp3o1-edit-add-media" class="button ccp3o1-button"><span class="dashicons dashicons-plus-alt"></span> Update Media</button>

                        <p><strong><span class="dashicons dashicons-editor-expand"></span> Dimensions (width x height):</strong></p>
                        <input type="text" name="ccp3o1_edit_carousel[dimensions]" placeholder="e.g., 500x800" value="<?php echo esc_attr($dimensions); ?>" class="ccp3o1-input">

                        <p><strong><span class="dashicons dashicons-visibility"></span> Visible Items:</strong></p>
                        <input type="number" name="ccp3o1_edit_carousel[visible_items]" value="<?php echo esc_attr($visible_items); ?>" min="1" max="10" class="ccp3o1-input">

                        <p><strong><span class="dashicons dashicons-clock"></span> Speed (ms):</strong></p>
                        <input type="number" name="ccp3o1_edit_carousel[speed]" value="<?php echo esc_attr($speed); ?>" min="100" max="5000" class="ccp3o1-input">

                        <p><strong><span class="dashicons dashicons-align-center"></span> Gap Between Items (px):</strong></p>
                        <input type="number" name="ccp3o1_edit_carousel[gap]" value="<?php echo esc_attr($gap); ?>" min="0" max="100" class="ccp3o1-input">

                        <p><strong><span class="dashicons dashicons-art"></span> Gap Color:</strong></p>
                        <input type="text" name="ccp3o1_edit_carousel[gap_color]" class="ccp3o1-color-picker" value="<?php echo esc_attr($gap_color); ?>">

                        <p><strong><span class="dashicons dashicons-performance"></span> Smooth Carousel:</strong></p>
                        <label><input type="checkbox" name="ccp3o1_edit_carousel[smooth]" value="1" <?php checked(1, $smooth); ?> /> Enable smoother transitions</label>

                        <?php submit_button('Update Carousel', 'primary', 'submit', true, ['class' => 'ccp3o1-submit-button']); ?>
                    </form>
                </div>
                <script>
                    jQuery(document).ready(function($) {
                        $('.ccp3o1-color-picker').wpColorPicker();
                    });
                </script>
                <?php
            }
        }
        ?>
        <div id="ccp3o1-carousel-list">
            <h2><span class="dashicons dashicons-list-view"></span> Existing Carousels</h2>
            <?php ccp3o1_display_carousels(); ?>
        </div>
        <div class="ccp3o1-reset-section">
            <h2><span class="dashicons dashicons-warning"></span> Reset Carousels</h2>
            <p><strong>Warning:</strong> This will delete all carousels. Use only if deletion issues persist.</p>
            <form method="post" action="">
                <?php wp_nonce_field('ccp3o1_reset_carousels_nonce', 'ccp3o1_reset_nonce'); ?>
                <button type="submit" name="ccp3o1_reset_carousels" class="button ccp3o1-button ccp3o1-reset-button"><span class="dashicons dashicons-update"></span> Reset All Carousels</button>
            </form>
        </div>
        <p class="ccp3o1-footer">Developed with <span class="dashicons dashicons-heart"></span> by <a href="https://buymeacoffee.com/3rrorOnly1" target="_blank">3rrorOnly1</a></p>
    </div>
    <?php
}

// Register settings
function ccp3o1_register_settings() {
    register_setting('ccp3o1_settings_group', 'ccp3o1_carousels', [
        'sanitize_callback' => 'ccp3o1_sanitize_carousels'
    ]);

    add_settings_section(
        'ccp3o1_main_section',
        '<span class="dashicons dashicons-admin-settings"></span> Add New Carousel',
        null,
        'ccp3o1-custom-carousel'
    );

    add_settings_field(
        'ccp3o1_new_carousel',
        'Carousel Settings',
        'ccp3o1_carousel_fields',
        'ccp3o1-custom-carousel',
        'ccp3o1_main_section'
    );
}
add_action('admin_init', 'ccp3o1_register_settings');

// Sanitize input
function ccp3o1_sanitize_carousels($input) {
    global $wpdb;
    $carousels = get_option('ccp3o1_carousels', []);
    if (!is_array($carousels)) {
        $carousels = [];
    }

    // Log current carousels state
    error_log('Sanitizing carousels - Current state: ' . print_r($carousels, true));

    // Initialize new carousel with defaults
    $new_carousel = isset($input['new_carousel']) && is_array($input['new_carousel']) ? $input['new_carousel'] : [];

    // Validate items
    $items = [];
    if (!empty($new_carousel['items']) && is_string($new_carousel['items'])) {
        $item_ids = array_filter(array_map('absint', explode(',', $new_carousel['items'])));
        foreach ($item_ids as $id) {
            if (wp_get_attachment_url($id)) {
                $items[] = $id;
            }
        }
    }
    if (empty($items)) {
        add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Please select at least one image or video.', 'error');
        return $carousels;
    }

    // Validate dimensions
    $dimensions = isset($new_carousel['dimensions']) ? sanitize_text_field($new_carousel['dimensions']) : '500x800';
    if (!preg_match('/^\d+x\d+$/', $dimensions)) {
        add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Invalid dimensions format. Use widthxheight (e.g., 500x800).', 'error');
        return $carousels;
    }

    // Set defaults for other fields
    $carousel_id = uniqid('carousel_');
    $speed = isset($new_carousel['speed']) ? absint($new_carousel['speed']) : 300;
    $smooth_flag = !empty($new_carousel['smooth']) ? 1 : 0;
    if ($smooth_flag && $speed < 600) {
        // Auto-adjust speed for smoother transition if too low
        $speed = 800;
    }
    $carousels[$carousel_id] = [
        'items' => $items,
        'dimensions' => $dimensions,
        'visible_items' => isset($new_carousel['visible_items']) ? absint($new_carousel['visible_items']) : 3,
        'speed' => $speed,
        'gap' => isset($new_carousel['gap']) ? absint($new_carousel['gap']) : 10,
        'gap_color' => isset($new_carousel['gap_color']) ? (sanitize_hex_color($new_carousel['gap_color']) ?: '#ffffff') : '#ffffff',
        'smooth' => $smooth_flag
    ];

    // Force update to ensure save
    $wpdb->update(
        $wpdb->options,
        ['option_value' => maybe_serialize($carousels)],
        ['option_name' => 'ccp3o1_carousels']
    );
    error_log('Carousels updated after sanitization: ' . print_r($carousels, true));

    return $carousels;
}

// Display carousel fields
function ccp3o1_carousel_fields() {
    ?>
    <div id="ccp3o1-carousel-form" class="ccp3o1-form-card">
        <p><strong><span class="dashicons dashicons-format-gallery"></span> Add Media (Images/Videos):</strong></p>
        <input type="hidden" id="ccp3o1-media-ids" name="ccp3o1_carousels[new_carousel][items]" value="">
        <div id="ccp3o1-media-preview"></div>
        <button type="button" id="ccp3o1-add-media" class="button ccp3o1-button"><span class="dashicons dashicons-plus-alt"></span> Add Media</button>
        <p><strong><span class="dashicons dashicons-editor-expand"></span> Dimensions (width x height):</strong></p>
        <input type="text" name="ccp3o1_carousels[new_carousel][dimensions]" placeholder="e.g., 500x800" value="500x800" class="ccp3o1-input">
        <p><strong><span class="dashicons dashicons-visibility"></span> Visible Items:</strong></p>
        <input type="number" name="ccp3o1_carousels[new_carousel][visible_items]" value="3" min="1" max="10" class="ccp3o1-input">
        <p><strong><span class="dashicons dashicons-clock"></span> Speed (ms):</strong></p>
        <input type="number" name="ccp3o1_carousels[new_carousel][speed]" value="300" min="100" max="5000" class="ccp3o1-input">
        <p><strong><span class="dashicons dashicons-align-center"></span> Gap Between Items (px):</strong></p>
        <input type="number" name="ccp3o1_carousels[new_carousel][gap]" value="10" min="0" max="100" class="ccp3o1-input">
        <p><strong><span class="dashicons dashicons-art"></span> Gap Color:</strong></p>
        <input type="text" name="ccp3o1_carousels[new_carousel][gap_color]" class="ccp3o1-color-picker" value="#ffffff">
        <p><strong><span class="dashicons dashicons-performance"></span> Smooth Carousel:</strong></p>
        <label><input type="checkbox" name="ccp3o1_carousels[new_carousel][smooth]" value="1" /> Enable smoother transitions</label>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('.ccp3o1-color-picker').wpColorPicker();
        });
    </script>
    <?php
}

// Display existing carousels
function ccp3o1_display_carousels() {
    global $wpdb;
    // Fetch raw option value for debugging
    $raw_option = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'ccp3o1_carousels'");
    error_log('Raw ccp3o1_carousels option: ' . $raw_option);
    $carousels_db = maybe_unserialize($raw_option);
    $carousels = is_array($carousels_db) ? $carousels_db : get_option('ccp3o1_carousels', []);
    error_log('Parsed carousels: ' . print_r($carousels, true));
    if (!is_array($carousels) || empty($carousels)) {
        echo '<p class="ccp3o1-no-items">No carousels created yet.</p>';
        return;
    }

    foreach ($carousels as $id => $carousel) {
        echo '<div class="ccp3o1-carousel ccp3o1-card">';
        echo '<h3><span class="dashicons dashicons-images-alt"></span> Carousel ID: ' . esc_html($id) . '</h3>';
        echo '<p><strong>Shortcode:</strong> <code>[ccp3o1_custom_carousel id="' . esc_attr($id) . '"]</code></p>';
        echo '<p><strong>Dimensions:</strong> ' . esc_html($carousel['dimensions']) . '</p>';
        echo '<p><strong>Visible Items:</strong> ' . esc_html($carousel['visible_items']) . '</p>';
        echo '<p><strong>Speed:</strong> ' . esc_html($carousel['speed']) . 'ms</p>';
        echo '<p><strong>Gap:</strong> ' . esc_html($carousel['gap']) . 'px</p>';
        echo '<p><strong>Gap Color:</strong> <span style="display:inline-block; width:20px; height:20px; background-color:' . esc_attr($carousel['gap_color']) . ';"></span> ' . esc_html($carousel['gap_color']) . '</p>';
        $is_smooth = !empty($carousel['smooth']);
        echo '<p><strong>Smooth:</strong> ' . ($is_smooth ? 'Enabled' : 'Disabled') . '</p>';
        echo '<p><strong>Items:</strong></p>';
        echo '<div class="ccp3o1-media-list">';
        foreach ($carousel['items'] as $item_id) {
            $url = wp_get_attachment_url($item_id);
            if (!$url) continue;
            $type = wp_check_filetype($url);
            if (strpos($type['type'], 'image') !== false) {
                echo '<img src="' . esc_url($url) . '" style="max-width:100px; margin:5px;">';
            } elseif (strpos($type['type'], 'video') !== false) {
                echo '<video src="' . esc_url($url) . '" style="max-width:100px; margin:5px;" controls></video>';
            }
        }
        echo '</div>';
        echo '<div style="display:flex; gap:10px; align-items:center; margin-top:10px;">';
        echo '<a href="' . esc_url(admin_url('admin.php?page=ccp3o1-custom-carousel&edit=' . urlencode($id))) . '" class="button ccp3o1-button"><span class="dashicons dashicons-edit"></span> Edit</a>';
        echo '<form method="post" action="' . admin_url('admin-post.php') . '" class="ccp3o1-delete-form" style="display:inline;">';
        echo '<input type="hidden" name="action" value="ccp3o1_delete_carousel">';
        echo '<input type="hidden" name="carousel_id" value="' . esc_attr($id) . '">';
        echo wp_nonce_field('ccp3o1_delete_carousel_nonce', 'ccp3o1_nonce', true, false);
        echo '<button type="submit" class="button ccp3o1-button ccp3o1-delete-button"><span class="dashicons dashicons-trash"></span> Delete Carousel</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
}

// Handle carousel deletion
function ccp3o1_delete_carousel() {
    global $wpdb;

    // Log for debugging
    error_log('Delete carousel action triggered at ' . date('Y-m-d H:i:s'));

    // Verify nonce
    if (!isset($_POST['ccp3o1_nonce']) || !wp_verify_nonce($_POST['ccp3o1_nonce'], 'ccp3o1_delete_carousel_nonce')) {
        error_log('Nonce verification failed for carousel deletion');
        add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Security check failed. Please try again.', 'error');
        wp_redirect(admin_url('admin.php?page=ccp3o1-custom-carousel'));
        exit;
    }

    // Check permissions and carousel ID
    if (isset($_POST['carousel_id']) && current_user_can('manage_options')) {
        $carousel_id = sanitize_text_field($_POST['carousel_id']);
        $raw_option = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'ccp3o1_carousels'");
        error_log('Before deletion - Raw ccp3o1_carousels: ' . $raw_option);
        $carousels = get_option('ccp3o1_carousels', []);
        error_log('Before deletion - Parsed carousels: ' . print_r($carousels, true));

        if (is_array($carousels) && isset($carousels[$carousel_id])) {
            unset($carousels[$carousel_id]);

            // Clear transients and caches
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ccp3o1_%' OR option_name LIKE '_transient_timeout_ccp3o1_%'");
            wp_cache_flush(); // Clear object cache

            // Direct database update
            $serialized_carousels = maybe_serialize($carousels);
            $update_result = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s",
                    $serialized_carousels,
                    'ccp3o1_carousels'
                )
            );
            error_log('Update query executed: ' . $wpdb->last_query);
            error_log('Update result: ' . ($update_result !== false ? 'Success' : 'Failed'));

            // Verify update
            $updated_raw = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'ccp3o1_carousels'");
            $updated_carousels = maybe_unserialize($updated_raw);
            error_log('After deletion - Raw ccp3o1_carousels: ' . $updated_raw);
            error_log('After deletion - Parsed carousels: ' . print_r($updated_carousels, true));

            if ($update_result !== false && !isset($updated_carousels[$carousel_id])) {
                error_log('Carousel ' . $carousel_id . ' deleted successfully');
            } else {
                error_log('Failed to update ccp3o1_carousels in database');
                add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Failed to delete carousel due to database error. Try resetting carousels.', 'error');
            }
        } else {
            error_log('Carousel ID ' . $carousel_id . ' not found in ccp3o1_carousels');
            add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Carousel not found.', 'error');
        }
    } else {
        error_log('Invalid request or insufficient permissions for carousel deletion');
        add_settings_error('ccp3o1_messages', 'ccp3o1_error', 'Invalid request or insufficient permissions.', 'error');
    }

    // Redirect with timestamp to prevent caching
    wp_redirect(admin_url('admin.php?page=ccp3o1-custom-carousel&ccp3o1_action=delete&t=' . time()));
    exit;
}
add_action('admin_post_ccp3o1_delete_carousel', 'ccp3o1_delete_carousel');

// Shortcode
function ccp3o1_carousel_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'ccp3o1_custom_carousel');
    $carousels = get_option('ccp3o1_carousels', []);
    if (empty($atts['id']) || !isset($carousels[$atts['id']])) {
        return '<p>Invalid or missing carousel ID.</p>';
    }

    $carousel = $carousels[$atts['id']];
    $smooth = !empty($carousel['smooth']) ? 1 : 0;
    $output = '<div class="ccp3o1-carousel" data-visible="' . esc_attr($carousel['visible_items']) . '" data-speed="' . esc_attr($carousel['speed']) . '" data-gap="' . esc_attr($carousel['gap']) . '" data-smooth="' . esc_attr($smooth) . '" style="background-color:' . esc_attr($carousel['gap_color']) . ';">';

    foreach ($carousel['items'] as $item_id) {
        $url = wp_get_attachment_url($item_id);
        if (!$url) continue;
        $type = wp_check_filetype($url);
        $dimensions = explode('x', $carousel['dimensions']);
        $width = !empty($dimensions[0]) ? $dimensions[0] : '500';
        $height = !empty($dimensions[1]) ? $dimensions[1] : '800';

        if (strpos($type['type'], 'image') !== false) {
            $output .= '<div><img src="' . esc_url($url) . '" style="width:' . esc_attr($width) . 'px; height:' . esc_attr($height) . 'px; object-fit:cover;"></div>';
        } elseif (strpos($type['type'], 'video') !== false) {
            $output .= '<div><video src="' . esc_url($url) . '" style="width:' . esc_attr($width) . 'px; height:' . esc_attr($height) . 'px;" controls></video></div>';
        }
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('ccp3o1_custom_carousel', 'ccp3o1_carousel_shortcode');

// Handle update (edit) of existing carousel
function ccp3o1_update_carousel() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Insufficient permissions', 'custom-carousel-plugin'));
    }

    if (!isset($_POST['ccp3o1_update_nonce']) || !wp_verify_nonce($_POST['ccp3o1_update_nonce'], 'ccp3o1_update_carousel_nonce')) {
        wp_die(__('Security check failed', 'custom-carousel-plugin'));
    }

    $carousel_id = isset($_POST['carousel_id']) ? sanitize_text_field(wp_unslash($_POST['carousel_id'])) : '';
    $edit_raw = isset($_POST['ccp3o1_edit_carousel']) ? wp_unslash($_POST['ccp3o1_edit_carousel']) : [];
    $edit = is_array($edit_raw) ? $edit_raw : [];

    $carousels = get_option('ccp3o1_carousels', []);
    if (!is_array($carousels) || !isset($carousels[$carousel_id])) {
        wp_redirect(admin_url('admin.php?page=ccp3o1-custom-carousel'));
        exit;
    }

    // Sanitize fields similar to creation
    $items = [];
    if (!empty($edit['items']) && is_string($edit['items'])) {
        $item_ids = array_filter(array_map('absint', explode(',', $edit['items'])));
        foreach ($item_ids as $id) {
            if (wp_get_attachment_url($id)) {
                $items[] = $id;
            }
        }
    }
    $dimensions = isset($edit['dimensions']) ? sanitize_text_field($edit['dimensions']) : '500x800';
    if (!preg_match('/^\d+x\d+$/', $dimensions)) {
        $dimensions = '500x800';
    }
    $visible_items = isset($edit['visible_items']) ? absint($edit['visible_items']) : 3;
    $speed = isset($edit['speed']) ? absint($edit['speed']) : 300;
    $gap = isset($edit['gap']) ? absint($edit['gap']) : 10;
    $gap_color_input = isset($edit['gap_color']) ? sanitize_hex_color($edit['gap_color']) : '';
    $gap_color = $gap_color_input ? $gap_color_input : '#ffffff';
    $smooth = !empty($edit['smooth']) ? 1 : 0;
    if ($smooth && $speed < 600) {
        // Auto-adjust for smooth transitions if too low
        $speed = 800;
    }

    // Update only provided values; keep existing items if none were provided
    if (!empty($items)) {
        $carousels[$carousel_id]['items'] = $items;
    }
    $carousels[$carousel_id]['dimensions'] = $dimensions;
    $carousels[$carousel_id]['visible_items'] = $visible_items;
    $carousels[$carousel_id]['speed'] = $speed;
    $carousels[$carousel_id]['gap'] = $gap;
    $carousels[$carousel_id]['gap_color'] = $gap_color;
    $carousels[$carousel_id]['smooth'] = $smooth ? 1 : 0;

    update_option('ccp3o1_carousels', $carousels);
    // Ensure fresh read even with persistent object cache
    if (function_exists('wp_cache_delete')) {
        wp_cache_delete('ccp3o1_carousels', 'options');
        wp_cache_delete('alloptions', 'options');
    }

    wp_redirect(admin_url('admin.php?page=ccp3o1-custom-carousel&ccp3o1_action=updated&t=' . time()));
    exit;
}
add_action('admin_post_ccp3o1_update_carousel', 'ccp3o1_update_carousel');
?>
