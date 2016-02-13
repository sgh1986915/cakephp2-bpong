<?php
/**
 * Shell task to set slug for all bpogposts
 *@author Edward
 */
class SetblogslugShell extends Shell {
	var $uses = array('Blogpost');

	
	function main() {
		$this->Blogpost->contain();
		$posts = $this->Blogpost->find('all');
		foreach ( $posts as $post ) {
			//$this->Blogpost->id = $post['Blogpost']['id'];
			unset($post['Blogpost']['slug']);
			unset($post['Blogpost']['description']);
			$this->Blogpost->save($post);
			$this->out("Slug for " . $post['Blogpost']['title'] . " generated\n\r");
		}	
		$this->out('Slug generation finished');	
	}
}
?>