<?php

namespace IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class VideoTutorialCreateFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        return [
            'base' => [
                'translationPrefix' => 'videotutorial::fields',
                'fields' => [
                    'parent' => [
                        'type' => 'select',
                        'multiple' => false,
                        'rules' => 'string|nullable|exists:videotutorials,slug',
                        'relation' => 'parent'
                    ],

                    'name' => ['text' => 'string|required|max:255'],
                    'title' => ['text' => 'string|required|max:255'],

                    'short_description' => ['texteditor' => 'string|required|max:1024'],
                    'description' => ['texteditor' => 'string|required|max:102400'],

                    'sorting_index' => ['number' => 'integer|nullable'],
                    'link' => ['text' => 'string|nullable|max:255'],
                    'embed' => ['textarea' => 'string|nullable|max:102400'],

                    'active' => ['boolean' => 'boolean|required'],
                    'show' => ['boolean' => 'boolean|nullable'],

                    'file' => [
                        'type' => 'file',
                        'multiple' => false,
                        'rules' =>'file|nullable|max:' . config('media-library.max_file_size')
                    ],
                ],
                'width' => ['1-2@m']
            ]
        ];
    }
}

