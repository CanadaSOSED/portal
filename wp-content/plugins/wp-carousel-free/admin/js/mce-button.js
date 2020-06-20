(function() {
	tinymce.PluginManager.add('sp_wpcp_mce_button', function( editor, url ) {
		editor.addButton('sp_wpcp_mce_button', {
			text: false,
            icon: false,
			image: url + '/wp-carousel-icon.svg',
            tooltip: 'WP Carousel',
            onclick: function () {
                editor.windowManager.open({
                    title: 'Insert Shortcode',
					width: 400,
					height: 100,
					body: [
						{
							type: 'listbox',
							name: 'listboxName',
                            label: 'Select Carousel',
							'values': editor.settings.spWPCPCarouselList
						}
					],
					onsubmit: function( e ) {
						editor.insertContent( '[sp_wpcarousel id="' + e.data.listboxName + '"]');
					}
				});
			}
		});
	});
})();