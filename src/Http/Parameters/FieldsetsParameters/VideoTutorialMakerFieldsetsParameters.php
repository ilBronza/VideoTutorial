<?php

namespace IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class VideoTutorialMakerFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters(): array
    {
        return [
            'base' => [
                'translationPrefix' => 'videotutorial::fields',
                'fields' => [
                    'name' => [
                        'text' => 'required|string|max:255|not_regex:/[<>]/',
                    ],
                    'title' => [
                        'text' => 'required|string|max:255|not_regex:/[<>]/',
                    ],
                    'plain_short_description' => [
                        'type' => 'textarea',
                        'label' => __('videotutorial::fields.short_description'),
                        'rules' => 'nullable|string|max:1024',
                    ],
                    'plain_description' => [
                        'type' => 'textarea',
                        'label' => __('videotutorial::fields.description'),
                        'rules' => 'nullable|string|max:10000',
                    ],
                    'active' => [
                        'boolean' => 'required|boolean',
                    ],
                    'show' => [
                        'boolean' => 'required|boolean',
                    ],
                ],
                'width' => [
                    '1-2@m',
                ],
            ],
        ];
    }
}
