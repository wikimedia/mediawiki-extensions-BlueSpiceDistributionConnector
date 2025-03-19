<?php

namespace BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate;

use BlueSpice\DistributionConnector\ConfigDefinitionMigrate\PluggableAuthMigrator;
use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\MediaWikiServices;

require_once dirname( __DIR__, 5 ) . "/maintenance/Maintenance.php";

/**
 * Migrates "SimpleSAMLphp" and "OpenIDConnect" configs from "PluggableAuth 5" to "PluggableAuth 6"
 * With "PluggableAuth 5" there plugins used separate globals for all configs.
 *
 * From now on with "PluggableAuth 6" all authentication plugins configs are stored like that.
 * <code>
 * $wgPluggableAuth_Config['Log in using SAML'] = [
 *        'plugin' => 'SimpleSAMLphp",
 *        'data' => [
 *            'authSourceId' => '...',
 *            'emailAttribute' => '...',
 *            ...
 *        ]
 * ]
 * </code>
 *
 * This maintenance script handles specifically migration of "SimpleSAMLphp" and "OpenIDConnect" plugins configuration.
 */
class MigratePluggableAuthConfig extends LoggedUpdateMaintenance {

	/**
	 * @var array[]
	 */
	private $configMigrationMap = [

		'SimpleSAMLphp' => [
			'DistributionConnectorSimpleSAMLphpEmailAttribute' => 'emailAttribute',
			'DistributionConnectorSimpleSAMLphpGroupAttributeDelimiter' => 'groupAttributeDelimiter',
			'DistributionConnectorSimpleSAMLphpRealNameAttribute' => 'realNameAttribute',
			'DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName' => 'groupAttributeName',
			'DistributionConnectorSimpleSAMLphpUsernameAttribute' => 'usernameAttribute'
		],

		'OpenIDConnect' => [
			'DistributionConnectorOpenIDConnectForceLogout' => 'forceLogout',
			'DistributionConnectorOpenIDConnectMigrateUsersByEmail' => 'migrateUsersByEmail',
			'DistributionConnectorOpenIDConnectMigrateUsersByUserName' => 'migrateUsersByUserName',
			'DistributionConnectorOpenIDConnectUseEmailNameAsUserName' => 'useEmailNameAsUserName',
			'DistributionConnectorOpenIDConnectUseRealNameAsUserName' => 'useRealNameAsUserName',
		]
	];

	/**
	 * Key for old "OpenIDConnect" complex config, containing necessary providers connection information.
	 * It is processed separately because it needs another logic.
	 *
	 * @var string
	 */
	private $openIdConnectConfigKey = 'DistributionConnectorOpenIDConnectConfig';

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

		[ $currentPluggableAuthConfig, $configExists ] = $this->getCurrentPluggableAuthConfig();

		// If some keys already exist in "$pluggableAuthConfig", they'll be silently overridden
		// It should be okay since that script will be executed just after update
		// So "$pluggableAuthConfig" will be filled instantly after it appears in "Config Manager"
		$migrator = new PluggableAuthMigrator( $this->db );
		$newPluggableAuthConfig = $migrator->migrateConfigs( $currentPluggableAuthConfig );

		$this->addNewConfigToDb( $configExists, $newPluggableAuthConfig );

		$this->cleanUpOldConfigs();

		$this->output( "\nMigration done!" );

		return true;
	}

	/**
	 * Gets current "PluggableAuth_Config" value from DB if it exists.
	 *
	 * @return array
	 */
	private function getCurrentPluggableAuthConfig(): array {
		$configExists = false;

		$pluggableAuthConfig = [];

		$currentPluggableAuthConfig = $this->db->selectField(
			$this->configTable,
			's_value',
			"s_name = '{$this->pluggableAuthConfigKey}'",
			__METHOD__
		);
		if ( $currentPluggableAuthConfig ) {
			$configExists = true;

			$pluggableAuthConfig = json_decode( $currentPluggableAuthConfig, true );

			$this->output( "Got current value of '{$this->pluggableAuthConfigKey} config'...\n" );
		}

		return [ $pluggableAuthConfig, $configExists ];
	}

	/**
	 * @param bool $configExists
	 * @param array $pluggableAuthConfig
	 *
	 * @return void
	 */
	private function addNewConfigToDb( bool $configExists, array $pluggableAuthConfig ): void {
		$this->output( "\n" );

		$newConfigValue = json_encode( $pluggableAuthConfig );

		if ( $configExists ) {
			$this->db->update(
				$this->configTable,
				[ 's_value' => $newConfigValue ],
				"s_name = '{$this->pluggableAuthConfigKey}'",
				__METHOD__
			);

			$this->output( "Config '{$this->pluggableAuthConfigKey}' updated.\n" );
		} else {
			$this->db->insert(
				$this->configTable,
				[
					's_name' => $this->pluggableAuthConfigKey,
					's_value' => $newConfigValue
				],
				__METHOD__
			);

			$this->output( "Config '{$this->pluggableAuthConfigKey}' added.\n" );
		}
	}

	/**
	 * Delete old "SimpleSAMLphp" and "OpenIDConnect" configs from DB, they are not used anymore.
	 * They are now stored in "$wgPluggableAuth_Config"
	 *
	 * @return void
	 */
	private function cleanUpOldConfigs(): void {
		$this->output( "Cleaning up old configs...\n" );

		$configsToDelete = [];

		foreach ( $this->configMigrationMap as $configs ) {
			foreach ( $configs as $configName => $pluginDataKey ) {
				$configsToDelete[] = $configName;
			}
		}

		$configsToDelete[] = $this->openIdConnectConfigKey;

		$this->db->delete(
			$this->configTable,
			[ 's_name' => $configsToDelete ],
			__METHOD__
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateKey() {
		return 'bs_migrate_pluggable_auth_config';
	}
}

$maintClass = MigratePluggableAuthConfig::class;
require_once RUN_MAINTENANCE_IF_MAIN;
