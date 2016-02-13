<?php
# YouTube PHP class
# used for embedding videos as well as video screenies on web page without single line of HTML code
# and for upload video to youtube 
# author Oleg Dikusar.

class YouTube {

   var $id = NULL;	
   var $HOSTED_OR_GOOGLE= 'HOSTED';
   var $YOUTUBE_EMAIL = 'BPONGNation';
   var $YOUTUBE_PASS = "<ups4Et9aW";
   var $API_NAME = 'bpong.com';
   var $YOUTUBE_USERNAME = 'BPONGNation';	
   var $API_KEY = 'AI39si66dS8yRhoEfsRG9mAIrJqtUD-hqYTDmJ34D3jYY3pAeOxe36UqexIjKRQB6vNJTbwCgx_BPfKXUNVcuGSRt3j7e652uw';
   var $AUTH_TOKEN = '';
   
   /* SHAKURO TEST ACCOUNT
   var $id = NULL;	
   var $HOSTED_OR_GOOGLE= 'HOSTED';
   var $YOUTUBE_EMAIL = 'shakurotest';
   var $YOUTUBE_PASS = "shakurotest";
   var $API_NAME = 'shakurotest';
   var $YOUTUBE_USERNAME = 'shakurotest';	
   var $API_KEY = 'AI39si6CGlie_Cb5SPqCsna_Rb03MGrjsKShtdbGUe0OPAR8oaGomI8T6L_76cq4UJlxzbcTITCnTgeL8IGJTgYZodHEb11V9w';
   var $AUTH_TOKEN = '';
   */
	

	/**
	 * Constructor
	 *
	 * This is the default constructor which accepts YouTube URL in any of most commonly used forms.
	 *
	 * @access protected
	 * @param string $url YouTube URL in any of most commonly used forms. Can be ommited (defaults to null),
	 *  but you will have to use setID to set ID explicitly
	 * @see setID
	 */

	function __construct($url = null) {
		if ($url != null) {
			$this->id = YouTube::parseURL($url);
		}
	}

	/**
	 * Set YouTube ID explicitly
	 *
	 * This method sets YouTube ID explicitly. It checks if the ID is in good format. If yes it will set it
	 * and return true, and if not - it will return false
	 *
	 * @access public
	 * @param string $id YouTube ID
	 * @return boolean Whether the ID has been set successfully
	 */

	function setID($id) {
		if (preg_match('/([A-Za-z0-9_-]+)/', $url, $matches)) {
			$this->id = $id;
			return true;
		}
		else
			return false;
	}

	/**
	 * Get string representation of YouTube ID
	 *
	 * This method returns YouTube video ID if any. Otherwise returns null.
	 *
	 * @access public
	 * @return string YouTube video ID if any, otherwise null
	 */

	function getID() {
		return $this->id;
	}

	/**
	 * Parse YouTube URL and return video ID.
	 *
	 * This method sreturnns YouTube video ID if any. Otherwise returns null.
	 *
	 * @access public
	 * @static
	 * @param string $url URL of YouTube video in any of most commonly used forms
	 * @return string YouTube video ID if any, otherwise null
	 */

	function parseURL($url) {
		if (preg_match('/watch\?v\=([A-Za-z0-9_-]+)/', $url, $matches))
			return $matches[1];
		else
			return false;
	}

	    // get URL from embed object
	function getCheckingEmbedID($object) {
		return $this->getEmbedID($object);
	}

    // get URL from embed object
	public static function getEmbedID($object) {
		if (preg_match('/http:\/\/www.youtube.com\/v\/([A-Za-z0-9\-_]+)/i', $object, $matches)) {
			return $matches[1];
		}	elseif(preg_match('/http:\/\/www.youtube.com\/embed\/([A-Za-z0-9\-_]+)/i', $object, $matches)) {
			return $matches[1];	
		}	elseif(preg_match('/http:\/\/youtu.be\/([A-Za-z0-9\-_]+)/i', $object, $matches)) {
			return $matches[1];	
		}	elseif(preg_match('/http:\/\/www.youtube.com\/watch\?v=([A-Za-z0-9\-_]+)/i', $object, $matches)) {
			return $matches[1];	
		} else {
			return false;
		}
	}
	/**
	 * Get YouTube video HTML embed code
	 *
	 * This method returns HTML code which is used to embed YouTube video in page
	 *
	 * @access public
	 * @param string $url YouTube video URL. If this cannot be parsed it will be used as video ID. It can be omitted
	 * @param integer $width Width of embedded video, in pixels. Defaults to 425
	 * @param integer $height Height of embedded video, in pixels. Defaults to 344
	 * @return string HTML code which is used to embed YouTube video in page
	 */

