<?php
class JqImgcropComponent extends Component
{

    function uploadImage($uploadedInfo, $upload_dir, $prefix)
    {
        $upload_path = $upload_dir . DS;
        $max_file = "34457280";                         // Approx 30MB
        $max_width = 800;
        $userfile_name = $uploadedInfo['name'];
        $userfile_tmp =  $uploadedInfo["tmp_name"];
        $userfile_size = $uploadedInfo["size"];
        $filename = $prefix.basename($uploadedInfo["name"]);
        $file_ext = substr($filename, strrpos($filename, ".") + 1);
        $uploadTarget = $upload_path.$filename;
        if (empty($uploadedInfo)) {
            return false;
        }
    
        if (isset($uploadedInfo['name'])) {
            move_uploaded_file($userfile_tmp, $uploadTarget);
            // chmod($uploadTarget , 0777);
            $width = $this->getWidth($uploadTarget);
            $height = $this->getHeight($uploadTarget);
            // Scale the image if it is greater than the width set above
            if ($width > $max_width) {
                $scale = $max_width / $width;
                $uploaded = $this->resizeImage($uploadTarget, $width, $height, $scale);
            } else {
                $scale = 1;
                $uploaded = $this->resizeImage($uploadTarget, $width, $height, $scale);
            }
        }
        return array('imageName' => $filename, 'imageWidth' => $this->getWidth($uploadTarget), 'imageHeight' => $this->getHeight($uploadTarget));
    }

    function getHeight($image) 
    {
        $sizes = getimagesize($image);
        $height = $sizes[1];
        return $height;
    }
    function getWidth($image) 
    {
        $sizes = getimagesize($image);
        $width = $sizes[0];
        return $width;
    }



    function resizeImage($image, $width, $height, $scale) 
    {
        $image_data = getimagesize($image);
        $imageType = image_type_to_mime_type($image_data[2]);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
        case "image/gif":
            $source = imagecreatefromgif($image);
                break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            $source = imagecreatefromjpeg($image);
                break;
        case "image/png":
        case "image/x-png":
            $source = imagecreatefrompng($image);
            break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);

        switch ($imageType) {
        case "image/gif":
            imagegif($newImage, $image);
                break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            imagejpeg($newImage, $image, 90);
                break;
        case "image/png":
        case "image/x-png":
            imagepng($newImage, $image);
            break;
        }

        //chmod($image, 0777);
        return $image;
    }


    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale) 
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
    
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
        case "image/gif":
            $source = imagecreatefromgif($image);
            break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            $source = imagecreatefromjpeg($image);
            break;
        case "image/png":
        case "image/x-png":
            $source = imagecreatefrompng($image);
            break;
        }
        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        switch ($imageType) {
        case "image/gif":
            imagegif($newImage, $thumb_image_name);
            break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            imagejpeg($newImage, $thumb_image_name, 90);
            break;
        case "image/png":
        case "image/x-png":
            imagepng($newImage, $thumb_image_name);
            break;
        }
        //chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }

    function cropImage($thumb_width, $x1, $y1, $x2, $y2, $w, $h, $thumbLocation, $imageLocation)
    {
        $scale = $thumb_width/$w;
        $cropped = $this->resizeThumbnailImage($thumbLocation, $imageLocation, $w, $h, $x1, $y1, $scale);
        return $cropped;
    }
}
?>