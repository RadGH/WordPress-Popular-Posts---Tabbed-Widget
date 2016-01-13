<?php

class wppTabbedWidget extends WP_Widget
{

	// Register widget with WordPress.
	public function __construct() {
		parent::__construct( 'wppTabbedWidget', // Base ID
			'WPP Tabbed Widget', // Name
			array( 'description' => 'Displays three tabs for WordPress Popular Posts.' ) // Args
		);
	}

	public function wpp_shortcode( $range, $custom_args = array() ) {
		$default_args = array(
			'header' => '',
			'limit' => 10,
			'range' => 'daily',
			'freshness' => false,
			'order_by' => 'views',
			'post_type' => 'post,page',
			'pid' => '',
			'cat' => '',
			'author' => '',
			'title_length' => 0,
			'title_by_words' => 0,
			'excerpt_length' => 0,
			'excerpt_format' => 0,
			'excerpt_by_words' => 0,
			'thumbnail_width' => 0,
			'thumbnail_height' => 0,
			'rating' => false,
			'stats_comments' => false,
			'stats_views' => true,
			'stats_author' => false,
			'stats_date' => false,
			'stats_date_format' => 'F j, Y',
			'stats_category' => false,
			'wpp_start' => '<ul class="wpp-list">',
			'wpp_end' => '</ul>',
			'header_start' => '<h2>',
			'header_end' => '</h2>',
			'post_html' => '',
			'php' => false
		);

		$args = shortcode_atts( $default_args, $custom_args );

		$args['range'] = $range;

		$sc = '[wpp ';

		foreach( $args as $k => $v ) {
			if ( $v === $default_args[$k] ) continue;

			$sc .= $k . '=';

			if ( is_numeric($v) && (intval($v) == floatval($v)) ) $sc .= $v;
			else if ( $v === false ) $sc .= 0;
			else if ( $v === true ) $sc .= 1;
			else $sc .= '"'. esc_attr($v) .'"';

			$sc .= " ";
		}

//		$sc = substr($sc, -1);

		$sc .= ']';

		echo do_shortcode($sc);
	}

	// Front-end display of widget.
	public function widget( $widget, $instance ) {
		$title = !empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$tab1_title = $instance['tab1-title'];
		$tab2_title = $instance['tab2-title'];
		$tab3_title = $instance['tab3-title'];
		$count = (int) $instance['count'];

		if ( $count < 1 ) $count = 5;

		$args = array(
			'post_type' => 'post',
			'header' => '',
			'limit' => $count,
			'thumbnail_width' => get_option( 'thumbnail_size_w', 150 ),
			'thumbnail_height' => get_option( 'thumbnail_size_h', 150 ),
			'title_length' => 25,
			'title_by_words' => 1,
			'header_start' => '',
			'header_end' => '',
			'wpp_start' => '<ul class="wpp-list wwptw-list">',
			'wpp_end' => '</ul>',
			'post_html' => '<li>
<a href="{url}" class="wpp-overlay"></a>
<div class="wpp-content">
<div class="wpp-num"></div>
<div class="wpp-stats">{stats}</div>
</div>
<div class="wpp-photo">
<div class="wpp-thumb">{thumb}</div>
<div class="wpp-title">{title}</div>
</div>
</li>',
		);

		echo $widget['before_widget'];

		?>
		<div class="wpptw">
			<?php if ( $title ) echo $widget['before_title'], esc_html( $title ), $widget['after_title']; ?>

			<ul class="wpptw-tabs">
				<li class="wpptw-tab-item wpptw-tab-today wpptw-active">
					<a href="#popular-today"><?php echo $tab1_title; ?></a>
				</li>
				<li class="wpptw-tab-item wpptw-tab-weekly">
					<a href="#popular-weekly"><?php echo $tab2_title; ?></a>
				</li>
				<li class="wpptw-tab-item wpptw-tab-monthly">
					<a href="#popular-monthly"><?php echo $tab3_title; ?></a>
				</li>
			</ul>

			<div class="wpptw-content">
				<div id="popular-today" class="wpptw-content-item wpptw-content-today wpptw-active">
					<?php $this->wpp_shortcode('daily', $args); ?>
				</div>
				<div id="popular-weekly" class="wpptw-content-item wpptw-content-weekly">
					<?php $this->wpp_shortcode('weekly', $args); ?>
				</div>
				<div id="popular-monthly" class="wpptw-content-item wpptw-content-monthly">
					<?php $this->wpp_shortcode('monthly', $args); ?>
				</div>
			</div>
		</div>
		<?php

		echo $widget['after_widget'];
	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['tab1-title'] = strip_tags( $new_instance['tab1-title'] );
		$instance['tab2-title'] = strip_tags( $new_instance['tab2-title'] );
		$instance['tab3-title'] = strip_tags( $new_instance['tab3-title'] );

		$instance['count'] = (int) $new_instance['count'];

		return $instance;
	}


	public function form( $instance ) {
		// Retrieve all of our fields from the $instance variable
		$fields = array(
			'title',

			'tab1-title',
			'tab2-title',
			'tab3-title',

			'count',
		);

		// Format each field's data into an array
		foreach ( $fields as $name ) {
			$fields[$name] = array(
				'id'    => $this->get_field_id( $name ),
				'name'  => $this->get_field_name( $name ),
				'value' => null,
			);

			if ( isset( $instance[$name] ) ) $fields[$name]['value'] = $instance[$name];
		}

		if ( $fields['count']['value'] === null ) $fields['count']['value'] = 5;

		// Display the widget fields
		?>

		<p>
			<label for="<?php echo esc_attr( $fields['title']['id'] ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" type="text"
		       id="<?php echo esc_attr( $fields['title']['id'] ); ?>"
		       name="<?php echo esc_attr( $fields['title']['name'] ); ?>"
		       value="<?php echo esc_attr( $fields['title']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['tab1-title']['id'] ); ?>"><?php _e( 'Tab Titles:' ); ?></label>

			<label for="<?php echo esc_attr( $fields['tab1-title']['id'] ); ?>" class="screen-reader-text">Tab 1 Title</label>
			<input class="widefat" type="text"
		       placeholder="Today"
		       id="<?php echo esc_attr( $fields['tab1-title']['id'] ); ?>"
		       name="<?php echo esc_attr( $fields['tab1-title']['name'] ); ?>"
		       value="<?php echo esc_attr( $fields['tab1-title']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['tab2-title']['id'] ); ?>" class="screen-reader-text">Tab 2 Title</label>
			<input class="widefat" type="text"
		       placeholder="Weekly"
		       id="<?php echo esc_attr( $fields['tab2-title']['id'] ); ?>"
		       name="<?php echo esc_attr( $fields['tab2-title']['name'] ); ?>"
		       value="<?php echo esc_attr( $fields['tab2-title']['value'] ); ?>" /> <br>
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['tab3-title']['id'] ); ?>" class="screen-reader-text">Tab 3 Title</label>
			<input class="widefat" type="text"
		       placeholder="Monthly"
		       id="<?php echo esc_attr( $fields['tab3-title']['id'] ); ?>"
		       name="<?php echo esc_attr( $fields['tab3-title']['name'] ); ?>"
		       value="<?php echo esc_attr( $fields['tab3-title']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['count']['id'] ); ?>"><?php _e( 'Show up to:' ); ?></label> <br>
			<input class="widefat" type="number" style="width: 50px;"
			       id="<?php echo esc_attr( $fields['count']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['count']['name'] ); ?>"
			       value="<?php echo esc_attr( $fields['count']['value'] ); ?>" /> posts
		</p>
		<?php
	}

} // class wppTabbedWidget