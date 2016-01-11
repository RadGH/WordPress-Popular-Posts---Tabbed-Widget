jQuery(function() {
	var $wpptw = jQuery('.wpptw');
	if ( $wpptw.length < 1 ) return;

	var $tabs = $wpptw.children('ul.wpptw-tabs').children('li.wpptw-tab-item');

	var $contents = $wpptw.children('div.wpptw-content').children('div.wpptw-content-item');

	$tabs.on('click', 'a', function(e) {
		// Do nothing on current tab
		if ( jQuery(this).is('.wpptw-active') ) return false;

		var $the_tab = jQuery(this).closest('li.wpptw-tab-item');
		var $the_content = jQuery( jQuery(this).attr('href') );

		// Ignore events if link does not have a matching target
		if ( $the_content.length < 1 ) return;

		// Activate the new tab and content
		$tabs.not($the_tab).removeClass('wpptw-active');
		$the_tab.addClass('wpptw-active');

		$contents.not($the_content).removeClass('wpptw-active');
		$the_content.addClass('wpptw-active');

		return false;
	});
});