	function EmbedVideo($url = null, $width = 425, $height = 344) {
		if ($url == null)
			$videoid = $this->id;
		else
		{
			$videoid = YouTube::parseURL($url);
			if (!$videoid) $videoid = $url;
		}

		return '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$videoid.'?rel=0&fs=1&loop=0"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/'.$videoid.'?rel=0&fs=1&loop=0" allowfullscreen="true" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object>';
	}

	/**
	 * Get URL of YouTube video screenshot
	 *
	 * This method returns URL of YouTube video screenshot. It can get one of three screenshots defined by YouTube
	 *
	 * @access public
	 * @param string $url YouTube video URL. If this cannot be parsed it will be used as video ID. It can be omitted
	 * @param integer $imgid Number of screenshot to be returned. It can be 1, 2 or 3
	 * @return string URL of YouTube video screenshot
	 */

	public function GetImgURL($url = null, $imgid = 1) {
		if ($url == null)
			$videoid = $this->id;
		else
		{
			$videoid = YouTube::parseURL($url);
			if (!$videoid) $videoid = $url;
		}

		return "http://img.youtube.com/vi/$videoid/$imgid.jpg";
	}

	/**
	 * Get URL of YouTube video screenshot
	 *
	 * This method returns URL of YouTube video screenshot. It can get one of three screenshots defined by YouTube
	 * DEPRECATED! Use GetImgURL instead.
	 *
	 * @deprecated
	 * @see GetImgURL
	 * @access public
	 * @param string $url YouTube video URL. If this cannot be parsed it will be used as video ID. It can be omitted
	 * @param integer $imgid Number of screenshot to be returned. It can be 1, 2 or 3
	 * @return string URL of YouTube video screenshot
	 */

	function GetImg($url = null, $imgid = 1) {
		return GetImgURL($url, $imgid);
	}

	/**
	 * Get YouTube screenshot HTML embed code
	 *
	 * This method returns HTML code which is used to embed YouTube video screenshot in page
	 *
	 * @access public
	 * @param string $url YouTube video URL. If this cannot be parsed it will be used as video ID
	 * @param integer $imgid Number of screenshot to be returned. It can be 1, 2 or 3
	 * @param string $alt Alternate text of the screenshot
	 * @return string HTML code which embeds YouTube video screenshot
	 */

	function ShowImg($url = null, $imgid = 1, $alt = 'Video screenshot') {
		return "<img src='".$this->GetImgURL($url, $imgid)."' width='130' height='97' border='0' alt='".$alt."' title='".$alt."' />";
	}
	
