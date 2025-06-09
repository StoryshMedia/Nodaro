<h1 align="center">Field configurations</h1>

## Fields

- [General Configuration](#general-configuration)
- [AdvancedAssociation](#advancedassociation)
- [Button](#button)
- [Card](#card)
- [Checkbox](#checkbox)
- [Column](#column)
- [Content](#content)
- [Datepicker](#datepicker)
- [FileUpload](#fileupload)
- [ImageGallery](#imagegallery)
- [Infobox](#infobox)
- [Messages](#messages)
- [Multiple](#multiple)
- [Output](#output)
- [SearchSelect](#searchselect)
- [Selectbox](#selectbox)

## General Configuration

All fields have this basic structure and can be controlled via the following basic config

```
[
    'type' => 'COMPONENT_TYPE',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'disabled' => false
    ]
]
```

## AdvancedAssociation

Provides a list of child elements with modification functionalities

```
[
    'type' => 'AdvancedAssociation',
    'config' => [
        'parent' => 'PARENT_ITEM_IDENTIFIER',
        'addConfig' => [
            'config' => [
                'saveCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/add',
                'template' => VIEW_TEMPLATE_ARRAY
            ]
        ],
        'rows' => [
            [
                'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-20',
                'controlClass' => 'col-span-2',
                'controls' => [
                    [
                        'label' => 'EDIT',
                        'icon' => 'IconPencil',
                        'type' => 'modal',
                        'config' => [
                            'call' => '/be/api/NAMESPACE/BUNDLE/MODEL/save',
                            'template' => VIEW_TEMPLATE_ARRAY
                        ]
                    ],
                    [
                        'label' => 'DELETE',
                        'icon' => 'IconMinus',
                        'type' => 'danger',
                        'call' => '/be/api/NAMESPACE/BUNDLE/MODEL/delete',
                        'confirm' => true,
                        'action' => [
                            'type' => 'remove'
                        ]
                    ]
                ],
                'fields' => [
                    [
                        'type' => 'Card',
                        'placeholder' => 'PLACEHOLDER_STRING',
                        'identifier' => 'publication',
                        'config' => [
                            'returnObject' => true,
                            'headlineIdentifier' => 'SOME_PROPERTY',
                            'descriptionIdentifier' => 'SOME_PROPERTY',
                            'buttonText' => 'LINK_LABEL',
                            'buttonLink' => 'LINK_TARGET'
                        ]
                    ],
                    [
                        'type' => 'Output',
                        'placeholder' => 'PLACEHOLDER_STRING',
                        'identifier' => 'SOME_PROPERTY',
                        'autocomplete' => false,
                        'fileArrayIdentifier' => '',
                        'config' => [
                            'mentions' => false,
                            'disabled' => true
                        ]
                    ]
                ]
            ]
        ],
        'bypassId' => true
    ]
]
```

## Button

A simple button for api calls

```
[
    'type' => 'Button',
    'placeholder' => '',
    'config' => [
        'condition' => [
            'type' => 'isTrue',
            'checkProperty' => 'SOME_PROPERTY'
        ],
        'bypassId' => true,
        'buttonText' => 'BUTTON_LABEL',
        'buttonType' => 'dark|danger|success|info',
        'method' => 'POST|GET|PUT',
        'functionCall' => 'API_ENDPOINT',
        'successHandling' => [
            'type' => 'refresh|reload'
        ]
    ]
]
```

## Card

A card element to display child entities in a list

```
[
    'type' => 'Card',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'identifier' => 'SOME_PROPERTY',
        'headlineIdentifier' => 'SOME_PROPERTY',
        'descriptionIdentifier' => 'SOME_PROPERTY',
        'buttonText' => 'BUTTON_LABEL',
        'buttonLink' => 'LINK_TARGET'
    ]
]
```

## Checkbox

A simple checkbox element for form data

```
[
    'type' => 'Checkbox',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'trueLabel' => 'ENABLED',
        'falseLabel' => 'DISABLED',
        'disabled' => true
    ]
]
```

## Column

Displays items inside a table column

```
[
    'type' => 'Column',
    'config' => [
        'items' => [
            [
                'type' => 'Checkbox',
                'placeholder' => 'BANNED',
                'identifier' => 'SOME_PROPERTY',
                'config' => [
                    'disabled' => true
                ]
            ]
        ],
        'fieldClasses' => '',
        'bypassId' => true
    ]
]
```

## Content

A Content editor for frontend sites or blog entries 

```
[
    'type' => 'Content',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'disableSettings' => true,
        'items' => [
            'itemIdentifier' => 'entry',
            'addItemCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/add',
            'deleteItemCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/delete',
            'tabAddCall' => '/be/api/custom/module/tab',
            'pluginConfigRefreshCall' => '/be/api/custom/module/plugin/config/refresh',
            'saveModuleItemCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/save',
            'saveItemCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/save',
            'renderTemplateCall' => '/be/api/custom/module/plugin/template',
            'refreshCall' => '/be/api/custom/module/rerender'
        ],
        'modules' => [
            'getCall' => '/be/api/NAMESPACE/BUNDLE/MODEL',
            'refreshCall' => '/be/api/custom/module/rerender',
            'addItemCall' => '/be/api/NAMESPACE/BUNDLE/MODEL'
        ]
    ]
]
'config' => [
    'disableSettings' => true
]
```

## Datepicker

A simple colorpicker element uses external library [flatPickr](https://flatpickr.js.org/)

```
[
    'type' => 'Datepicker',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'valueType' => 'date|time',
        'disabled' => true
    ],
]
```

## FileUpload

A component which provides the ability to upload files or select a file from media library

```
[
    'type' => 'FileUpload',
    'placeholder' => 'SOME_HEADLINE',
    'identifier' => 'SOME_PROPERTY',
    'config' => [
        'uploadCall' => 'API_ENDPOINT',
        'multiple' => true,
        'mini' => true,
        'dropText' => 'DROP_HERE',
        'assignAlbum' => 'user',
        'baseModel' => [],
        'bypassId' => true
    ]
]
```

## ImageGallery

A simple gallery element to show multiple images of the item


```
[
    'type' => 'ImageGallery',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'controls' => [
            [
                'label' => 'SET_MAIN_IMAGE',
                'icon' => 'ICON_STRING',
                'type' => 'success',
                'call' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/main'
            ],
            [
                'label' => 'DELETE',
                'icon' => 'ICON_STRING',
                'type' => 'danger',
                'call' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/delete',
                'confirm' => true
            ]
        ],
        'getCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/',
        'bypassId' => true
    ]
]
```

## Infobox

Display an infobox for different purposes

```
[
    'type' => 'Infobox',
    'config' => [
        'icon' => 'ICON_STRING',
        'headline' => 'HEADLINE_TEXT',
        'text' => 'BODY_TEXT',
        'linkText' => 'LINK_TEXT',
        'linkUrl' => 'LINK_TARGET',
    ]
]
```

## Messages

A chat message component to display message history

```
[
    'type' => 'Messages',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'showUserImage' => true,
        'filesIdentifier' => 'SOME_PROPERTY',
        'relations' => [
            [
                'placeholder' => 'RELATION_PLACEHOLDER',
                'identifier' => 'SOME_PROPERTY',
                'config' => [
                    'headlineIdentifier' => 'SOME_PROPERTY',
                    'buttonLink' => 'LINK_TARGET'
                ]
            ]
        ],
        'badges' => [
            [
                'label' => 'LABEL_TEXT',
                'type' => 'warning',
                'condition' => [
                    'type' => 'isTrue',
                    'checkProperty' => 'SOME_PROPERTY'
                ]
            ]
        ],
        'actions' => [
            [
                'label' => 'LABEL_TEXT',
                'icon' => 'IconX',
                'condition' => [
                    'type' => 'multiple',
                    'allowedBy' => 'and',
                    'checks' => [
                        [
                            'condition' => [
                                'type' => 'isFalse',
                                'checkProperty' => 'SOME_PROPERTY'
                            ]
                        ],
                        [
                            'condition' => [
                                'type' => 'isTrue',
                                'checkProperty' => 'SOME_PROPERTY'
                            ]
                        ]
                    ]
                ],
                'actionCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/delete'
            ]
        ]
    ]
]
```

## Multiple

Lists all child items with the ability to change Data in the list

```
[
    'type' => 'Multiple',
    'placeholder' => 'SOME_HEADLINE',
    'information' => 'SOME_INFORMATION',
    'config' => [
        'type' => 'FIELD_TYPE E.G. Card',
        'bypassId' => true,
        'placeholder' => '',
        'config' => [
            'getCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/',
            'returnObject' => true,
            'identifier' => 'SOME_PROPERTY',
            'headlineIdentifier' => 'SOME_PROPERTY',
            'descriptionIdentifier' => 'SOME_PROPERTY',
            'buttonText' => 'LINK_LABEL',
            'buttonLink' => 'LINK_TARGET'
        ]
    ]
]
```

## Output

A simple output of the field value - just for visualization

```
[
    'type' => 'Output',
    'placeholder' => '',
    'identifier' => 'subject',
    'config' => [
        'showHeader' => true,
        'header' => 'SOME_HEADLINE'
    ]
]
```

## SearchSelect

An advanced selectbox component with the ability to search items within the API

```
[
    'type' => 'SearchSelect',
    'placeholder' => 'SOME_HEADLINE',
    'identifier' => 'SOME_PROPERTY',
    'config' => [
        'setModel' => false,
        'setIdentifier' => 'SOME_PROPERTY',
        'searchCall' => 'API_SEARCH_ENDPOINT'
    ]
]
```

## Selectbox

An advanced selectbox component with the ability to search items within the API. You have either the option to provide selectable items via the property 'items' or give an api endpoint via the property 'getCall'

```
[
    'type' => 'Selectbox',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'disabled' => true,
        'getCall' => 'API_ENDPOINT',
        'items' => [
            [
                'title' => 'SOME_TITLE',
                'value' => 'SOME_VALUE'
            ]
        ]
    ]
]
```

## SelectList

A side by side selection comoponent, where the selected values are printed

```
[
    'type' => 'SelectList',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'selections' => [
            'getCall' => '/be/api/NAMESPACE/BUNDLE/MODEL'
        ],
        'model' => [
            'getCall' => '/be/api/NAMESPACE/BUNDLE/MODEL/PROPERTY/',
            'getUrlParameters' => [
                [
                    'identifier' => 'SOME_PROPERTY'
                ]
            ]
        ],
        'bypassId' => true
    ]
]
```

## Table

Display and manage child data in a table view

```
[
    'type' => 'Table',
    'placeholder' => 'SOME_HEADLINE',
    'config' => [
        'columns' => [
            [
                'identifier' => 'SOME_PROPERTY',
                'subIdentifier' => 'SOME_PROPERTY',
                'type' => 'string|array|boolean|float|link|rating'
            ]
        ],
        'controls' => [
            [
                'type' => 'function|link|modal',
                'config' => [
                    'confirm' => true|false,
                    'text' => 'DELETE_CONFIRMATION_TEXT',
                    'headline' => 'DELETE_CONFIRMATION_HEADLINE',
                    'icon' => 'ICON_STRING',
                    'call' => '/be/api/NAMESPACE/BUNDLE/MODEL/delete'
                ]
            ]
        ]
    ]
]
```
