<?php

namespace Smug\FrontendBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\FrontendBundle\Entity\Script\Script;

class ScriptConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [];

	const DETAIL_DATA = [
		'config' => [],
		'tabs' => [
			[
				'headline' => 'ALL_SCRIPTS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-4 gap-5 my-5',
						'fields' => [
							[
								'type' => 'Button',
								'placeholder' => '',
								'config' => [
									'bypassId' => true,
									'buttonText' => 'SCAN_FOR_SCRIPTS',
									'buttonType' => 'dark',
									'method' => 'POST',
									'functionCall' => '/be/api/custom/script/scan',
									'successHandling' => [
										'type' => 'refresh'
									]
								]
							],
							[
								'type' => 'Button',
								'placeholder' => '',
								'config' => [
									'bypassId' => true,
									'buttonText' => 'ADD_CUSTOM_SCRIPTS',
									'buttonType' => 'dark',
									'method' => 'POST',
									'functionCall' => '/be/api/custom/empty/install',
									'successHandling' => [
										'type' => 'refresh'
									]
								]
							]
						]
					],
					[
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => [
							[
								'type' => 'Multiple',
								'placeholder' => '',
								'information' => '',
								'config' => [
									'saveCall' => '/be/api/smug/frontend/script/save',
									'emitReaction' => false,
									'type' => 'Card',
									'bypassId' => true,
									'placeholder' => '',
									'config' => [
										'badges' => [
											[
												'title' => 'INSTALLED',
												'type' => 'dark',
												'condition' => [
													'type' => 'isTrue',
													'checkProperty' => 'installed'
												]
											],
											[
												'title' => 'ACTIVE',
												'type' => 'success',
												'condition' => [
													'type' => 'isTrue',
													'checkProperty' => 'active'
												]
											],
											[
												'title' => 'INACTIVE',
												'type' => 'warning',
												'condition' => [
													'type' => 'multiple',
													'allowedBy' => 'and',
													'checks' => [
														[
															'condition' => [
																'type' => 'isTrue',
																'checkProperty' => 'installed'
															]
														],
														[
															'condition' => [
																'type' => 'isFalse',
																'checkProperty' => 'active'
															]
														]
													]
												]
											],
											[
												'title' => 'UNINSTALLED',
												'type' => 'danger',
												'condition' => [
													'type' => 'isFalse',
													'checkProperty' => 'installed'
												]
											]
										],
										'getCall' => '/be/api/smug/frontend/script',
										'restrict' => false,
										'returnObject' => true,
										'edit' => [
											'template' => self::EDIT_SCRIPT_MODAL_DATA
										],
										'headlineIdentifier' => 'title',
										'descriptionIdentifier' => 'description'
									]
								]
							]
						]
					]
				]
			]
		]
	];

	const EDIT_SCRIPT_MODAL_DATA = [
		'headline' => 'SCRIPT_DETAILS',
		'config' => [
			'class' => Script::class
		],
		'data' => [
			'tabs' => [
				[
					'headline' => 'BASE_SETTINGS',
					'type' => 'standard',
					'rows' => [
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Text',
									'placeholder' => 'TITLE',
									'identifier' => 'title'
								]
							]
						],
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Editor',
									'placeholder' => 'DESCRIPTION',
									'identifier' => 'description',
									'config' => [
										'mentions' => false
									]
								]
							]
						]
					],
				],
				[
					'headline' => 'SCRIPT_VALUES',
					'type' => 'standard',
					'rows' => [
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Text',
									'placeholder' => 'TEMPLATE',
									'identifier' => 'template'
								]
							]
						],
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Text',
									'placeholder' => 'EXTERNAL_SOURCE',
									'identifier' => 'externalSrc'
								]
							]
						],
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Textarea',
									'placeholder' => 'PLAIN_SCRIPT',
									'identifier' => 'plainScript'
								]
							]
						],
						[
							'class' => 'grid grid-cols-1 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'PluginFields',
									'placeholder' => '',
									'identifier' => 'fields'
								]
							]
						]
					],
				],
				[
					'headline' => 'STATE',
					'type' => 'standard',
					'rows' => [
						[
							'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
							'fields' => [
								[
									'type' => 'Button',
									'placeholder' => '',
									'config' => [
										'condition' => [
											'type' => 'isFalse',
											'checkProperty' => 'installed'
										],
										'bypassId' => true,
										'buttonText' => 'INSTALL',
										'buttonType' => 'dark',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/script/install',
										'successHandling' => [
											'type' => 'refresh'
										]
									]
								],
								[
									'type' => 'Button',
									'placeholder' => '',
									'config' => [
										'condition' => [
											'type' => 'isTrue',
											'checkProperty' => 'installed'
										],
										'bypassId' => true,
										'buttonText' => 'UPDATE_SCRIPT',
										'buttonType' => 'dark',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/script/update',
										'successHandling' => [
											'type' => 'refresh'
										]
									]
								],
								[
									'type' => 'Button',
									'placeholder' => '',
									'config' => [
										'condition' => [
											'type' => 'multiple',
											'allowedBy' => 'and',
											'checks' => [
												[
													'condition' => [
														'type' => 'isTrue',
														'checkProperty' => 'installed'
													]
												],
												[
													'condition' => [
														'type' => 'isFalse',
														'checkProperty' => 'active'
													]
												]
											]
										],
										'bypassId' => true,
										'buttonText' => 'ACTIVATE',
										'buttonType' => 'success',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/script/activate',
										'successHandling' => [
											'type' => 'refresh'
										]
									]
								],
								[
									'type' => 'Button',
									'placeholder' => '',
									'config' => [
										'condition' => [
											'type' => 'multiple',
											'allowedBy' => 'and',
											'checks' => [
												[
													'condition' => [
														'type' => 'isTrue',
														'checkProperty' => 'installed'
													]
												],
												[
													'condition' => [
														'type' => 'isTrue',
														'checkProperty' => 'active'
													]
												]
											]
										],
										'bypassId' => true,
										'buttonText' => 'DEACTIVATE',
										'buttonType' => 'warning',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/script/deactivate',
										'successHandling' => [
											'type' => 'refresh'
										]
									]
								],
								[
									'type' => 'Button',
									'placeholder' => '',
									'config' => [
										'condition' => [
											'type' => 'multiple',
											'allowedBy' => 'and',
											'checks' => [
												[
													'condition' => [
														'type' => 'isTrue',
														'checkProperty' => 'installed'
													]
												],
												[
													'condition' => [
														'type' => 'isTrue',
														'checkProperty' => 'active'
													]
												]
											]
										],
										'bypassId' => true,
										'buttonText' => 'DEINSTALL',
										'buttonType' => 'danger',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/script/deinstall',
										'successHandling' => [
											'type' => 'refresh'
										]
									]
								]
							]
						]
					],
				]
			]
		]
	];

	public static function getListConstants(): array
	{
		return self::LIST_DATA;
	}

	public static function getDetailConstants(): array
	{
		return self::DETAIL_DATA;
	}

	public static function getAddConstants(): array
	{
		return [];
	}

	public static function getReadingRights(): string
	{
		return '';
	}

	public static function getWritingRights(): string
	{
		return '';
	}
}
