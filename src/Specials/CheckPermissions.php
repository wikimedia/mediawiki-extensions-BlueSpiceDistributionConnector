<?php

namespace BlueSpice\DistributionConnector\Specials;

use InvalidArgumentException;
use MediaWiki\Html\Html;
use MediaWiki\HTMLForm\Field\HTMLSelectField;
use MediaWiki\HTMLForm\Field\HTMLTitleTextField;
use MediaWiki\HTMLForm\Field\HTMLUserTextField;
use MediaWiki\HTMLForm\OOUIHTMLForm;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Status\Status;
use MediaWiki\Title\TitleFactory;
use MediaWiki\User\UserFactory;
use PermissionsError;
use StatusValue;

class CheckPermissions extends SpecialPage {

	/**
	 * @var PermissionManager
	 */
	private $permissionManager;

	/**
	 * @var TitleFactory
	 */
	private $titleFactory;

	/**
	 * @var UserFactory
	 */
	private $userFactory;

	/**
	 * @param PermissionManager $permissionManager
	 * @param TitleFactory $titleFactory
	 * @param UserFactory $userFactory
	 */
	public function __construct(
		PermissionManager $permissionManager, TitleFactory $titleFactory, UserFactory $userFactory
	) {
		parent::__construct( 'CheckPermissions', 'checkpermissions', false );
		$this->permissionManager = $permissionManager;
		$this->titleFactory = $titleFactory;
		$this->userFactory = $userFactory;
	}

	/**
	 *
	 * @param string $subPage
	 * @return void
	 * @throws PermissionsError
	 */
	public function execute( $subPage ) {
		$this->checkPermissions();
		$this->setHeaders();
		$out = $this->getOutput();
		$out->setPageTitle( $this->msg( 'bs-distributionconnector-checkpermissions' ) );
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
	 * @return StatusValue
	 */
	public function processInput( $formData ) {
		$user = $this->userFactory->newFromName( $formData['username'] );

		if ( !$user ) {
			return Status::newFatal(
				'bs-distributionconnector-checkpermissions-error-invalid-user'
			);
		}

		$permission = $formData['permission'];
		$title = $formData['title'];
		try {
			$title = $this->titleFactory->newFromText( $title );
			if ( !$title ) {
				return Status::newFatal(
					'bs-distributionconnector-checkpermissions-error-invalid-title'
				);
			}
		} catch ( InvalidArgumentException $e ) {
			return Status::newFatal( $e->getMessage() );
		}

		$permissionStatus = $this->permissionManager->getPermissionStatus( $permission, $user, $title );
		if ( !$permissionStatus->isOK() ) {
			return $permissionStatus;
		}

		$successMsg = $this->msg(
			'bs-distributionconnector-checkpermissions-success',
			$user,
			$permission,
			$title
		);

		$this->getOutput()->addHTML(
			Html::rawElement(
				'div', [ 'class' => 'successbox' ], $successMsg
		) );

		return StatusValue::newGood();
	}
}
