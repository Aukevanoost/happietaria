<?php

namespace core;


class Tools
{
    private $salt = "P@sSw0rD123321!@#";
    /* Genereert een random string voor afbeeldingen ofzo */
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /* Dit zou ook wat moeten doen */
    public function generateReplaceString($length = 40) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function uploadImage($image, $folder, $size, $thumb, $old_image){

        // als er geen grootte meegegeven is maakt hij de grootte automatisch 5 MB
        if($size == 0) {
            $size = 5242880;
        }

        // Basis defineren
        $target_dir = "./template/_img/".$folder."/";
        $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        $FileName = md5($randomString . pathinfo($image["name"], PATHINFO_FILENAME)). '.' . pathinfo($image["name"], PATHINFO_EXTENSION);
        $target_file = $target_dir . $FileName;
        $uploadOk = 1; $errorMessage = "";
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        $success = false;
        $sizeinMB = ($size / 1024) / 1024;

        // Check of de afbeelding niet te groot is
        if ($image["size"] > $size) {
            $errorMessage .= 'ERROR: image too big. (max '.round($sizeinMB).' MB), ';
            $uploadOk = 0;
        }

        // Check of de afbeelding wel een afbeelding is
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $errorMessage .= 'ERROR: nope. (only: jpg, png, gif), ';
            $uploadOk = 0;
        }

