<?php

namespace BlueSpice\DistributionConnector\ConfigDefinitionMigrate;

use Wikimedia\Rdbms\IDatabase;

class PluggableAuthMigrator {

	/**
	 * @var IDatabase
	 */
	private $db;

	/**
	 * DB table where config values are stored.
	 *
	 * @var string
	 */
	private $configTable = 'bs_settings3';

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
	 * @var string
	 */
	private $simpleSamlPhpButtonLabel = 'Log in using SAML';

	/**
	 * Map of "PluggableAuth 5" with "PluggableAuth 6" configuration.
	 *
	 * @var array
	 */
	private $openIdConnectConfigMap = [
		'clientID' => 'clientID',
		'clientsecret' => 'clientsecret',
		'name' => null,
		'icon' => null,
		'proxy' => 'proxy',
		'scope' => 'scope',
		'preferred_username' => 'preferred_username',
		'authparam' => 'authparam',
		'verifyHost' => 'verifyHost',
		'verifyPeer' => 'verifyPeer'
	];

	/**
	 * Key for old "OpenIDConnect" complex config, containing necessary providers connection information.
	 * It is processed separately because it needs another logic.
	 *
	 * @var string
	 */
	private $openIdConnectConfigKey = 'DistributionConnectorOpenIDConnectConfig';

	/**
	 * @var string
	 */
	private $openIdConnectDefaultButtonLabel = 'Log in using OpenIDConnect';

	/**
	 * @param IDatabase $db
	 */
	public function __construct( IDatabase $db ) {
		$this->db = $db;
	}

	/**
	 * @param array $currentPluggableAuthConfig
	 *
	 * @return array
	 */
	public function migrateConfigs( array $currentPluggableAuthConfig = [] ): array {
		$pluggableAuthConfig = $currentPluggableAuthConfig;

		[ $simpleSamlPhpData, $simpleSamlPhpGroupSync ] = $this->makeSimpleSamlPhpData();
		if ( $simpleSamlPhpData ) {
			$pluggableAuthConfig[$this->simpleSamlPhpButtonLabel] = [
				'plugin' => 'SimpleSAMLphp',
				'data' => $simpleSamlPhpData
			];

			if ( $simpleSamlPhpGroupSync ) {
				// In this script we just migrate old "SimpleSAMLphp" and "OpenIDConnect" configs
				// In old "SimpleSAMLphp" configs only "syncall" can be configured, see":
				// \BlueSpice\DistributionConnector\ConfigDefinitionMigrate\PluggableAuthMigrator::$configMigrationMap
				// Specifically "DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName"

				// So there will always be only one entry in "groupsyncs" array in that case
				$pluggableAuthConfig[$this->simpleSamlPhpButtonLabel]['groupsyncs'] = [ $simpleSamlPhpGroupSync ];
			}
		}

		// "OpenIDConnect" may add multiple login buttons, one per provider
		$openIdConnectProviders = $this->makeOpenIdConnectData();
		foreach ( $openIdConnectProviders as $buttonLabel => $pluginData ) {
			$pluggableAuthConfig[$buttonLabel] = [
				'plugin' => 'OpenIDConnect',
				'data' => $pluginData
			];
		}

		return $pluggableAuthConfig;
	}

	/**
	 * @param string $pluginName
	 *
	 * @return array
	 */
	private function makePluginDataFromConfigs( string $pluginName ): array {
		$pluginData = [];

		foreach ( $this->configMigrationMap[$pluginName] as $configName => $pluginDataKey ) {
			$currentConfigValue = $this->db->selectField(
				$this->configTable,
				's_value',
				"s_name = '$configName'",
				__METHOD__
			);

			if ( $currentConfigValue ) {
				$pluginData[$pluginDataKey] = $currentConfigValue;
			}
		}

		return $pluginData;
	}

	/**
	 * @return array Two arrays which should be added to "data" key and "groupsync" correspondingly
	 * 		in "$wgPluggableAuth_Config" for "SimpleSAMLPphp"
	 */
	private function makeSimpleSamlPhpData(): array {
		$pluginData = $this->makePluginDataFromConfigs( 'SimpleSAMLphp' );

		if ( isset( $GLOBALS['wgSimpleSAMLphp_AuthSourceId'] ) ) {
			// We need to manually add 'authSourceId' data key directly from the global
			$pluginData['authSourceId'] = $GLOBALS['wgSimpleSAMLphp_AuthSourceId'];
		}

		// In PluggableAuth 7.0 there is separate sub-array for groups syncs
		$groupSync = [];

		if ( !empty( $pluginData['groupAttributeName'] ) ) {
			$groupSync['groupAttributeName'] = $pluginData['groupAttributeName'];

			// Set "group attribute delimiter" only if "group attribute name" is set
			if ( !empty( $pluginData['groupAttributeDelimiter'] ) ) {
				$groupSync['groupAttributeDelimiter'] = $pluginData['groupAttributeDelimiter'];
			}
		}

		unset( $pluginData['groupAttributeName'] );
		unset( $pluginData['groupAttributeDelimiter'] );

		if ( $groupSync ) {
			$groupSync['type'] = 'syncall';
		}

		return [ $pluginData, $groupSync ];
	}

	/**
	 * @return array Array where key is login button label and value is plugin data array
	 *        There could be multiple "OpenIDConnect" providers and therefore multiple login buttons
	 */
	private function makeOpenIdConnectData(): array {
		$currentConfigRaw = $this->db->selectField(
			$this->configTable,
			's_value',
			"s_name = '{$this->openIdConnectConfigKey}'",
			__METHOD__
		);

		// There could be not config
		if ( !$currentConfigRaw ) {
			return [];
		}

		$currentOpenIdConnectConfig = json_decode( $currentConfigRaw, true );

		// Counter for case when we have multiple providers without specified name
		// Like "Log in using OpenIDConnect (1)", "Log in using OpenIDConnect (2)" and so on.
		$i = 1;

		// Plugin configs which are used among ALL providers
		$globalProviderData = $this->makePluginDataFromConfigs( 'OpenIDConnect' );

		$providers = [];

		foreach ( $currentOpenIdConnectConfig as $providerUrl => $config ) {
			$pluginData = $globalProviderData;

			$pluginData['providerURL'] = $providerUrl;

			foreach ( $config as $configKey => $configValue ) {
				$dataKey = $this->openIdConnectConfigMap[$configKey];
				// Some configs are not needed anymore, skip them
				if ( $dataKey === null ) {
					continue;
				}

				$pluginData[$dataKey] = $configValue;
			}

			if ( empty( $config['name'] ) ) {
				// Provider name is used as login button label
				// If none provided - use default one
				$name = "{$this->openIdConnectDefaultButtonLabel} ($i)";
				$i++;
			} else {
				$name = $config['name'];
			}

			$providers[$name] = $pluginData;
		}

		return $providers;
	}
}
