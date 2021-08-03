<?php

namespace BlueSpice\DistributionConnector;

class Extension extends \BlueSpice\Extension {

	public static function onRegistration() {
		// disable edit-Link on HeaderTabs
		$GLOBALS['wgHeaderTabsEditTabLink'] = false;
		static::registerWorkflows();
	}

	private static function registerWorkflows() {
		$base = dirname( __DIR__ ) . '/workflow';
		$GLOBALS['wgWorkflowDefinitions']['data-collector'] = "$base/DataCollector.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['editorial-review'] = "$base/EditorialReview.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['four-eye-principle'] = "$base/FourEyePrinciple.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['group-approval'] = "$base/GroupApproval.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['group-control'] = "$base/GroupControl.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['revision-control'] = "$base/RevisionControl.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['three-stage-feedback-collection'] = "$base/ThreeStageFeedbackCollection.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['three-step-approval'] = "$base/ThreeStepApproval.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['user-approval'] = "$base/UserApproval.bpmn";
		$GLOBALS['wgWorkflowDefinitions']['user-control'] = "$base/UserControl.bpmn";
	}
}
