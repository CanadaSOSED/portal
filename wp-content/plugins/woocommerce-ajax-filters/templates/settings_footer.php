<div class="wrap">
    <?php if( ! empty($feature_list) && count($feature_list) > 0 ) { ?>
        <div class="paid_features">
            <?php
            $feature_text = '';
            foreach($feature_list as $feature) {
                $feature_text .= '<li>'.$feature.'</li>';
            }
            $text = '<h3>Receive more features and control with Paid version of the plugin:</h3>
            <div>
            <ul>
                %feature_list%
            </ul>
            </div>
            <div><a class="get_premium_version" href="%link%">PREMIUM VERSION</a></div>
            <p>Support the plugin by purchasing paid version. This will provide faster growth, better support and much more functionality for the plugin</p>';
            $text = str_replace('%feature_list%', $feature_text, $text);
            $text = str_replace('%link%', $dplugin_link, $text);
            $text = str_replace('%plugin_name%', @ $plugin_info['Name'], $text);
            $text = str_replace('%plugin_link%', @ $plugin_info['PluginURI'], $text);
            echo $text;
            ?>
        </div>
        <?php
        $text = '<h4>Both <a href="%plugin_link%" target="_blank">Free</a> and <a href="%link%" target="_blank">Paid</a> versions of %plugin_name% developed by <a href="http://berocket.com" target="_blank">BeRocket</a></h4>';
    } else {
        $text = '<h4><a href="%plugin_link%" target="_blank">%plugin_name%</a> developed by <a href="http://berocket.com" target="_blank">BeRocket</a></h4>';
    }
    $text = str_replace('%link%', $dplugin_link, $text);
    $text = str_replace('%plugin_name%', @ $plugin_info['Name'], $text);
    $text = str_replace('%plugin_link%', @ $plugin_info['PluginURI'], $text);
    echo $text;
    ?>
</div>
<style>
.paid_features {
    border: 1px solid #c29a9a;
    background: white;
    padding: 20px 20px 10px 30px;
    font-weight: 600;
}
.get_premium_version {
    display: inline-block;
    background-color: rgb(239, 109, 109);
    border-color: rgb(222, 72, 72);
    color: white;
    font-size: 20px;
    height: auto;
    padding: 10px 41px;
    margin: 1em 0 1em 0;
    text-decoration: none;
    cursor: pointer;
}
.get_premium_version:hover {
    color: white;
    background-color: rgb(222, 72, 72);
}
.paid_features ul li{
    list-style: initial;
    margin-left: 2em;
}
</style>