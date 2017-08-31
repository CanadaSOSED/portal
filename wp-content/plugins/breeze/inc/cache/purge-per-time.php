<?php
/**
 *  @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  Original development of this plugin by JoomUnited https://www.joomunited.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined('ABSPATH') || die('No direct script access allowed!');

class Breeze_PurgeCacheTime {
    protected $timettl = false;
    protected $normalcache = 0;
    protected $varnishcache = 0;
    public function __construct($settings = null)
    {
        $this->timettl = $settings['breeze-ttl'];

        $this->normalcache = (int)$settings['breeze-active'];

        $this->varnishcache = (int)$settings['breeze-varnish-purge'];

        add_action( 'breeze_purge_cache', array( $this, 'schedule_varnish' ) );
        add_action( 'init', array( $this, 'schedule_events' ) );
        add_filter( 'cron_schedules', array( $this, 'filter_cron_schedules' ) );

    }
    //     * Unschedule events
    public function unschedule_events() {
        $timestamp = wp_next_scheduled( 'breeze_purge_cache' );

        wp_unschedule_event( $timestamp, 'breeze_purge_cache' );
    }
    //       set up schedule_events
    public function schedule_events() {

        $timestamp = wp_next_scheduled( 'breeze_purge_cache' );

        // Expire cache never
        if ( isset( $this->timettl ) && (int)$this->timettl === 0 ) {
            wp_unschedule_event( $timestamp, 'breeze_purge_cache' );
            return;
        }

        if ( ! $timestamp ) {
            wp_schedule_event( time(), 'breeze_varnish_time', 'breeze_purge_cache' );
        }
    }

    /**
     * Add custom cron schedule
     */
    public function filter_cron_schedules( $schedules ) {
        if ( !empty( $this->timettl ) && is_numeric($this->timettl) && (int)$this->timettl > 0 ) {
            $interval = $this->timettl * 60;
        } else {
            $interval = '86400'; // One day
        }

        $schedules['breeze_varnish_time'] = array(
            'interval' => apply_filters( 'breeze_varnish_purge_interval', $interval ),
            'display'  => esc_html__( 'Cloudways Varnish Purge Interval', 'breeze' ),
        );

        return $schedules;
    }

    //execute purge varnish after time life
    public function schedule_varnish(){
        // Purge varnish cache
        if($this->varnishcache){
            $homepage = home_url().'/?breeze';
            $main = new Breeze_PurgeVarnish();
            $main->purge_cache($homepage);
        }
        // Purge normal cache
        if($this->normalcache){
            Breeze_PurgeCache::breeze_cache_flush();
        }

    }

    public static function factory() {
        static $instance;
        if ( ! $instance ) {
            $instance = new self();
        }
        return $instance;
    }
}

$basic = get_option('breeze_basic_settings');
$varnish = get_option('breeze_varnish_cache');
//Enabled auto purge the varnish caching by time life
$params = array(
    'breeze-active' => (isset($basic['breeze-active'])?(int)$basic['breeze-active']:0),
    'breeze-ttl' => (isset($basic['breeze-ttl'])?(int)$basic['breeze-ttl']:0),
    'breeze-varnish-purge' => (isset($varnish['auto-purge-varnish'])?(int)$varnish['auto-purge-varnish']:0),
);

if($params['breeze-active'] || $params['breeze-varnish-purge']){
    $purgeTime = new Breeze_PurgeCacheTime($params);
}

