<?php

use MediaWiki\Config\Config;
use MediaWiki\Maintenance\Maintenance;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( !$IP ) {
	$IP = dirname( __DIR__, 3 );
}
require_once "$IP/maintenance/Maintenance.php";

class MigratePluggableAuthSettings extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->requireExtension( "BlueSpiceWikiFarm" );
	}

	public function execute() {
		/** @var Config */
		$farmConfig = $this->getServiceContainer()->getService( 'BlueSpiceWikiFarm._Config' );
		$rootDbPrefix = $farmConfig->get( 'managementDBprefix' );
		$rootDbName = $farmConfig->get( 'managementDBname' );
		$rootDb = $rootDbPrefix . $rootDbName;

		$this->output( "Searching for PluggableAuth settings in root ($rootDb) ... " );
		$dbr = $this->getServiceContainer()->getConnectionProvider()->getReplicaDatabase( $rootDb );
		$res = $dbr->newSelectQueryBuilder()
			->table( 'bs_settings3' )
			->fields( [ 's_name', 's_value' ] )
			->where( [ 's_name' => [ 'wgPluggableAuth_Config', 'wgPluggableAuth_EnableAutoLogin' ] ] )
			->caller( __METHOD__ )
			->fetchResultSet();

		$settings = [];
		foreach ( $res as $row ) {
			$settings[ $row->s_name ] = $row->s_value;
		}

		if ( !$settings ) {
			$this->output( 'None found. Skipping. ' );
			return;
		}

		$this->output( 'Found settings. Copying to instance ... ' );
		$newDbw = $this->getServiceContainer()->getConnectionProvider()->getPrimaryDatabase();
		foreach ( $settings as $name => $value ) {
			$newDbw->newInsertQueryBuilder()
				->table( 'bs_settings3' )
				->row( [
					's_name' => $name,
					's_value' => $value
				] )
				->caller( __METHOD__ )
				->execute();

			$this->output( "Copied '$name'." );
		}
	}

}

$maintClass = MigratePluggableAuthSettings::class;
require_once RUN_MAINTENANCE_IF_MAIN;
