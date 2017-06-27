<?php
$ri = $layout->param('ri');
?>
<?php
if( (! $ri) && $layout->has_partial('head') ){
	echo $layout->partial('head');
}
?>
<?php
if( $layout->has_partial('theme_header') ){
	echo $layout->partial('theme_header');
}
?>
<?php if( $ri && ($ri == 'wordpress') ) : ?>
<div id="nts" class="wrap">
<?php else : ?>
<div id="nts">
<?php endif; ?>

<?php if( $ri ) : ?>
<p>&nbsp;</p>
<?php endif; ?>

<div class="hc-no-print">
<?php
if( $layout->has_partial('brand') ){
	echo $layout->partial('brand');
}
if( $layout->has_partial('profile') ){
	echo $layout->partial('profile');
}
if( $layout->has_partial('menu') ){
	echo $layout->partial('menu');
}
if( $layout->has_partial('header') ){
	echo $layout->partial('header');
}
elseif( $layout->has_partial('header_ajax') ){
	echo $layout->partial('header_ajax');
}
?>
</div>
<?php
$flashdata = '';
if( $layout->has_partial('flashdata') ){
	$flashdata = $layout->partial('flashdata');
}

/* CONTENT */
if( $layout->has_partial('sidebar') ){
	$content = HC_Html_Factory::widget('grid')
		->set_scale('sm')
		->set_gutter(2)
		->add_child(
			'content',
			array($flashdata, $layout->partial('content')),
			9
			)
		->add_child(
			'sidebar',
			$layout->partial('sidebar'),
			3
			)
		;

	$content
		->add_child_style('content', 'margin', 'b2')
		->add_child_style('sidebar', 'padding', 'y2')
		// ->add_child_style('sidebar', 'bg-color', 'silver')
		;
	
	echo $content->render();
}
else {
	echo $flashdata;
	echo $layout->partial('content');
}
?>

</div><!-- /nts -->

<?php
if( $layout->has_partial('js_footer') ){
	echo $layout->partial('js_footer');
}
if( $layout->has_partial('theme_footer') ){
	echo $layout->partial('theme_footer');
}

if( defined('NTS_PROFILER') ){
	global $NTS_COUNT_HTML_ELEMENTS;
	echo 'USED ' . $NTS_COUNT_HTML_ELEMENTS . ' NTS HTML ELEMENT OBJECTS<br>';
}
?>

<?php if( ! $ri ) : ?>
</body>
</html>
<?php endif; ?>