	function getAuthToken () {
        $data = "accountType=HOSTED_OR_GOOGLE&Email=" . $this->YOUTUBE_EMAIL . "&Passwd=" . $this->YOUTUBE_PASS . "&service=youtube&source=" . $this->API_NAME;		    
     	
        $ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/youtube/accounts/ClientLogin');
      	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      	curl_setopt($ch, CURLOPT_HEADER, 1);
      	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/x-www-form-urlencoded")); 
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       	curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        preg_match("!(.*?)Auth=(.*?)\n!si", $result, $ok);
        
        if (empty($ok[2])) {
            return false;        
        } else {
            return $this->AUTH_TOKEN = $ok[2];        
        }   
	}
	
	
	function __getUploadUrlResponse($title, $description, $category = 'People', $keywords = '') {
        if (!$this->getAuthToken()) {
            return false;        
        }	 
        $data = '<?xml version="1.0"?>
            <entry xmlns="http://www.w3.org/2005/Atom"
              xmlns:media="http://search.yahoo.com/mrss/"
              xmlns:yt="http://gdata.youtube.com/schemas/2007">
              <media:group>
                <media:title type="plain">' . $title . '</media:title>
                <media:description type="plain">
                  ' . $description . '
                </media:description>
                <media:category
                  scheme="http://gdata.youtube.com/schemas/2007/categories.cat">' . $category . '
                </media:category>
                <media:keywords>' . $keywords . '</media:keywords>
              </media:group>
            </entry>';
     	
        $ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, 'http://gdata.youtube.com/action/GetUploadToken');
      	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      	curl_setopt($ch, CURLOPT_HEADER, 1);
      	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          	"Content-Type: application/atom+xml; charset=UTF-8", 
          	"Content-Length: " . strlen($data), 
          	"Authorization: GoogleLogin auth=" . $this->AUTH_TOKEN,
          	"X-GData-Client: " . $this->API_NAME,
          	"X-GData-Key: key=" . $this->API_KEY
      	)); 
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       	curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        
        preg_match("!<url>(.*?)<\/url>!si",$response, $url);
        preg_match("!<token>(.*?)<\/token>!si",$response, $token);  
        if (empty($url[1]) || empty($token[1])) {
            return false;    
        } else {
            $result['url'] = $url[1];
            $result['token'] = $token[1];
            return $result;
        }
    }
		
	function getUploadUrl($title, $description, $category = 'People', $keywords = '', $iterations = 3) {
	    $i = 1;
	    $res = array();
	    $brake = 0;
	    while ($brake == 0) {
	        $res = $this->__getUploadUrlResponse($title, $description, $category, $keywords);
	        if (!empty($res)) {
                $brake = 1;	        
	        } elseif ($i == $iterations) {
                $brake = 1;    
            }
            $i++;
        }
        return $res;
	 }    
	 // delete video from youtube
	 function deleteVideo($videoID) {
            if (!$this->getAuthToken()) {
                return false;        
            }	     
            $url = 'http://gdata.youtube.com/feeds/api/users/' . $this->YOUTUBE_USERNAME . '/uploads/' . $videoID;	     
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              	"Content-Type: application/atom+xml", 
              	"Authorization: GoogleLogin auth=" . $this->AUTH_TOKEN,
              	"X-GData-Client: " . $this->API_NAME,
              	"X-GData-Key: key=" . $this->API_KEY,
				"GData-Version: 2"         	
          	)); 						
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			$output = curl_exec($ch);
			$result['err'] = curl_errno( $ch );
			$result['errmsg']  = curl_error( $ch );
			$result['header']  = curl_getinfo( $ch );

			curl_close($ch);
            return $result;  	     
	 }
	 
	 ////////////////////////////////DOWNLOAD//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 // download video from youtube
	 // get functions from Author:        Andrew Gee Email:        info@dumaguetewebdesign.com Website:    http://www.dumaguetewebdesign.com
	 // if not vorking in future, we can download own video form url 'http://www.youtube.com/download_my_video?v='.$videoId  (but only for loggined)
    
	 function download_video ($id, $fileName){
	     if (file_exists($fileName . '.flv')) {
            @unlink($fileName . '.flv');
	     }
        $data = $this->download_get_html($id);
        if($flv = $this->download_get_flv($data)) {
            return $this->download_get_file($flv, $fileName . '.flv');
        } else {
        	return false;
        }
    }
    
    function download_get_file($url, $filename) {
        $file = fopen($filename, 'wb');
        if (!$file) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE);
        //curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE);
        curl_exec($ch);
        curl_close($ch);
        fclose($file);
        return(true);
    }
    // get flv url
    function download_get_flv($data) {
        //After &fmt_url_map= //Before & //Split by %2C //Select first //After %7C
        if(eregi('fmt_url_map',$data)) {
            $data = end(split('&fmt_url_map=',$data));
            $data = current(split('&',$data));
            $split = explode('%2C',$data);
            $data = $split[0];
            $data = end(split('%7C',$data));
            return(urldecode($data));
        } else {
            //if(eregi('verify-age-details',$data)) {
            //    echo 'Age verification needed<br>';
            //}
            return(false);
        }
    }
    // 
    function download_get_html($id) {
    	$url = 'http://www.youtube.com/watch?v=' . $id;
        $ch = curl_init();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; //browsers keep this blank.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows;U;Windows NT 5.0;en-US;rv:1.4) Gecko/20030624 Netscape/7.1 (ax)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_REFERER, "http://www.youtube.com/");
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE);
        //curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE);
        $result = curl_exec ($ch);
        if (!$result) {
            //echo "cURL error number:" .curl_errno($ch);
            //echo "cURL error:" . curl_error($ch);
            //exit;
            curl_close ($ch);
            return false;
        }
        curl_close ($ch);
        return($result);
    }
	 ////////////////////////////////EOF DOWNLOAD///////////////////////////////////////////////////////////////////////////////////////////////////////////// 
}
?>
