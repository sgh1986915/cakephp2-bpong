<?php
/**
 * Import few models
 */
App::import('Model', 'Forumbranch');
App::import('Model', 'Forumtopic');

/**
 * Generate forum navigation links
 * @author Povstyanoy
 *
 * @tutorial
 *  <div class="grit">
        <ul>
            <li><a href="#">Forum</a>
            </li>
            <li>
                <a href="#">Bpong</a>
            </li>
            <li>
                <a href="#">WSOBP</a>
            </li>
            <li>
                Questions
            </li>
        </ul>
    </div>
 *
 *
 */

class ForumlinksHelper extends AppHelper {

    var $helpers = array('Html');

    var $uses = array('Forumbranch');

	/**
	 * @var string Contains string delimiter
	 */
    var $delimiter = ' <img src="/img/raquo.gif" alt=">>" /> ';

    function last_title( $slugs = "") {
		if ( empty( $slugs ) ) {
			return "";
		}

		//$parameters = $objBranch->findIdBySlug( $slugs );

		$last_slug = array_pop( $slugs );

		$objTopic = new Forumtopic();
		$objTopic->contain();
		$topicname = $objTopic->find( "first", array('conditions' => array('slug' => $last_slug)) );
		if ( !empty( $topicname['Forumtopic']['name'] ) ) {
			return ": " . $topicname['Forumtopic']['name'];
		}

		$objBranch = new Forumbranch();
		$objBranch->contain();
		$forumname = $objBranch->find( "first", array('conditions' => array('slug' => $last_slug)) );
		if ( !empty( $forumname['Forumbranch']['name'] ) ) {
			return ": " . $forumname['Forumbranch']['name'];
		}

		return "";

	}

    /**
     * Generate navigation trough branches
     *
     * @param int $branch_id
     * @param int $topic_id
     * @return string Html code
     */

	function generateforumlinks( $slugs = null ) {

    	$fl_begin = '<div class="grit">
    					<ul>
    				';
    	$fl_end = '</ul></div>';
    	$forumlinks = $fl_begin . "<li>" . $this->Html->link( "Forum", array('controller' => 'forumbranches', 'action' => 'index'), array(), false, false ) . "</li>";

    	$objBranch = new Forumbranch();
		$arraylinks = array();


		//$parameters = $objBranch->findIdBySlug($slugs);

		$last_slug = array_pop ($slugs);

		$objTopic = new Forumtopic();
		$objTopic->contain();
		$topic = $objTopic->find( 'first', array ( 'conditions' => array ( 'slug' => $last_slug ) ) );

		if(!empty($topic['Forumtopic']['name'])) {
			$add_to_links = "<li>" . $topic['Forumtopic']['name'] . "</li>";
			$last_slug = array_pop ($slugs);
		}

		$objBranch->contain();
		$forum = $objBranch->find( 'first', array ( 'conditions' => array ( 'slug' => $last_slug ) ) );
		$forum_path = $objBranch->getpath ( $forum ['Forumbranch'] ['id'] );

		if (empty($forum_path)) {
			return "";
		}

		$slugs = array();
		$slugs[0] = $forum_path[0]['Forumbranch']['slug'];

		foreach($forum_path as $index => $branch) {
			if ( $index != 0 ) $slugs[ $index ] = $slugs [ $index - 1 ] . "/" . $branch['Forumbranch']['slug'];
			if (empty( $topic['Forumtopic']['name'])) {
				if ($index == count($forum_path) - 1 ) {
					array_push( $arraylinks , "<li>" . $branch['Forumbranch']['name']  . "</li>" );
				} else {
					array_push( $arraylinks , "<li>" . $this->Html->link( $branch['Forumbranch']['name'], array('controller'=>'forumbranches', 'action' => 'index', $slugs[ $index ]), array(), false, false ) . "</li>");
				}
			} else {
				array_push( $arraylinks , "<li>" . $this->Html->link( $branch['Forumbranch']['name'], array('controller'=>'forumbranches', 'action' => 'index', $slugs[ $index ]), array(), false, false ) . "</li>");
			}
		}

		if ( !empty( $topic['Forumtopic']['name'] ) ) {
			//last topic is not a link
			array_push( $arraylinks, $add_to_links);
		}

		$forumlinks .=  implode("", $arraylinks);
		$forumlinks .= $fl_end;

		return $forumlinks;
	}

