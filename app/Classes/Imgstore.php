<?php

namespace App\Classes;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class Imgstore
{
    public static function file($image, $type)
    {
        if(!isset($image)) return null;
        if ($type == 'post') $folder = 'images';
        if ($type == 'profile') $folder = 'profiles';

        Image::make($image)->encode('webp', 100)->fit(400, 400)->save();
        $imgfilename = uniqid() . '.webp';
        Storage::disk('local')->put('public/'.$folder.'/' . $imgfilename, fopen($image, 'r+'));

        return $imgfilename;
    }
}
