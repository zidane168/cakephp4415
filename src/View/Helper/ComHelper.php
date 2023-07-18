<?php
namespace App\View\Helper;

use Cake\ORM\TableRegistry;
use Cake\View\Helper;

class ComHelper extends Helper
{
    public $helpers = ['Html'];

    public function formatPhoneNumber($phone_number)
    {  
        $obj_CidcParents = TableRegistry::get('CidcParents');
        return $obj_CidcParents->format_phone_number($phone_number);
    }

    // public function makeEdit($title, $url)
    // {
    //     // Use the HTML helper to output
    //     // Formatted data:

    //     $link = $this->Html->link($title, $url, ['class' => 'edit']);

    //     return '<div class="editOuter">' . $link . '</div>';
    // }
}