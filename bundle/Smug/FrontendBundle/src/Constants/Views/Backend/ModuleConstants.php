<?php

namespace Smug\FrontendBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\FrontendBundle\Entity\Module\Module;

class ModuleConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [];

	const DETAIL_DATA = [
		'config' => [],
		'tabs' => [
			[
				'headline' => 'ALL_MODULES',
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
									'buttonText' => 'SCAN_FOR_MODULES',
									'buttonType' => 'dark',
									'method' => 'POST',
									'functionCall' => '/be/api/custom/module/scan',
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
										'getCall' => '/be/api/smug/frontend/module',
										'restrict' => false,
										'returnObject' => true,
										'edit' => [
											'template' => self::EDIT_MODULE_MODAL_DATA
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

	const EDIT_MODULE_MODAL_DATA = [
		'headline' => 'MODULE_DETAILS',
		'config' => [
			'class' => Module::class,
			'disableSave' => true
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
										'functionCall' => '/be/api/custom/module/install',
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
										'buttonText' => 'UPDATE_MODULE',
										'buttonType' => 'dark',
										'method' => 'PUT',
										'functionCall' => '/be/api/custom/module/update',
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
										'functionCall' => '/be/api/custom/module/activate',
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
										'functionCall' => '/be/api/custom/module/deactivate',
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
										'functionCall' => '/be/api/custom/module/deinstall',
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
