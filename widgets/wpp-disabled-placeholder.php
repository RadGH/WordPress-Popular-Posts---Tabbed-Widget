<?php

class wppTabbedWidget extends WP_Widget
{

	// Register widget with WordPress.
	public function __construct() {
		parent::__construct( 'wppTabbedWidget', // Base ID
			'WPP Tabbed - Disabled', // Name
			array( 'description' => 'Plugin dependencies not enabled. This widget will fill in until it is re-activated..' ) // Args
		);
	}

	// Front-end display of widget.
	public function widget( $widget, $instance ) {
		return;
	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		return $old_instance;
	}


	public function form( $instance ) {
		?>
		<p>
			Dependencies for this plugin are diabled. Please re-activate the dependency to restore this widget.
		</p>
		<?php
	}

} // class wppTabbedWidget