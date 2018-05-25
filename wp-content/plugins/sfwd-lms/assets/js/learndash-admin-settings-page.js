jQuery(document).ready(function() {
	jQuery('.learndash-settings-page-wrap a.learndash-description-link').toggle( function( e ) {
		//jQuery(e.currentTarget).parent().find('span.learndash-description').slideDown();
		jQuery(e.currentTarget).parent().find('span.learndash-description').show();
	}, function( e ) {
		//jQuery(e.currentTarget).parent().find('span.learndash-description').slideUp();
		jQuery(e.currentTarget).parent().find('span.learndash-description').hide();
	});
});
