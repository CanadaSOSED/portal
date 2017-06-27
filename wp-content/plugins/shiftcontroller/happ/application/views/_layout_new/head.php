<?php
$ri = $layout->param('ri');

if( defined('NTS_DEVELOPMENT') && NTS_DEVELOPMENT ){
	$assets_dir = NTS_DEVELOPMENT . '/assets';
	$localhost = defined('NTS_LOCALHOST') ? NTS_LOCALHOST : 'localhost';
	$assets_web_dir = 'http://' . $localhost . '/wp/wp-content/plugins/';
}
else {
	$assets_dir = dirname(__FILE__) . '/../../../assets';
	$CI =& ci_get_instance();
	$assets_web_dir = $CI->config->base_url();

	// if intercepted
	$real_ri = HC_Lib::ri();
	switch( $real_ri ){
		case 'wordpress':
			$assets_web_dir = plugins_url('', realpath( $assets_dir . '/..' )) . '/';
			break;
		default:
			break;
	}
}
require( $assets_dir . '/files.php' );

foreach( $more_js as $mf ){
	if( ! in_array($mf, $js_files) ){
		$js_files[] = $mf;
	}
}
foreach( $more_css as $mf ){
	if( ! in_array($mf, $css_files) ){
		$css_files[] = $mf;
	}
}

/* make hc-hitcode.css goes after all */
$final_css_files = array();
$append = array();
foreach( $css_files as $f ){
	$fname = is_array($f) ? $f[0] : $f;
	if( substr($fname, -strlen('/hc-hitcode.css')) == '/hc-hitcode.css' ){
		$append[] = $f;
	}
	else {
		$final_css_files[] = $f;
	}
}
foreach( $append as $f ){
	$final_css_files[] = $f;
}
$css_files = $final_css_files;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo (isset($page_title)) ? $page_title : ''; ?></title>

<?php
$prfx = array('http://', 'https://', '//');
?>
<?php foreach( $css_files as $f ) : ?>
<?php
	$file = is_array($f) ? $f[0] : $f;

	$full = FALSE;
	reset( $prfx );
	foreach( $prfx as $prf ){
		if( substr($file, 0, strlen($prf)) == $prf ){
			$full = TRUE;
			break;
		}
	}
	$file = $full ? $file : $assets_web_dir . $file;
?>
<?php if( is_array($f) ) : ?>
<!--[if <?php echo $f[1]; ?>]>
<link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" />
<![endif]-->
<?php	else : ?>
<link rel="stylesheet" type="text/css" href="<?php echo $file; ?>" />
<?php	endif; ?>
<?php endforeach; ?>

<?php foreach( $js_files as $f ) : ?>
<?php
	$file = is_array($f) ? $f[0] : $f;
	$skip_start = FALSE;
	reset( $prfx );
	foreach( $prfx as $prf ){
		if( substr($file, 0, strlen($prf)) == $prf ){
			$skip_start = TRUE;
			break;
		}
	}
	$file = $skip_start ? $file : $assets_web_dir . $file;
?>
<?php	if( is_array($f) ) : ?>
<!--[if <?php echo $f[1]; ?>]>
<script language="JavaScript" type="text/javascript" src="<?php echo $file; ?>"></script>
<![endif]-->
<?php	else : ?>
<script language="JavaScript" type="text/javascript" src="<?php echo $file; ?>"></script>
<?php	endif; ?>
<?php endforeach; ?>
<?php if( 0 ) : ?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
var hc_vars = {
	"link_prefix_regular":"<?php echo addslashes(HC_Lib::link()); ?>",
	"link_prefix_ajax":"<?php echo addslashes(HC_Lib::link()); ?>",
	};
/* ]]> */
</script>
<?php endif; ?>
<?php
if( $layout->has_partial('theme_head') ){
	echo $layout->partial('theme_head');
}
?>
</head>

<body>