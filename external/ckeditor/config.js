/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.resize_minWidth = 580;
	config.resize_maxWidth = 800;
	config.language = 'sv';
	config.toolbar = 'zidaToolbar';

    config.toolbar_zidaToolbar =
    [
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['NumberedList','BulletedList','Outdent','Indent'],
		['Undo','Redo'],
		['Templates','RemoveFormat','PasteText','Replace'],
		['SpellChecker','Scayt','Maximize', 'ShowBlocks','-','About'],
		'/',
		['Bold','Italic','Underline','Strike'],
		['Link','Anchor','Image','Flash'],
		['TextColor','BGColor'],
		['FontSize'],
		['Subscript','Superscript','Table','Smiley','SpecialChar','-','Source']
		
    ];

};
