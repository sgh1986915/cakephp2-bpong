<?php
/**
 * Shell task to load  blog images from old bpong and changes paths in db
 *@author Edward
 */
class ImageloadShell extends Shell {
	var $uses = array('Blogpost');
	var $bpongPath = 'http://www.bpong.com';
	var $imagePath;
	
	function main() {
		$this->imagePath = WWW_ROOT.'img'.DS.'blog'.DS;
		$fileListContent = '';
		
		$posts = $this->Blogpost->find('all');
		foreach ( $posts as $post ) {
			//$this->Blogpost->id = $post['Blogpost']['id'];
			unset($post['Blogpost']['slug']);
			unset($post['Blogpost']['title']);
			unset($post['Blogpost']['user_id']);
			unset($post['Blogpost']['created']);
			unset($post['Blogpost']['deleted']);
			$subject = $post['Blogpost']['description'];
			preg_match_all('@<img[^>]*src="([^"]*)"[^>]*>@Usi', $subject, $matches);
			if(!empty($matches[1])) {
				foreach($matches[1] as $img) {
					$oldPath = $img;
					$newPath = $this->imagePath.basename($img);
					$wwwPath = '/img/blog/'.basename($img);
					
					if(preg_match('/img/blog/', $img)) {
						//that link already fixed
						continue;
					}
					
					if (!preg_match('bpong.com', $img) && !preg_match('http://', $img)) { //it's relative path. Let's deal with it separetely
						if($img{0} == '.') {
							$img = $this->bpongPath.ltrim($img,'.');
						} elseif ($img{0} == '/') {
							$img = $this->bpongPath.$img;
						} else {
							$img = $this->bpongPath.'/'.$img;
						}
					}
					if(!@copy($img,$newPath)) {
    					$errors= error_get_last();
    					$this->out("COPY ERROR: ".$errors['message']);
    					$this->out("FILE:".$oldPath);
    					$key = $this->in('Change dp path anyway?', array('y', 'n','q'), 'y');
    						if (low($key) == 'y') {
    							$post['Blogpost']['description'] = str_replace($oldPath, $wwwPath, $post['Blogpost']['description']);
    							$fileListContent .= $oldPath. "\n\r";
    						} elseif(low($key) == 'q') {
    							exit;
    						}
    					
					} else {
    					$this->out($img."\n\r COPY TO \n\r".$newPath);
    					$post['Blogpost']['description'] = str_replace($oldPath, $wwwPath, $post['Blogpost']['description']);
					}
					
					
					
				}
			 $this->Blogpost->save($post);	
			}
			//$this->out(pr($matches));
			//$this->Blogpost->save($post);
		}
		$this->createFile(WWW_ROOT.'files.txt',$fileListContent);	
	}
}
?>