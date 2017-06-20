<div class="wrap">
<?php
$text = '<h1>%plugin_name% by BeRocket</h1>
<div>%desc%</div>';
$text = str_replace('%plugin_name%', $plugin_info['Name'], $text);
$text = str_replace('%desc%', $plugin_info['Description'], $text);
echo $text;
?>
</div>