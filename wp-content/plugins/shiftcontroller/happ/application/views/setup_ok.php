<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( $page_title . ' OK' )
		)
	);

$start_url = HC_Lib::link();
$brand_title = $this->config->item('nts_app_title');
?>
<p>
Thank you for installing <strong><?php echo $brand_title; ?></strong>! Please now proceed to the <a href="<?php echo $start_url; ?>">start page</a>.
</p>

<META http-equiv="refresh" content="5;URL=<?php echo $start_url; ?>">

<?php
$localhost = ($this->input->server('SERVER_NAME') != 'localhost') ? FALSE : TRUE;
$track_setup = $this->config->item('nts_track_setup');
if( $track_setup ){
	list( $track_site_id, $track_goal_id ) = explode( ':', $track_setup );
}
?>
<?php if( $track_setup ) : ?>
<?php if( $localhost ) : ?>
	<?php // echo 'TRACKING ' . $track_site_id . ':' . $track_goal_id; ?>
<?php else : ?>
<br><br><br><br>

<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.greatdealsplaza.com/piwik/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "<?php echo $track_site_id; ?>"]);
	_paq.push(['trackGoal', <?php echo $track_goal_id; ?>]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<?php endif; ?>
<?php endif; ?>