<?php
    /*
    * Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
    * "PHP & Jquery image upload & crop"
    * Date: 2008-11-21
    * Ver 1.2
    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    *
    * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
    * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
    * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
    * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
    * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
    * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
    * THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
    *
    */
    
    /*
     the functions used by the image upload functionality
    */
    
    /*
     resizes a given image
    */
    function resizeImage($image,$width,$height,$scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
        switch($imageType)
        {
            case "image/gif":
                $source=imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source=imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source=imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

        switch($imageType)
        {
            case "image/gif":
                imagegif($newImage,$image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage,$image,90);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage,$image);
                break;
        }

        chmod($image, 0777);
        imagedestroy($newImage);
        return $image;
    }
    /*
     creates a square image based on the selected area - used three times to produce three different sized images for different
     areas of the site
    */
    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
        switch($imageType)
        {
            case "image/gif":
                $source=imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source=imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source=imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
        switch($imageType)
        {
            case "image/gif":
                imagegif($newImage,$thumb_image_name);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage,$thumb_image_name,90);
             break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage,$thumb_image_name);
                break;
        }
        chmod($thumb_image_name, 0777);
        imagedestroy($newImage);
        return $thumb_image_name;
    }
    /*
     returns image height
    */
    function getHeight($image)
    {
        $dev=0;
        dev_dump($image,"Image in getHeight()",$dev);
        $size = getimagesize($image);
        $height = $size[1];
        return $height;
    }
    /*
     returns image width
    */
    function getWidth($image)
    {
        $dev=0;
        dev_dump($image,"Image in getWidth()",$dev);
        $size = getimagesize($image);
        $width = $size[0];
        return $width;
    }
    function strip_extra_file_extension($file_name,$file_ext)
    {
        if (substr_count($file_name,".")>1)
        {
            $exploded=explode(".",$file_name);
            return $exploded[0].".".$file_ext;
        }
        else
        {
            return $file_name;
        }
    }
?>