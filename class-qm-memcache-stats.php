<?php
/**
 * Plugin Name: Query Monitor: Memcache Stats
 * Description: Shows Memcache stats in Query Monitor
 * Version: 0.1
 * Plugin URI: https://github.com/Automattic/qm-memcache-stats
 * Author: Automattic
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Class QM_Collector_Memcache
 */
class QM_Memcache_Stats {
	public function __construct() {
		if ( class_exists( 'QM_Collectors' ) ) {
			global $wp_object_cache;

			// Don't load the plugin if all required objects aren't available.
			if ( ! isset( $wp_object_cache ) || ! isset( $wp_object_cache->stats ) || ! isset( $wp_object_cache->group_ops ) ) {
				return;
			}

			$this->register_collector();
			add_filter( 'qm/outputter/html', array( $this, 'register_output' ), 101, 1 );
		}
	}

	/*
	 * Register collector
	 *
	 * @return void
	 */
	private function register_collector() {
		require_once __DIR__ . '/classes/class-qm-collector-memcache-stats.php';
		QM_Collectors::add( new QM_Collector_Memcache_Stats() );
	}

	/*
	 * Register output
	 *
	 * @param array $output
	 *
	 * @return array
	 */
	public function register_output( array $output ): array {
		require_once __DIR__ . '/classes/class-qm-output-memcache-stats.php';

		$collector = QM_Collectors::get( 'memcache-stats' );
		if ( isset( $collector ) ) {
			$output['memcache'] = new QM_Output_Memcache_Stats( $collector );
		}

		return $output;
	}
}

add_action(
	'plugins_loaded',
	function () {
		new QM_Memcache_Stats();
	},
	10,
	0
);
