<?php

if ($_GET['none'] == '1') {
	exit;
}


require "../config.php";

// Privilieges and checks
if (empty($user)) {
   $error = '1';
}

        if (isset($_GET['qqfile'])) {
        	$parts = pathinfo($_GET['qqfile']);
        	$ext = strtolower($parts['extension']);
        } elseif (isset($_FILES['qqfile'])) {
        	$parts = pathinfo($_FILES['qqfile']);
        	$ext = strtolower($parts['extension']);
        } else {
        	// boooooooooom goes the dy-no-MITE!
        }
        
        
$check_type = check_type($ext);

//if (empty($_POST['id'])) {
	if ($check_type == 'file' && $privileges['upload_files'] != '1') {
		$error = '1';
		$error_msg = lg_cannot_upload;
	}
	else if ($check_type == 'img' && $privileges['upload_images'] != '1') {
		$error = '1';
		$error_msg = lg_cannot_upload_img;
	}
	else if (empty($check_type)) {
		$error = '1';
		$error_msg = lg_error; 
	}
//}

if ($error == '1') {
	die("{'error':'" . lg_error . "'}");   
	exit;
}



/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm extends db {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        
        $path = $uploadDirectory . $filename . '.' . $ext;
	        
        // Image or file?
        $check_type = check_type($ext);
        if ($check_type == 'img') {
        	$uploadDirectory .= "media/";
        }
	        
	        // ----------------------------------------------------
	         
            
	        	// UPLOADING AN IMAGE
       		global $user_data;
       		if ($check_type == 'img') {
				require PATH . "/includes/image.functions.php";
				$image = new image;
				$relative_path = str_replace(PATH, '', $path);
				$q = "
					INSERT INTO `" . TABLE_PREFIX . "media` (`filename`,`location`,`title`,`caption`,`owner`,`date`,`public`)
					VALUES ('" . $this->mysql_clean($filename) . "','" . $this->mysql_clean($relative_path) . "','" . $this->mysql_clean($filename) . "','','" . $user_data['id'] . "','" . $this->current_date() . "','0')
				";
			   	$insert_id = $this->insert($q);
				$thumb_name = $uploadDirectory . "tb-" . $filename . '.' . $ext;
				$thumbnail = $image->crop_image($path,$thumb_name,'250','');
			}
			
			// UPLOADING A FILE
			else {
			
				$url = URL . "/generated/" . $filename;
				$rand = time() . rand(1000,9999);
				$id_put = substr(md5($rand),0,10);
		   	   	$q = "
		   	   		INSERT INTO `" . TABLE_PREFIX . "attachments` (`id`,`path`,`server_path`,`filename`,`owner`)
		   	   		VALUES ('" . $id_put . "','" . $this->mysql_clean($url) . "','" . $this->mysql_clean($path) . "','" . $this->mysql_clean($filename) . "','" . $user_data['username'] . "')
		   	   	";
		   	   	$insert_id = $this->insert($q);
		   	   	
			}
	        
	        // ----------------------------------------------------
	        
	        
	        
      //  $q = "INSERT INTO `" . TABLE_PREFIX . "temp_uploads` (`temp_id`,`path`,`key`,`date`) VALUES ('" . $this->mysql_clean($final_id) . "','" . $this->mysql_clean($path) . "','$key','" . $this->current_date() . "')";
       // $insert = $this->insert($q);
        
        
        
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader extends db {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        // Image or file?
        $check_type = check_type($ext);
        if ($check_type == 'img') {
        	$uploadDirectory .= "media/";
        }
    
            
        // Save to server...
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
        
	        $path = $uploadDirectory . $filename . '.' . $ext;
	        
	        // ----------------------------------------------------
	          
       		global $user_data;
       			
	        	// UPLOADING AN IMAGE
       		if ($check_type == 'img') {
				require PATH . "/includes/image.functions.php";
				$image = new image;
				$relative_path = str_replace(PATH, '', $path);
				$q = "
					INSERT INTO `" . TABLE_PREFIX . "media` (`filename`,`location`,`title`,`caption`,`owner`,`date`,`public`)
					VALUES ('" . $this->mysql_clean($filename) . "','" . $this->mysql_clean($relative_path) . "','" . $this->mysql_clean($filename) . "','','" . $user_data['id'] . "','" . $this->current_date() . "','0')
				";
			   	$insert_id = $this->insert($q);
				$thumb_name = $uploadDirectory . "tb-" . $filename . '.' . $ext;
				$thumbnail = $image->crop_image($path,$thumb_name,'250','');
			}
			
			// UPLOADING A FILE
			else {
		   	   	
			
				$url = URL . "/generated/" . $filename;
				$rand = time() . rand(1000,9999);
				$id_put = substr(md5($rand),0,10);
		   	   	$q = "
		   	   		INSERT INTO `" . TABLE_PREFIX . "attachments` (`id`,`path`,`server_path`,`filename`,`owner`)
		   	   		VALUES ('" . $id_put . "','" . $this->mysql_clean($url) . "','" . $this->mysql_clean($path) . "','" . $this->mysql_clean($filename) . "','" . $user_data['username'] . "')
		   	   	";
		   	   	$insert_id = $this->insert($q);
            
			}
	        
	        // ----------------------------------------------------
	        
	        
            return array('success'=>true,'type'=>$check_type);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array('jpg','jpeg','png','gif','zip','pdf','doc','docx','odt','xlsx','csv','xltx','xml','xls','ods','txt','rtf');

// max file size in bytes
$sizeLimit = 10485760; // 10 Mb

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

$path = PATH . "/generated/";
$result = $uploader->handleUpload($path);

// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
exit;


function check_type($ext) {
	$lowerext = strtolower($ext);
	// Uploading an image
	if ($lowerext == 'jpg' || $lowerext == 'jpeg' || $lowerext == 'png' || $lowerext == 'gif' || $lowerext == 'tif' || $lowerext == 'tiff') {
		return 'img';
	}
	// Uploading a file
	else {
		return 'file';
	}
}

?>