        // check of er iets niet goed ging / of er een error is
        if ($uploadOk == 0)
            $errorMessage .=  " | upload failed";
        else {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                $success = true;
                // oude afbeelding verwijderen
                if($old_image != "") {
                    if (!unlink($target_dir . $old_image)){
                        $errorMessage = 'ERROR: old image not found';
                        $success = false;
                    }
                    if($thumb){
                        if (!unlink($target_dir . "thumb/" . $old_image)){
                            $errorMessage = 'ERROR: old thumbnail not found';
                            $success = false;
                        }
                    }

                }

                // Thumbnail
                if($thumb) {
                    $thumbResult = $this->createThumbnail($FileName, $folder);
                    if(!$thumbResult) {
                        $errorMessage = "ERROR: thumbnail failed";
                        $success = false;
                    }
                }

            } else {
                $errorMessage = "ERROR: Image upload failed";
            }
        }

        $result = array("message" => $errorMessage, "name" => $FileName, "error" => $image["error"], "file" => $target_file);
        return $result;
    }

    protected function uploadImages($images, $folder, $size, $thumb, $old_image){
        $total = count($images['name']);
        $success = false;
        $uploadOk = 1;
        $errorMessage = "";
        $imgNames = array();

        // als er geen grootte meegegeven is maakt hij de grootte automatisch 5 MB
        if($size == 0) {
            $size = 5242880;
        }

        for($i=0; $i<$total; $i++) {

            // Basis defineren
            $target_dir = "./template/_img/".$folder."/";
            $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
            $FileName = md5($randomString . pathinfo($images["name"][$i], PATHINFO_FILENAME)). '.' . pathinfo($images["name"][$i], PATHINFO_EXTENSION);
            $target_file = $target_dir . $FileName;

            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            $sizeinMB = ($size / 1024) / 1024;

            // Check of de afbeelding niet te groot is
            if ($images["size"][$i] > $size) {
                $errorMessage .= 'ERROR: image too big. (max '.round($sizeinMB).' MB), ';
                $uploadOk = 0;
            }

            // Check of de afbeelding wel een afbeelding is
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "PNG" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                $errorMessage .= 'ERROR: nope. (only: jpg, png, gif), ';
                $uploadOk = 0;
            }

            // check of er iets niet goed ging / of er een error is
            if ($uploadOk == 0)
                $errorMessage .=  " | upload failed";
            else {
                if (move_uploaded_file($images["tmp_name"][$i], $target_file)) {
                    $success = true;
                    // oude afbeelding verwijderen
                    if($old_image != "") {
                        if (!unlink($target_dir . $old_image)){
                            $errorMessage = 'ERROR: old image not found';
                            $success = false;
                        }
                        if($thumb){
                            if (!unlink($target_dir . "thumb/" . $old_image)){
                                $errorMessage = 'ERROR: old thumbnail not found';
                                $success = false;
                            }
                        }

                    }

                    // Thumbnail
                    if($thumb) {
                        $thumbResult = $this->createThumbnail($FileName, $folder);
                        if(!$thumbResult) {
                            $errorMessage = "ERROR: thumbnail failed";
                            $success = false;
                        }
                    }

                } else {
                    $errorMessage = "ERROR: Image upload failed";
                }
            }
            array_push($imgNames, $FileName);


        }

        $result = array("message" => $errorMessage, "names" => $imgNames, "error" => $_FILES["image"]["error"], "file" => $target_file);

        return $result;
    }

    public function uploadPdf($file, $maxSize,$old_file){
        $message = "";
        $target_dir = "./template/files/";

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $message = "Error while uploading PDF file";
        }

        // ensure a safe filename
        $name = preg_replace("/[^A-Z0-9._-]/i", "_", $file["name"]);

        // don't overwrite an existing file
        $i = 0;
        $parts = pathinfo($name);
        while (file_exists($target_dir . $name)) {
            $i++;
            $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }

        if($parts["extension"] != 'pdf' && $parts["extension"] != 'docx' && $parts["extension"] != 'txt'){
            $message = "This file cannot be uploaded, only pdf, docx and txt are allowed";

        }

        if($message == ''){
            // preserve file from temporary directory
            $success = move_uploaded_file($file["tmp_name"],
                $target_dir . $name);


            if (!$success) {
                $message = "Unable to save file.";
            }else{
                if($old_file != "") {
                    if (!unlink($target_dir . $old_file)){
                        $message = 'ERROR: old file not found';
                    }

                }
            }

            // set proper permissions on the new file
            chmod($target_dir . $name, 0644);
        }

        $result = array("message" => $message, "name" => $name);

        return $result;
    }

    /* Maakt een thumbnail van de afbeelding */
    private function createThumbnail($image, $folder) {
        // thumb info
        $thumbnail_width = 300;
        $thumbnail_height = 300;
        $background=false;

        // check if directory already exists
        if (!is_dir("./template/_img/".$folder."/thumb")) {
            mkdir("./template/_img/".$folder."/thumb", 0777, true);
        }

        // Location stuff
        $filepath = "./template/_img/".$folder."/".$image;
        $thumbpath = "./template/_img/".$folder."/thumb/".$image;

        // Get new dimensions
        list($original_width, $original_height, $original_type) = getimagesize($filepath);

        $ratio_orig = $original_width/$original_height;

        if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
            $thumbnail_width = $thumbnail_height*$ratio_orig;
        } else {
            $thumbnail_height = $thumbnail_width/$ratio_orig;
        }

        if ($original_type === 1) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        } else if ($original_type === 2) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        } else if ($original_type === 3) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        } else {
            return false;
        }

        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background

        // figuring out the color for the background
        if(is_array($background) && count($background) === 3) {
            list($red, $green, $blue) = $background;
            $color = imagecolorallocate($new_image, 255, 255, 255);
            imagefill($new_image, 0, 0, $color);
            // apply transparent background only if is a png image
        } else if($background === 'transparent' && $original_type === 3) {
            imagesavealpha($new_image, TRUE);
            $color = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefill($new_image, 0, 0, $color);
        }

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $original_width, $original_height);
        $imgt($new_image, $thumbpath);

        return file_exists($thumbpath);
    }



    protected function hashPassword($password, $salt2){
        $salt1 = hash("snefru",$this->salt);
        $password_hash = hash("ripemd320", $salt1 . $salt2 . $password);
        return $password_hash;
    }
	
	
	protected function formatTimestamp($datetime) {
		$seconds_ago = (time() - $datetime);

		$timestamp = "";
		if ($seconds_ago >= 31536000) {
			$timestamp =  " " . intval($seconds_ago / 31536000) . " year(s) ago";
		} elseif ($seconds_ago >= 2419200) {
			$timestamp =  " " . intval($seconds_ago / 2419200) . " month(s) ago";
		} elseif ($seconds_ago >= 86400) {
			$timestamp =  "About " . intval($seconds_ago / 86400) . " day(s) ago";
		} elseif ($seconds_ago >= 3600) {
			$timestamp =  "About " . intval($seconds_ago / 3600) . " hour(s) ago";
		} elseif ($seconds_ago >= 60) {
			$timestamp =  "About " . intval($seconds_ago / 60) . " minute(s) ago";
		} else {
			$timestamp =  "About less than a minute ago";
		}
		return $timestamp;
	}

}