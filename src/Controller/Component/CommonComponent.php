<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class CommonComponent extends Component
{

    // -------------------------------------------------------
    // -- COMMON FUNCTION
    // -------------------------------------------------------

    public function upload_images($img, $relative_path, $file_name_suffix, $key)
    {

        if (!file_exists($relative_path)) {
            @mkdir($relative_path, 0777, true);
        }

        $name    = $img->getClientFileName();
        $size    = $img->getSize();
        $tmpName = $img->getStream()->getMetadata('uri');

        // rename the uploaded file
        $_filename = mb_strtolower($name);
        $file = new File($_filename);
        $renamed_file = date('Ymd-Hi') . '-' . $file_name_suffix . '-' . uniqid() . '-' . $key . '.' . $file->ext();
        $path = $relative_path . DS . $renamed_file;
        $targetPath = WWW_ROOT . $path;

        list($width, $height, $type, $attr) = getimagesize($tmpName);
        $succeed  = array();

        if ($img->getSize() > 0 && $img->getError() == 0) {
            $img->moveTo($targetPath);

            $succeed = array(
                'ori_name'      => $name,
                're_name'       => $renamed_file,
                'path'             => $path,
                'width'         => $width,
                'height'         => $height,
                'size'             => $size,
            );
        }

        return $succeed;
    }

    public function upload_files($img, $relative_path, $file_name_suffix, $key)
    {

        if (!file_exists($relative_path)) {
            @mkdir($relative_path, 0777, true);
        }

        $name    = $img->getClientFileName();

        // rename the uploaded file
        $_filename = mb_strtolower($name);
        $file = new File($_filename);
        $renamed_file = date('Ymd-Hi') . '-' . $file_name_suffix . '-' . uniqid() . '-' . $key . '.' . $file->ext();
        $path = $relative_path . DS . $renamed_file;
        $targetPath = WWW_ROOT . $path;

        $succeed  = array();
        $img->moveTo($targetPath);

        $succeed = array(
            'ori_name'      => $name,
            're_name'       => $renamed_file,
            'path'             => $path,
            'ext'           => $file->ext()
        );

        return $succeed;
    }

    public function upload_videos($img, $relative_path, $file_name_suffix, $key)
    {

        if (!file_exists($relative_path)) {
            @mkdir($relative_path, 0777, true);
        }

        $name    = $img->getClientFileName();
        $size    = $img->getSize();
        $tmpName = $img->getStream()->getMetadata('uri');

        // rename the uploaded file
        $_filename = mb_strtolower($name);
        $file = new File($_filename);
        $renamed_file = date('Ymd-Hi') . '-' . $file_name_suffix . '-' . uniqid() . '-' . $key . '.' . $file->ext();
        $path = $relative_path . DS . $renamed_file;
        $targetPath = WWW_ROOT . $path;
        $succeed  = array();

        if ($img->getSize() > 0 && $img->getError() == 0) {
            $img->moveTo($targetPath);

            $succeed = array(
                'ori_name'      => $name,
                're_name'       => $renamed_file,
                'path'             => $path,
                'size'             => $size,
                'ext'             => $file->ext()
            );
        }

        return $succeed;
    }
}
