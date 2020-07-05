<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.wpgens.com
 * @since      1.0.0
 *
 * @package    Fav_Gens
 * @subpackage Fav_Gens/admin/partials
 */

?>
<style>
    /* Table */
    
    .raftable__raftable {
        border-collapse: collapse;
        border-spacing: 0;
        max-width: 100%;
        background-color: #fff;
        width: 100%; 
    }

    .raftable__heading {
        padding: 14px 10px;
        color: #fff;
        font-weight: bold;
        background: #985dc3;
    }

    .raftable__row:hover {
        background: #f7f7f7; 
    }

    .raftable__cell {
        padding: 7px 10px;
        height: 42px;
        border-bottom:1px solid #f3f3f3;
    }

    .raftable__cell span {
        align-items: center;
        background-color: #a495f3;
        border-radius: 4px;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        height: 24px;
        justify-content: center;
        line-height: 1.5;
        padding-left: 9px;
        padding-right: 9px;
        white-space: nowrap;
    }
    .raftable__cell span.email_sent {
        background-color: #f3bd05;
    }
    .raftable__cell span.new_order {
        background-color: #75cc1f;
    }
    .raftable__cell span.coupon_applied {
        background-color: #4da6ff;
    }
    .raftable__cell span.email_share {
        background-color: #e67e22;
    }
    .raftable__cell span.subscription_renewal {
        background-color: #1abc9c;
    }

    .rafpagination { width: 100%; background-color:#985dc3; margin-top:-11px;}
    .rafpagination ul { list-style:none; font-size:12px; padding: 8px; }
    .rafpagination li{ display:inline-block; margin:0; }
    .rafpagination li a{ display:inline-block; padding:4px 9px; margin:0 2px; font-weight:bold; text-decoration:none; color:#fff; }
    .rafpagination li a:hover{ color: #ddd }
    .rafpagination li.current span { display:inline-block; padding:4px 9px; color: #fff; }	    

</style>
<div style="background-color:#985DC4;min-height:120px;margin-left:-20px;">
    <h1 style="color:#fff;margin:0;line-height:120px;margin-left:25px;"><?php _e('Refer a Friend by WPGens','gens-raf'); ?></h1>
</div>
<div style="background-color:#fff;min-height:60px;margin-left:-20px;">
    <h2 style="color:#222;margin:0;line-height:60px;margin-left:25px;"><?php _e('List of all actions made by Refer a Friend plugin','gens-raf'); ?></h2>
</div>
<?php if(isset($_GET['message'])) { ?>
	<div id="message" class="error notice is-dismissible">
		<p><?php echo $_GET['message']; ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
<?php } ?>
<div class="wrap">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="postbox-container-2" class="postbox-container">
                <?php if($formattedLogs) { ?>
                    <table class="raftable__raftable">
                        <thead>
                            <tr class="raftable__row">
                                <td class="raftable__heading">Type</td>
                                <td class="raftable__heading">Date</td>
                                <td class="raftable__heading">Message</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($formattedLogs as $log) { ?>
                            <tr class="raftable__row">
                                <td class="raftable__cell"><span class="<?php echo $log['type']; ?>"><?php echo $log['type_name']; ?></span></td>
                                <td class="raftable__cell"><?php echo $log['date']; ?></td>
                                <td class="raftable__cell"><?php echo $log['info']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <nav class="rafpagination" role="navigation" aria-label="pagination">
                        <ul>
                            <?php if($curpage != $startpage) { ?>
                                <li><a href="<?php echo admin_url( "admin.php?page=".$_GET["page"] ); ?>&lpage=1">First</a></li>
                            <?php } ?>
                            <?php if($curpage > 2) { ?>
                                <li><a href="<?php echo admin_url( "admin.php?page=".$_GET["page"] ); ?>&lpage=<?php echo $previouspage; ?>"><?php echo $previouspage; ?></a></li>
                            <?php } ?>
                            <li class="current"><span><?php echo $curpage ?></span></li>

                            <?php if($curpage != $endpage && $endpage != 0) { ?>
                                <?php if($curpage + 1 != $endpage) { ?>
                                <li><a href="<?php echo admin_url( "admin.php?page=".$_GET["page"] ); ?>&lpage=<?php echo $nextpage; ?>"><?php echo $nextpage ?></a></li>
                                <?php } ?>
                                <li><a href="<?php echo admin_url( "admin.php?page=".$_GET["page"] ); ?>&lpage=<?php echo $endpage; ?>">Last</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                <?php } else { ?>
                If you have recently updated the plugin and this screen does not show your referral orders, go to <a href="<?php echo admin_url( "admin.php?page=wc-status&tab=tools") ?>">this page</a> and click on <strong>generate missing logs</strong> button. Then come back here.
                <?php } ?>
            </div>
        </div>
    </div>
