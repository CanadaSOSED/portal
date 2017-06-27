<?php
if( $layout->has_partial('head') ){
	echo $layout->partial('head');
}
?>
<div id="nts">
<div class="container">
<?php
/* CONTENT */
echo $layout->partial('content');
?>

</div><!-- /container -->
</div><!-- /nts -->

<script language="Javascript1.2">
<!--
window.print();
//-->
</script>

</body>
</html>
<?php
exit;
?>