<?php

namespace ConfigDefinitionMigrate;

use BlueSpice\DistributionConnector\ConfigDefinitionMigrate\PluggableAuthMigrator;
use PHPUnit\Framework\TestCase;
use Wikimedia\Rdbms\IDatabase;

/**
 * @covers \BlueSpice\DistributionConnector\ConfigDefinitionMigrate\PluggableAuthMigrator
 */
class PluggableAuthMigratorTest extends TestCase {

	/**
	 * DB table where config values are stored.
	 *
	 * @var string
	 */
	private $configTable = 'bs_settings3';

	/**
	 * @return array[]
	 */
	public function provideConfigs(): array {
		$openIdConnectConfig = [
			'http://provider1.url' => [
				'clientID' => 'SomeID',
				'clientsecret' => 'secretsecret',
				'name' => 'ProviderName1',
				'icon' => 'Icon',
				'proxy' => 'SomeProxy',
				'scope' => 'Scope',
				'preferred_username' => 'SamGreatest',
				'authparam' => json_encode( [
					'firstParam',
					'secondParam'
				] ),
				'verifyHost' => true,
				'verifyPeer' => true
			],

			'http://provider2.url' => [
				'clientID' => 'AnotherID',
				'clientsecret' => 'lorem',
				'name' => 'ProviderName2',
				'icon' => 'Icon222',
				'proxy' => 'SomeProxy222',
				'scope' => 'Scope222',
				'preferred_username' => 'Johny',
				'authparam' => json_encode( [
					'param1',
					'param2'
				] ),
				'verifyHost' => false,
				'verifyPeer' => false
			],

			// First provider without name
			'http://provider3.url' => [
				'clientID' => 'AnotherID2',
				'clientsecret' => 'lorem2',
				'name' => '',
				'icon' => 'Icon333',
				'proxy' => 'SomeProxy333',
				'scope' => 'Scope333',
				'preferred_username' => 'Johny2',
				'authparam' => json_encode( [
					'param1',
					'param2'
				] ),
				'verifyHost' => false,
				'verifyPeer' => false,
			],

			// Second provider without name
			'http://provider4.url' => [
				'clientID' => 'AnotherID3',
				'clientsecret' => 'lorem3',
				'name' => '',
				'icon' => 'Icon444',
				'proxy' => 'SomeProxy444',
				'scope' => 'Scope444',
				'preferred_username' => 'Johny3',
				'authparam' => json_encode( [
					'param1',
					'param2'
				] ),
				'verifyHost' => false,
				'verifyPeer' => false,
			]
		];

		return [
			'Complex case with SAML and multiple OpenIDConnect providers' => [
				[
					'DistributionConnectorOpenIDConnectConfig' => json_encode( $openIdConnectConfig ),
					'DistributionConnectorOpenIDConnectUseRealNameAsUserName' => true,
					'DistributionConnectorOpenIDConnectUseEmailNameAsUserName' => true,
					'DistributionConnectorOpenIDConnectMigrateUsersByUserName' => true,
					'DistributionConnectorOpenIDConnectMigrateUsersByEmail' => true,
					'DistributionConnectorOpenIDConnectForceLogout' => true,
					'DistributionConnectorSimpleSAMLphpEmailAttribute' => 'email',
					'DistributionConnectorSimpleSAMLphpGroupAttributeDelimiter' => ',',
					'DistributionConnectorSimpleSAMLphpRealNameAttribute' => 'realname',
					'DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName' => 'ingroup',
					'DistributionConnectorSimpleSAMLphpUsernameAttribute' => 'uid'
				],
				[],
				[
					'Log in using SAML' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email',
							'realNameAttribute' => 'realname',
							'usernameAttribute' => 'uid'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup',
								'groupAttributeDelimiter' => ','
							]
						]
					],

					'ProviderName1' => [
						'plugin' => 'OpenIDConnect',
						'data' => [
							// Provider specific configs
							'providerURL' => 'http://provider1.url',
							'clientID' => 'SomeID',
							'clientsecret' => 'secretsecret',
							'proxy' => 'SomeProxy',
							'scope' => 'Scope',
							'preferred_username' => 'SamGreatest',
							'authparam' => json_encode( [
								'firstParam',
								'secondParam'
							] ),
							'verifyHost' => true,
							'verifyPeer' => true,

							// Global configs for each OpenIDConnect provider
							'forceLogout' => true,
							'migrateUsersByEmail' => true,
							'migrateUsersByUserName' => true,
							'useEmailNameAsUserName' => true,
							'useRealNameAsUserName' => true
						]
					],

					'ProviderName2' => [
						'plugin' => 'OpenIDConnect',
						'data' => [
							// Provider specific configs
							'providerURL' => 'http://provider2.url',
							'clientID' => 'AnotherID',
							'clientsecret' => 'lorem',
							'proxy' => 'SomeProxy222',
							'scope' => 'Scope222',
							'preferred_username' => 'Johny',
							'authparam' => json_encode( [
								'param1',
								'param2'
							] ),
							'verifyHost' => false,
							'verifyPeer' => false,

							// Global configs for each OpenIDConnect provider
							'forceLogout' => true,
							'migrateUsersByEmail' => true,
							'migrateUsersByUserName' => true,
							'useEmailNameAsUserName' => true,
							'useRealNameAsUserName' => true
						]
					],

					'Log in using OpenIDConnect (1)' => [
						'plugin' => 'OpenIDConnect',
						'data' => [
							// Provider specific configs
							'providerURL' => 'http://provider3.url',
							'clientID' => 'AnotherID2',
							'clientsecret' => 'lorem2',
							'proxy' => 'SomeProxy333',
							'scope' => 'Scope333',
							'preferred_username' => 'Johny2',
							'authparam' => json_encode( [
								'param1',
								'param2'
							] ),
							'verifyHost' => false,
							'verifyPeer' => false,

							// Global configs for each OpenIDConnect provider
							'forceLogout' => true,
							'migrateUsersByEmail' => true,
							'migrateUsersByUserName' => true,
							'useEmailNameAsUserName' => true,
							'useRealNameAsUserName' => true
						]
					],

					'Log in using OpenIDConnect (2)' => [
						'plugin' => 'OpenIDConnect',
						'data' => [
							// Provider specific configs
							'providerURL' => 'http://provider4.url',
							'clientID' => 'AnotherID3',
							'clientsecret' => 'lorem3',
							'proxy' => 'SomeProxy444',
							'scope' => 'Scope444',
							'preferred_username' => 'Johny3',
							'authparam' => json_encode( [
								'param1',
								'param2'
							] ),
							'verifyHost' => false,
							'verifyPeer' => false,

							// Global configs for each OpenIDConnect provider
							'forceLogout' => true,
							'migrateUsersByEmail' => true,
							'migrateUsersByUserName' => true,
							'useEmailNameAsUserName' => true,
							'useRealNameAsUserName' => true
						]
					]
				]
			],
			'Empty current config' => [
				// If there is no configs for OpenIDConnect and SimpleSAMLphp - then PluggableAuth config will be empty
				[],
				[],
				[]
			],
			'Current PluggableAuth config not empty (value is not overridden)' => [
				[
					'DistributionConnectorSimpleSAMLphpEmailAttribute' => 'email',
					'DistributionConnectorSimpleSAMLphpGroupAttributeDelimiter' => ',',
					'DistributionConnectorSimpleSAMLphpRealNameAttribute' => 'realname',
					'DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName' => 'ingroup',
					'DistributionConnectorSimpleSAMLphpUsernameAttribute' => 'uid'
				],
				[
					'Log in using SAML 2' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email2',
							'realNameAttribute' => 'realname2',
							'usernameAttribute' => 'uid2'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup2',
								'groupAttributeDelimiter' => ',2'
							]
						]
					]
				],
				[
					'Log in using SAML' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email',
							'realNameAttribute' => 'realname',
							'usernameAttribute' => 'uid'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup',
								'groupAttributeDelimiter' => ','
							]
						]
					],

					'Log in using SAML 2' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email2',
							'realNameAttribute' => 'realname2',
							'usernameAttribute' => 'uid2'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup2',
								'groupAttributeDelimiter' => ',2'
							]
						]
					],
				]
			],
			'Current PluggableAuth config not empty (value is overridden)' => [
				[
					'DistributionConnectorSimpleSAMLphpEmailAttribute' => 'email',
					'DistributionConnectorSimpleSAMLphpGroupAttributeDelimiter' => ',',
					'DistributionConnectorSimpleSAMLphpRealNameAttribute' => 'realname',
					'DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName' => 'ingroup',
					'DistributionConnectorSimpleSAMLphpUsernameAttribute' => 'uid'
				],
				[
					'Log in using SAML' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email2',
							'realNameAttribute' => 'realname2',
							'usernameAttribute' => 'uid2'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup2',
								'groupAttributeDelimiter' => ',2'
							]
						]
					]
				],
				[
					// Here configuration is overridden with migrated one
					'Log in using SAML' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email',
							'realNameAttribute' => 'realname',
							'usernameAttribute' => 'uid'
						],
						'groupsyncs' => [
							[
								'type' => 'syncall',
								'groupAttributeName' => 'ingroup',
								'groupAttributeDelimiter' => ','
							]
						]
					]
				]
			],
			'SAML config with empty GroupsGroupAttributeName' => [
				[
					'DistributionConnectorSimpleSAMLphpEmailAttribute' => 'email',
					'DistributionConnectorSimpleSAMLphpGroupAttributeDelimiter' => ',',
					'DistributionConnectorSimpleSAMLphpRealNameAttribute' => 'realname',
					'DistributionConnectorSimpleSAMLphpSyncAllGroupsGroupAttributeName' => '',
					'DistributionConnectorSimpleSAMLphpUsernameAttribute' => 'uid'
				],
				[],
				[
					// Here configuration is overridden with migrated one
					'Log in using SAML' => [
						'plugin' => 'SimpleSAMLphp',
						'data' => [
							'emailAttribute' => 'email',
							'realNameAttribute' => 'realname',
							'usernameAttribute' => 'uid'
						]
					]
				]
			]
		];
	}

	/**
	 * @dataProvider provideConfigs
	 * @covers \BlueSpice\DistributionConnector\ConfigDefinitionMigrate\PluggableAuthMigrator::migrateConfigs
	 */
	public function testNormal( $currentConfigs, $currentPluggableAuthConfig, $expectedPluggableAuthConfig ) {
		$dbMock = $this->createMock( IDatabase::class );

		$resultMap = [];
		foreach ( $currentConfigs as $configName => $configValue ) {
			$resultMap[] = [
				$this->configTable,
				's_value',
				"s_name = '$configName'",

				// We have to mock optional parameters as well...
				IDatabase::class . '::selectField',
				[],
				[],

				// Return value
				$configValue
			];
		}

		// Mock selecting config values from DB
		$dbMock->method( 'selectField' )->willReturnMap( $resultMap );

		$migrator = new PluggableAuthMigrator( $dbMock );
		$migratedPluggableAuthConfig = $migrator->migrateConfigs( $currentPluggableAuthConfig );

		$this->assertEquals( $expectedPluggableAuthConfig, $migratedPluggableAuthConfig );
	}
}
