<?php

namespace BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate;

use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\MediaWikiServices;

require_once dirname( __DIR__, 5 ) . "/maintenance/Maintenance.php";

class FixPluggableAuthGroupSync extends LoggedUpdateMaintenance {

	/**
	 * Key for new "PluggableAuth" config, which migration is done to
	 *
	 * @var string
	 */
	private $pluggableAuthConfigKey = 'DistributionConnectorPluggableAuthConfig';

	/**
	 * DB table where config values are stored.
	 *
	 * @var string
	 */
	private $configTable = 'bs_settings3';

	/**
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	private $db;

	/**
	 * @inheritDoc
	 */
	public function doDBUpdates() {
		$this->db = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY );

		$pluggableAuthConfig = $this->getPluggableAuthConfig();

		foreach ( $pluggableAuthConfig as $loginButtonLabel => &$entry ) {
			if ( isset( $entry['data']['groupsyncs'] ) ) {
				$this->output( "Group syncs are stored in 'data' subarray, which is incorrect. Fixing...\n" );

				$entry['groupsyncs'] = $entry['data']['groupsyncs'];

				unset( $entry['data']['groupsyncs'] );
			}
		}
		unset( $entry );

		$this->updatePluggableAuthConfig( $pluggableAuthConfig );

		$this->output( "\nPluggableAuth 'groupsyncs' fix done!\n" );

		return true;
	}

	/**
	 * Gets current "PluggableAuth_Config" value from DB if it exists.
	 *
	 * @return array
	 */
	private function getPluggableAuthConfig(): array {
		$pluggableAuthConfig = [];

		$currentPluggableAuthConfig = $this->db->selectField(
			$this->configTable,
			's_value',
			"s_name = '{$this->pluggableAuthConfigKey}'",
			__METHOD__
		);
		if ( $currentPluggableAuthConfig ) {
			$pluggableAuthConfig = json_decode( $currentPluggableAuthConfig, true );

			$this->output( "Got current value of '{$this->pluggableAuthConfigKey} config'...\n" );
		}

		return $pluggableAuthConfig;
	}

	/**
	 * Updates "PluggableAuth_Config" value in DB with corrected one.
	 *
	 * @param array $newConfigValue
	 */
	private function updatePluggableAuthConfig( array $newConfigValue ): void {
		$this->db->update(
			$this->configTable,
			[ 's_value' => json_encode( $newConfigValue ) ],
			"s_name = '{$this->pluggableAuthConfigKey}'",
			__METHOD__
		);

		$this->output( "Config '{$this->pluggableAuthConfigKey}' updated.\n" );
	}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateKey() {
		return 'bs_fix_pluggable_auth_group_sync';
	}
}

$maintClass = FixPluggableAuthGroupSync::class;
require_once RUN_MAINTENANCE_IF_MAIN;
