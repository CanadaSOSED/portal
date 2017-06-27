<?php
if( $layout->has_partial('header') ){
	echo $layout->partial('header');
}
if( $layout->has_partial('menubar') ){
	echo $layout->partial('menubar');
}
if( $layout->has_partial('content') ){
	echo $layout->partial('content');
}
?>