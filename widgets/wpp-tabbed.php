<?php

class wppTabbedWidget extends WP_Widget
{

	public $number_of_tabs = 3;

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
		$count = (int) $instance['count'];

		$tabs = array();

		for ( $i = 1; $i <= $this->number_of_tabs; $i++ ) {
			if ( empty($instance['tab'. $i .'-type']) ) continue;

			$tabs[$i] = array(
				'title' => $instance['tab'. $i .'-title'],
				'type' => $instance['tab'. $i .'-type'],
			);
		}

		if ( empty($tabs) ) return;

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
				<?php foreach( $tabs as $i => $tab ) { ?>
					<li class="wpptw-tab-item wpptw-tab-<?php echo esc_attr($tab['type']); ?> wpptw-tab-num-<?php echo esc_attr($i); ?> wpptw-active">
						<a href="#popular-<?php echo esc_attr($i); ?>"><?php echo esc_html($tab['title']); ?></a>
					</li>
				<?php } ?>
			</ul>

			<div class="wpptw-content">
				<?php foreach( $tabs as $i => $tab ) { ?>
					<div id="popular-<?php echo esc_attr($i); ?>" class="wpptw-content-item wpptw-content-<?php echo esc_attr($tab['type']); ?> <?php if ( $i == 1 ) echo 'wpptw-active'; ?>">
						<?php $this->wpp_shortcode($tab['type'], $args); ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php

		echo $widget['after_widget'];
	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = (int) $new_instance['count'];

		for ( $i = 1; $i <= $this->number_of_tabs; $i++ ) {
			$instance['tab'. $i .'-title'] = strip_tags( $new_instance['tab'. $i .'-title'] );
			$instance['tab'. $i .'-type'] = strip_tags( $new_instance['tab'. $i .'-type'] );
		}

		return $instance;
	}


	public function form( $instance ) {
		// Retrieve all of our fields from the $instance variable
		$fields = array(
			'title',
			'count',
		);

		for( $i = 1; $i <= $this->number_of_tabs; $i++ ) {
			$fields[] = 'tab'. $i .'-title';
			$fields[] = 'tab'. $i .'-type';
		}

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
			<label for="<?php echo esc_attr( $fields['title']['id'] ); ?>"><strong><?php _e( 'Widget Title:' ); ?></strong></label>
			<input class="widefat" type="text"
			       id="<?php echo esc_attr( $fields['title']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['title']['name'] ); ?>"
			       value="<?php echo esc_attr( $fields['title']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['count']['id'] ); ?>"><strong><?php _e( 'Posts per tab:' ); ?></strong></label> <br>
			<input class="widefat" type="number" style="width: 50px;"
			       id="<?php echo esc_attr( $fields['count']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['count']['name'] ); ?>"
			       value="<?php echo esc_attr( $fields['count']['value'] ); ?>" />
		</p>

		<?php
		for ( $i = 1; $i <= $this->number_of_tabs; $i++ ) {
			?>
			<p>
				<label for="<?php echo esc_attr( $fields['tab'. $i .'-title']['id'] ); ?>"><strong>Tab #<?php echo $i; ?>:</strong></label>
			</p>

			<p>
				<table style="margin-top: -10px;">
					<tbody>
					<tr>
						<td><label for="<?php echo esc_attr( $fields['tab'. $i .'-title']['id'] ); ?>">Title</label></td>
						<td><input class="small" type="text"
						           id="<?php echo esc_attr( $fields['tab'. $i .'-title']['id'] ); ?>"
						           name="<?php echo esc_attr( $fields['tab'. $i .'-title']['name'] ); ?>"
						           value="<?php echo esc_attr( $fields['tab'. $i .'-title']['value'] ); ?>" /></td>
					</tr>
					<tr>
						<td><label for="<?php echo esc_attr( $fields['tab'. $i .'-type']['id'] ); ?>">Range</label></td>
						<td><select class="small"
						            id="<?php echo esc_attr( $fields['tab'. $i .'-type']['id'] ); ?>"
						            name="<?php echo esc_attr( $fields['tab'. $i .'-type']['name'] ); ?>">
								<option value="">&ndash; Disabled &ndash;</option>
								<option value="daily" <?php selected($fields['tab'. $i .'-type']['value'], "daily"); ?>>Daily</option>
								<option value="weekly" <?php selected($fields['tab'. $i .'-type']['value'], "weekly"); ?>>7 Days</option>
								<option value="monthly" <?php selected($fields['tab'. $i .'-type']['value'], "monthly"); ?>>30 Days</option>
								<option value="all" <?php selected($fields['tab'. $i .'-type']['value'], "all"); ?>>All Time</option>
							</select></td>
					</tr>
					</tbody>
				</table>
			</p>
			<?php
		}
	}

} // class wppTabbedWidget