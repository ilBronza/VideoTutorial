<?php

namespace IlBronza\VideoTutorial\Http\Parameters\TableFields;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class VideoTutorialFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'videotutorial::fields',
            'fields' => [
                'mySelfSee' => 'links.see',
                'mySelfEdit' => 'links.edit',
                'name' => 'flat',
                'title' => 'flat',
                'parent' => 'relations.belongsTo',
                'children' => 'relations.hasMany',
                'short_description' => 'flat',
                'description' => 'texts.ellipsis',
                'link' => 'boolean',
                'file' => 'boolean',
                'embed' => 'boolean',
                'active' => 'editor.toggle',
                'show' => 'editor.toggle',
            ]
        ];
	}
}