<?php
$start_time        = 1490705082;
$end_time          = 1496275200;
$promo_price       = '$7.00';
$discount          = '70%';
$save_amount       = '$15.00';
$promo_plugin_name = 'WooCommerce Min/Max Quantity';
$promo_plugin_link = 'http://berocket.com/product/woocommerce-minmax-quantity';

if ( time() > $start_time && time() < $end_time ) { ?>
    <div class="discount-block-check"></div>
    <div class="wrap discount-block">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>../images/70p_sale.jpg" />
        <div>
            <?php
            $text = 'Only <strong>%price%</strong> for <strong>Premium</strong> %name% plugin!<br>
        <span>Get your <strong class="red">%disc% discount</strong> and save <strong>%amount%</strong> today</span>
        <a class="buy_button" href="%link%" target="_blank">Buy Now</a>';
            $text = str_replace('%name%', $promo_plugin_name, $text);
            $text = str_replace('%link%', $promo_plugin_link, $text);
            $text = str_replace('%price%', $promo_price, $text);
            $text = str_replace('%disc%', $discount, $text);
            $text = str_replace('%amount%', $save_amount, $text);
            echo $text;
            ?>
        </div>
    </div>
    <script>
        jQuery(window).scroll(function() {
            var top = jQuery('.discount-block-check').offset().top - 32;

            if( jQuery(window).width() <= 782 ) {
                jQuery('.discount-block').removeClass('fixed');
                jQuery('.discount-block-check').height(0);
            } else {
                if( jQuery(window).scrollTop() > top ) {
                    if( ! jQuery('.discount-block').is('.fixed') ) {
                        jQuery('.discount-block-check').height(jQuery('.discount-block').outerHeight(true));
                        jQuery('.discount-block').addClass('fixed');
                    }
                } else {
                    if( jQuery('.discount-block').is('.fixed') ) {
                        jQuery('.discount-block-check').height(0);
                        jQuery('.discount-block').removeClass('fixed');
                    }
                }
            }
        });
    </script>
    <style>
        .discount-block-check {
            margin: 0!important;
            padding: 0!important;
        }
        .discount-block{
            text-align: center;
            background: #ffffff;
            padding: 0;
            font-size: 24px;
            border: 2px solid #ef3542;
            line-height: 1.8em;
            z-index: 1000;
        }
        .discount-block > img{
            float: left;
            max-height: 130px;
        }
        .discount-block > div{
            padding: 20px;
            height: 90px;
        }
        .discount-block.fixed{
            position: fixed;
            top: 32px;
            left: 180px;
            right: 0px;
        }
        .discount-block .buy_button {
            font-size: 20px;
            padding: 8px 30px;
            color: #fff;
            line-height: 28px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            background-color: #ef3542;
            cursor: pointer;
        }
        .discount-block .buy_button:hover {
            background-color: #F54C57;
        }
        .red {
            color: #ef3542;
        }
        @media screen and (max-width: 1200px) {
            .discount-block{
                font-size: 20px;
            }
        }
        @media screen and (max-width: 1100px) {
            .discount-block{
                font-size: 18px;
                line-height: 1.5em;
            }
        }
        @media screen and (max-width: 960px) {
            .discount-block.fixed{
                left: 56px;
            }
        }
        @media screen and (max-width: 782px) {
            .discount-block.fixed{
                left: 10px;
            }
        }
        @media screen and (max-width: 767px) {
            .discount-block{
                font-size: 16px;
                line-height: 1.6em;
            }
            .discount-block > img{
                max-height: 110px;
            }
            .discount-block > div{
                padding: 10px;
                height: 90px;
            }
            .discount-block .buy_button{
                padding: 4px 15px;
                margin-top: 2px;
            }
        }
        @media screen and (max-width: 610px) {
            .discount-block{
                font-size: 14px;
                line-height: 1.6em;
            }
        }
        @media screen and (max-width: 570px) {
            .discount-block > div > span{
                display: none;
            }
        }
        @media screen and (max-width: 400px) {
            .discount-block .buy_button{
                font-size: 16px;
                padding: 2px 15px;
            }
        }
    </style>
<?php } ?>