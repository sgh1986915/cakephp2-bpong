<?php

$this->set('documentData', array(
    'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));

$this->set('channelData', array(
    'title' => __("Blog"),
    'link' => $this->Html->url('/', true),
    'description' => __("Blog."),
    'language' => 'en-us'));

foreach ($blogposts as $blogpost) {
    $blogpostTime = strtotime($blogpost['User']['created']);

    $blogpostLink = array(
	'controller' => 'blogposts',
	'action' => 'view',
	$blogpost['Blogpost']['slug']);
    // You should import Sanitize
    App::import('Sanitize');
    // This is the part where we clean the body text for output as the description
    // of the rss item, this needs to have only text to make sure the feed validates
    $bodyText = preg_replace('=\(.*?\)=is', '', $blogpost['Blogpost']['description']);
    $bodyText = $this->Text->stripLinks($bodyText);
    $bodyText = Sanitize::stripWhitespace($bodyText);
    $bodyText = $this->Text->truncate($bodyText, 400, array('ending' => '...', 'html' => true));

    echo  $this->Rss->item(array(), array(
	'title' => $blogpost['Blogpost']['title'],
	'link' => $blogpostLink,
	'guid' => array('url' => $blogpostLink, 'isPermaLink' => 'true'),
	'description' =>  $bodyText,
	'dc:creator' => $blogpost['User']['lgn'],
	'pubDate' => $blogpost['Blogpost']['created']));
}
?>