	function generate_link_and_title( $slugs = null ) {
    	if ( empty( $slugs ) ) {
    		return array( 'links' => '', 'pagetitle' => '' );
    	}

		$fl_begin = '<div class="grit">
    					<ul>
    				';
    	$fl_end = '</ul></div>';
    	$forumlinks = $fl_begin . "<li>" . $this->Html->link( "Forum", array('controller' => 'forumbranches', 'action' => 'index'), array(), false, false ) . "</li>";
		$page_title = "";

    	$objBranch = new Forumbranch();
		$arraylinks = array();


		//$parameters = $objBranch->findIdBySlug($slugs);

		$last_slug = array_pop ($slugs);

		$objTopic = new Forumtopic();
		$objTopic->contain();
		$topic = $objTopic->find( 'first', array ( 'conditions' => array ( 'slug' => $last_slug ) ) );

		if(!empty($topic['Forumtopic']['name'])) {
			$add_to_links = "<li>" . $topic['Forumtopic']['name'] . "</li>";
			$last_slug = array_pop ($slugs);
			$page_title = ": " . $topic['Forumtopic']['name'];
		}

		$objBranch->contain();
		$forum = $objBranch->find( 'first', array ( 'conditions' => array ( 'slug' => $last_slug ) ) );
		if ( !empty( $forum['Forumbranch']['name'] ) && $page_title == "" ) {
			$page_title = ": " . $forum['Forumbranch']['name'];
		}

		$forum_path = $objBranch->getpath ( $forum ['Forumbranch'] ['id'] );

		if (empty($forum_path)) {
			return "";
		}

		$slugs = array();
		$slugs[0] = $forum_path[0]['Forumbranch']['slug'];

		foreach($forum_path as $index => $branch) {
			if ( $index != 0 ) $slugs[ $index ] = $slugs [ $index - 1 ] . "/" . $branch['Forumbranch']['slug'];
			if (empty( $topic['Forumtopic']['name'])) {
				if ($index == count($forum_path) - 1 ) {
					array_push( $arraylinks , "<li>" . $branch['Forumbranch']['name']  . "</li>" );
				} else {
					array_push( $arraylinks , "<li>" . $this->Html->link( $branch['Forumbranch']['name'], array('controller'=>'forumbranches', 'action' => 'index', $slugs[ $index ]), array(), false, false ) . "</li>");
				}
			} else {
				array_push( $arraylinks , "<li>" . $this->Html->link( $branch['Forumbranch']['name'], array('controller'=>'forumbranches', 'action' => 'index', $slugs[ $index ]), array(), false, false ) . "</li>");
			}
		}

		if ( !empty( $topic['Forumtopic']['name'] ) ) {
			//last topic is not a link
			array_push( $arraylinks, $add_to_links);
		}

		$forumlinks .=  implode("", $arraylinks);
		$forumlinks .= $fl_end;

		return array( 'links' => $forumlinks, 'pagetitle' => $page_title );
	}

	function generate_link_and_title_for_posts( $topicname = "", $lft = null, $rght = null ) {
		$fl_begin = '<div class="grit">
    					<ul>
    				';
    	$fl_end = '</ul></div>';
    	$forumlinks = $fl_begin . "<li>" . $this->Html->link( "Forum", array('controller' => 'forumbranches', 'action' => 'index'), array(), false, false ) . "</li>";
		$page_title = "";

    	$objBranch = new Forumbranch();
		$arraylinks = array();

    	$objBranch = new Forumbranch();
		$forum_path = $objBranch->getBranchSlugTree ( $lft, $rght );

		if (empty($forum_path)) {
			return "";
		}

		$slugs = array();
		$slugs[0] = $forum_path[0]['Forumbranch']['slug'];

		foreach($forum_path as $index => $branch) {
			if ( $index != 0 ) $slugs[ $index ] = $slugs [ $index - 1 ] . "/" . $branch['Forumbranch']['slug'];
			array_push( $arraylinks , "<li>" . $this->Html->link( $branch['Forumbranch']['name'], array('controller'=>'forumbranches', 'action' => 'index', $slugs[ $index ]), array(), false, false ) . "</li>");
		}

		if ( !empty( $topicname ) ) {
			//last topic is not a link
			array_push( $arraylinks, "<li>$topicname</li>" );
			$page_title = ": " . $topicname;
		}

		$forumlinks .=  implode("", $arraylinks);
		$forumlinks .= $fl_end;

		return array( 'links' => $forumlinks, 'pagetitle' => $page_title );
	}

/*
	function generateforumurls( $slugs = null ) {

    	$objBranch = new Forumbranch();

    	$parameters = $objBranch->findIdBySlug( $slugs );
		$forum_id = $parameters ['Forum'];

    	$forum_path = $objBranch->getpath ( $forum_id );

		if (empty($forum_path))
			return "";

		$slugs = array();
		foreach($forum_path as $index => $branch) {
			$slugs[ $index ] = $branch['Forumbranch']['slug'];
		}

		if ( !empty( $parameters ['Topic'] ) ) {
			$objTopic = new Forumtopic();
			$objTopic->contain();
			$topic = $objTopic->read( 'slug', $parameters ['Topic']);
			//last topic is not a link
			array_push( $slugs, $topic['Forumtopic']['slug']);
		}

		$forumurl =  implode("/", $slugs);
		return $forumurl;
	}
*/

