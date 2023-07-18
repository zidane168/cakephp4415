/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

 

	config.filebrowserBrowseUrl      = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder.html',
	config.filebrowserImageBrowseUrl = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder.html?type=Images',
	config.filebrowserFlashBrowseUrl = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder.html?type=Flash',
	config.filebrowserUploadUrl      = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/cidckids-playgroup-portal/webroot/js/ckfinder/ckfinder/upload.php?type=flash';
};