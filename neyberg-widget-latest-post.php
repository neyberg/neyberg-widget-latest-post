<?php
    /**
     * Plugin Name: Neyberg widget: Latest posts
     * Plugin URI: neyberg.com
     * Description: Widget for display latest articles with preview images.
     * Version:  1.0
     * Author: Neyberg
     * Author URI: neyberg.com
     * License:  GPL2
     */

    class Neyberg_Widget_Recent_Posts extends WP_Widget
    {
        public function __construct() {
            $widget_ops = array(
                'classname' => 'neyberg_widget_recent_entries',
                'description' => __( 'Your site&#8217;s most recent Posts.' ),
                'customize_selective_refresh' => true,
            );
            parent::__construct( 'neyberg-recent-posts', __( 'Neyberg widget: Latest posts' ), $widget_ops );
            $this->alt_option_name = 'neyberg_widget_recent_entries';
        }

        /**
         * Outputs the content for the current Recent Posts widget instance.
         *
         */
        public function widget( $args, $instance ) {
            if ( !isset( $args['widget_id'] ) ) {
                $args['widget_id'] = $this->id;
            }

            $title = (!empty( $instance['title'] )) ? $instance['title'] : __( 'Recent Posts' );

            /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
            $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

            $number = (!empty( $instance['number'] )) ? absint( $instance['number'] ) : 5;
            if ( !$number ) {
                $number = 5;
            }
            $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

            /**
             * Filters the arguments for the Recent Posts widget.
             *
             */
            $r = new WP_Query( apply_filters( 'widget_posts_args', array(
                'posts_per_page' => $number,
                'no_found_rows' => true,
                'post_status' => 'publish',
                'ignore_sticky_posts' => true,
            ), $instance ) );

            if ( !$r->have_posts() ) {
                return;
            }
            ?>
            <?php echo $args['before_widget']; ?>
            <?php
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            ?>
            <?php while ( $r->have_posts() ) : $r->the_post(); ?>
            <div <?php post_class( 'card card--post-tiny d-flex align-items-center' ); ?>>
                <div class="media flex-shrink-0">
                    <svg class="image-placeholder" width="128" height="128">
                        <use xlink:href="#image-placeholder"></use>
                    </svg>
                    <a class="media__area" href="<?php the_permalink(); ?>">
                        <?php $featured_img_url = get_the_post_thumbnail_url( get_the_ID(), array( 108, 108 ) );
                        ?>

                        <img class="lazy-sidebar"
                             src="<?php echo get_template_directory_uri(); ?>/images/assets/placeholder.png"
                             data-src="<?php echo $featured_img_url; ?>" alt="<?php echo get_the_title(); ?>">
                    </a>
                </div>
                <div class="card-data">
                    <a class="card__name"
                       href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
                    <?php if ( $show_date ) : ?>
                        <span class="post-date"><?php echo get_the_date( 'd.m.Y' ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
            <?php
            echo $args['after_widget'];
        }

        /**
         * Handles updating the settings for the current Recent Posts widget instance.
         *
         */
        public function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
            $instance['number'] = (int)$new_instance['number'];
            $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool)$new_instance['show_date'] : false;
            return $instance;
        }

        /**
         * Outputs the settings form for the Recent Posts widget.
         *
         */
        public function form( $instance ) {
            $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
            $show_date = isset( $instance['show_date'] ) ? (bool)$instance['show_date'] : false;
            ?>
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo $title; ?>"/></p>

            <p>
                <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
                <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>"
                       name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1"
                       value="<?php echo $number; ?>" size="3"/></p>

            <p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?>
                      id="<?php echo $this->get_field_id( 'show_date' ); ?>"
                      name="<?php echo $this->get_field_name( 'show_date' ); ?>"/>
                <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label>
            </p>
            <?php
        }
    }

    function neyberg_recent_widget_registration() {
        register_widget( 'Neyberg_Widget_Recent_Posts' );
    }

    add_action( 'widgets_init', 'neyberg_recent_widget_registration' );
?>