	/**
	 * Generate the sequence of a slug field for nation page!!!
	 *
	 * @author Povstyanoy
	 * @param array $slug
	 * @return string
	 */
	function generatetopicurl( $slug = null ) {

		$objTopic = new Forumtopic();
		$objTopic->contain();
		$topic = $objTopic->find( 'first', array('conditions' => array('Forumtopic.slug' => $slug)));

    	$objBranch = new Forumbranch();
    	$forum_path = $objBranch->getpath ( $topic['Forumtopic']['forumbranch_id'] );

		if (empty($forum_path))
			return "";

		$slugs = array();
		foreach($forum_path as $index => $branch) {
			$slugs[ $index ] = $branch['Forumbranch']['slug'];
		}
		//last topic is not a link
		array_push( $slugs, $slug );

		$forumurl =  implode( "/", $slugs );
		return $forumurl;
	}

	function generate_last_post_urls( $lastpost_id = null ) {

    	$objBranch = new Forumbranch();

    	App::import('Model', 'Forumpost');
    	$objPost = new Forumpost();

    	$objPost->contain( 'Forumtopic' );
    	$lastpost = $objPost->read(null, $lastpost_id);

		$forum_id = $lastpost ['Forumtopic'] ['forumbranch_id'];

    	$forum_path = $objBranch->getpath ( $forum_id );

		if (empty($forum_path))
			return "";

		$slugs = array();
		foreach($forum_path as $index => $branch) {
			$slugs[ $index ] = $branch['Forumbranch']['slug'];
		}

		array_push($slugs, $lastpost ['Forumtopic'] ['slug']);

		return implode("/", $slugs);
	}

	function generate_last_post_url_for_branch ($forumtopic_slug, $lft, $rght) {
    	$objBranch = new Forumbranch();
		$forum_path = $objBranch->getBranchSlugTree ( $lft, $rght );

		if (empty($forum_path))
			return "";

		$slugs = array();

		foreach($forum_path as $index => $branch) {
			$slugs[ $index ] = $branch['Forumbranch']['slug'];
		}

		array_push($slugs, $forumtopic_slug);

		return implode("/", $slugs);
	}

    function unicode_wordwrap($str, $len=50, $break=" ", $cut=false){
        if(empty($str)) return "";

        $pattern="";
        if(!$cut)
            $pattern="/(\S{".$len."})/u";
        else
            $pattern="/(.{".$len."})/u";

        return preg_replace($pattern, "\${1}".$break, $str);
    }

    function cut_long_words_from_post($texttodecode = ""){
        if (empty($texttodecode)) {
            return "";
        }
        $charset = Configure::read("App.encoding");

        $how_much_chars = 80;
        preg_match_all('/[^\s]{' . $how_much_chars . ',}/', $texttodecode, $result, PREG_OFFSET_CAPTURE);
        $accumulated_shift = 0;

        $string_to_replace = array();
        $string_for_replace = array();

        foreach($result[0] as $index => $value) {
            if ( !preg_match('%(?:(?:https?|ftp|file)://|www\.|ftp\.)%', $value[0]) ) {
                /*
                $replacing_string = $this->unicode_wordwrap($value[0], $how_much_chars, "<br />", true);
                //$texttodecode = $this->_mb_substr_replace($texttodecode, $replacing_string, $value[1] + $accumulated_shift, mb_strlen($value[0]) );
                $texttodecode = substr_replace($texttodecode, $replacing_string, $value[1] + $accumulated_shift, mb_strlen($value[0], $charset ));
                $accumulated_shift += mb_strlen($replacing_string, $charset) - mb_strlen($value[0], $charset);
                */
                $string_to_replace[] = $value[0];
                $string_for_replace[]= $this->unicode_wordwrap($value[0], $how_much_chars, "<br />", true);;
            }
        }

        if (!empty($string_to_replace)) {
            $texttodecode = str_replace($string_to_replace, $string_for_replace, $texttodecode);
        }

        return $texttodecode;
    }


    function _mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null) {
        if (extension_loaded('mbstring') === true) {
            $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);

            if ($start < 0) {
                $start = max(0, $string_length + $start);
            } elseif ($start > $string_length) {
                $start = $string_length;
            }
            if ($length < 0) {
                $length = max(0, $string_length - $start + $length);
            } elseif ((is_null($length) === true) || ($length > $string_length)) {
                $length = $string_length;
            }

            if (($start + $length) > $string_length) {
                $length = $string_length - $start;
            }

            if (is_null($encoding) === true) {
                return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length);
            }

            return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
        }

        return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);
    }

}
