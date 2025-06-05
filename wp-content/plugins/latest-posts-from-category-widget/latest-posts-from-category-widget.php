<?php
/**
 * Plugin Name: Latest Posts from Category Widget
 * Description: A custom widget to display the latest posts from a specific category.
 * Version: 1.0
 * Author: Ramesh B
 */

class Latest_Posts_From_Category_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'latest_posts_from_category_widget',
            __('Latest Posts from Category', 'text_domain'),
            ['description' => __('Displays latest posts from a selected category.', 'text_domain')]
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $num_posts = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
        $category_id = !empty($instance['category']) ? absint($instance['category']) : 0;

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if ($category_id) {
            $query_args = [
                'cat' => $category_id,
                'posts_per_page' => $num_posts,
                'post_status' => 'publish',
            ];
            $recent_posts = new WP_Query($query_args);

            if ($recent_posts->have_posts()) {
                echo '<ul>';
                while ($recent_posts->have_posts()) {
                    $recent_posts->the_post();
                    echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p>' . __('No posts found in this category.', 'text_domain') . '</p>';
            }
        } else {
            echo '<p>' . __('Please select a category in the widget settings.', 'text_domain') . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Posts from Category', 'text_domain');
        $num_posts = !empty($instance['num_posts']) ? absint($instance['num_posts']) : 5;
        $category = !empty($instance['category']) ? absint($instance['category']) : '';
        $categories = get_categories(['hide_empty' => false]);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'text_domain'); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('num_posts')); ?>">
                <?php esc_attr_e('Number of posts to show:', 'text_domain'); ?>
            </label>
            <input class="tiny-text"
                   id="<?php echo esc_attr($this->get_field_id('num_posts')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('num_posts')); ?>"
                   type="number" step="1" min="1"
                   value="<?php echo esc_attr($num_posts); ?>"
                   size="3" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>">
                <?php esc_attr_e('Select Category:', 'text_domain'); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr($this->get_field_id('category')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value=""><?php _e('-- Select --', 'text_domain'); ?></option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>"
                        <?php selected($category, $cat->term_id); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['num_posts'] = absint($new_instance['num_posts']);
        $instance['category'] = absint($new_instance['category']);

        return $instance;
    }
}

// Register Widget
function register_latest_posts_from_category_widget() {
    register_widget('Latest_Posts_From_Category_Widget');
}
add_action('widgets_init', 'register_latest_posts_from_category_widget');
