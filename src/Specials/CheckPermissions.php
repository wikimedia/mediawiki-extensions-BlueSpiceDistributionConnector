<?php

namespace BlueSpice\DistributionConnector\Specials;

use BlueSpice;
use Html;
use HTMLSelectField;
use HTMLTitleTextField;
use HTMLUserTextField;
use MWException;
use OOUIHTMLForm;
use Status;

class CheckPermissions extends BlueSpice\SpecialPage {

	/**
	 * @var PermissionManager
	 */
	private $permissionManager = null;

	public function __construct() {
		parent::__construct( 'CheckPermissions', 'checkpermissions', false );
	}

	/**
	 *
	 * @param string $par
	 * @return void
	 */
	public function execute( $par ) {
		$this->checkPermissions();
		$this->setHeaders();
		$out = $this->getOutput();
		$out->setPageTitle( $this->msg( 'bs-distributionconnector-checkpermissions' ) );
		$this->permissionManager = $this->services->getPermissionManager();
		$options = [];
		foreach ( $this->permissionManager->getAllPermissions() as $var ) {
			$options[$var] = $var;
		}
		$formDescriptor = [
			'username' => [
				'class' => HTMLUserTextField::class,
				'label-message' => 'bs-distributionconnector-checkpermissions-label-user'
			],
			'permission' => [
				'class' => HTMLSelectField::class,
				'label-message' => 'bs-distributionconnector-checkpermissions-label-permission',
				'options' => $options
			],
			'title' => [
				'class' => HTMLTitleTextField::class,
				'label-message' => 'bs-distributionconnector-checkpermissions-label-title'
			],
		];
		$htmlForm = new OOUIHTMLForm( $formDescriptor, $this->getContext() );
		$htmlForm->setSubmitCallback( [ $this, 'processInput' ] );
		$htmlForm->show();
	}

	/**
	 *
	 * @param array $formData
	 * @return Status|array
	 */
	public function processInput( $formData ) {
		$user = $this->services->getUserFactory()->newFromName( $formData['username'] );

		if ( !$user ) {
			return Status::newFatal(
				'bs-distributionconnector-checkpermissions-error-invalid-user'
			);
		}

		$permission = $formData['permission'];
		$title = $formData['title'];
		try {
			$title = $this->services->getTitleFactory()->newFromText( $title );
			if ( !$title ) {
				return Status::newFatal(
					'bs-distributionconnector-checkpermissions-error-invalid-title'
				);
			}
		} catch ( MWException $e ) {
			return Status::newFatal( $e->getMessage() );
		}

		$errors = $this->permissionManager->getPermissionErrors( $permission, $user, $title );
		if ( !empty( $errors ) ) {
			return $errors;
		}

		$successMsg = wfMessage(
			'bs-distributionconnector-checkpermissions-success',
			$user,
			$permission,
			$title
		);

		$this->getOutput()->addHTML(
			Html::rawElement(
				'div', [ 'class' => 'successbox' ], $successMsg
		) );
	}
}
