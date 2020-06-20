
var sampleTags = ['c++', 'java', 'php', 'coldfusion', 'javascript', 'asp', 'ruby', 'python', 'c', 'scala', 'groovy', 'haskell', 'perl', 'erlang', 'apl', 'cobol', 'go', 'lua'];

( function( $ ) {

    $( document ).bind( 'gform_post_render', function() {

        /* Temporary fix due to jQuery version issue */
        jQuery.curCSS = jQuery.css;

        $.each( gfcpt_tag_inputs.tag_inputs, function() {
            $( this.input ).tagit( {
                availableTags:      gfcpt_tag_taxonomies[ this.taxonomy ],
                removeConfirmation: true,
                allowSpaces:        true,
                animate:            false
            } );
        } );

    } );

} )( jQuery );

