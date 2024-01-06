<?php

namespace App\Traits;

use Str;

trait KycFileUploadTrait
{
    public function kycFileUpload($query, $old = null): string // Taking input image as parameter
    {

        $allowExt = ['jpeg', 'png', 'jpg', 'gif', 'svg', 'pdf'];
        $ext = strtolower($query->getClientOriginalExtension());

        if ($query->getSize() > 5100000) {
            abort('406', 'max file size:5MB ');
        }

        if (! in_array($ext, $allowExt)) {
            abort('406', 'only allow : jpeg, png, jpg, gif, svg, pdf');
        }

        if ($old != null) {
            self::delete($old);
        }
        $image_name = Str::random(20);
        $image_full_name = $image_name.'.'.$ext;
        $upload_path = 'assets/kyc/';    // Creating Sub directory in Assets folder to put image
        $image_url = $upload_path.$image_full_name;
        $success = $query->move($upload_path, $image_full_name);

        return str_replace('assets/', '', $image_url); // Just return image
    }

    protected function delete($path)
    {
        if (file_exists('assets/'.$path)) {
            @unlink('assets/'.$path);
        }
    }
}
