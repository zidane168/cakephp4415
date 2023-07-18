"use strict";

/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function (config) {
  // Define changes to default configuration here. For example:
  // config.language = 'fr';  
  // config.uiColor = '#3ba9cc';
  config.toolbar = [{
    name: 'styles',
    items: ['Format', 'FontSize']
  }, {
    name: 'colors',
    items: ['TextColor', 'BGColor']
  }, {
    name: 'basicstyles',
    items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']
  }, // { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
  {
    name: 'links',
    items: ['Link', 'Unlink']
  }];
  config.fontSize_sizes = '12pt/12pt;13pt/13pt;14pt/14pt;15pt/15pt;18pt/18pt;20pt/20pt;24pt/24pt;28pt/28pt;';
  config.removeButtons = 'Source,Save,Templates,NewPage,Preview,Print,Cut,Copy,Paste,PasteText,PasteFromWord,Redo,Undo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,Blockquote,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,ShowBlocks,About';
};