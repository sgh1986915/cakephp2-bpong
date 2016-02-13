<?php
class Submission extends AppModel
{

    var $name = 'Submission';
    var $useTable = false;

    function getSubmissionsSql($userID) 
    {
        $sql = 
        "(
        SELECT 'Image' as model, views, images.id, images.created, images.modified, images.filename, images.votes_plus, images.votes_minus, images.user_id,  images.name, images.description, '' as code, albums.name as album_name, albums.id as album_id    
        FROM images 
        LEFT JOIN albums ON albums.id = images.model_id 
        WHERE images.user_id = " . $userID . " AND images.is_deleted = 0 AND images.model='Album'
        )  
        UNION  
        (SELECT 'Link' as model, views, id, created, modified, '' as filename, links.votes_plus, links.votes_minus, links.user_id, title as name, description, url as code, '' as album_name, '' as album_id    
        FROM links WHERE  user_id = " . $userID . " AND is_deleted = 0)   
        UNION    
        (SELECT 'Video' as model, views, videos.id, videos.created, videos.modified, youtube_id as filename, videos.votes_plus, videos.votes_minus, videos.user_id, videos.title as name, videos.description, videos.code, albums.name as album_name, albums.id as album_id    
        FROM videos 
        LEFT JOIN albums ON albums.id = videos.model_id        
        WHERE  videos.user_id = " . $userID . " AND videos.is_deleted = 0 AND videos.model='Album')";
        //exit;
        $sqlCount = 
        "SELECT (SELECT count(id) as cnt FROM images WHERE user_id = " . $userID . " AND is_deleted = 0 AND images.model='Album')  
        +
        (SELECT count(id) as cnt FROM links WHERE  user_id = " . $userID . " AND is_deleted = 0)   
        +    
        (SELECT count(id) as cnt FROM videos WHERE  user_id = " . $userID . " AND is_deleted = 0 AND videos.model='Album') as cnt";   
        //$sql = 'select * from submissions';
        //$sqlCount = 'select count(id) as cnt  from submissions';
        $queries['query'] = $sql;  
        $queries['count_query'] = $sqlCount;
  
        return $queries;
    }
    
    function getAllSubmissionsSql($search = '') 
    {
        $imageSearch = $videoSearch = $linkSearch = '';
        if ($search) {
            $imageSearch = " AND (images.name LIKE '%" . addslashes($search) . "%' OR images.description LIKE '%" . addslashes($search) . "')";    
            $videoSearch = " AND (videos.title LIKE '%" . addslashes($search) . "%' OR videos.description LIKE '%" . addslashes($search) . "')";
            $linkSearch = " AND (links.title LIKE '%" . addslashes($search) . "%' OR links.description LIKE '%" . addslashes($search) . "')";        
        }
        $sql = 
        "(
        SELECT 'Image' as model, images.comments, views, images.id, images.created, images.modified, images.filename, images.votes_plus, images.votes_minus, images.user_id,  images.name, images.description, '' as code, albums.name as album_name, albums.id as album_id    
        FROM images 
        LEFT JOIN albums ON albums.id = images.model_id 
        WHERE images.is_deleted = 0 AND albums.model <> 'StoreSlot' AND images.model='Album' " . $imageSearch . ")  
        UNION  
        (SELECT 'Link' as model, links.comments, views, id, created, modified,'' as filename, links.votes_plus, links.votes_minus, links.user_id, title as name, description, url as code, '' as album_name, '' as album_id    
        FROM links WHERE is_deleted = 0  " . $linkSearch . " )   
        UNION    
        (SELECT 'Video' as model, videos.comments, views, videos.id, videos.created, videos.modified, youtube_id as filename, videos.votes_plus, videos.votes_minus, videos.user_id, videos.title as name, videos.description, videos.code, albums.name as album_name, albums.id as album_id    
        FROM videos 
        LEFT JOIN albums ON albums.id = videos.model_id        
        WHERE  videos.is_deleted = 0 AND videos.model='Album'  " . $videoSearch . " )";
        //exit;
        $sqlCount = 
        "SELECT (SELECT count(images.id) as cnt FROM images 
        LEFT JOIN albums ON albums.id = images.model_id 
        WHERE images.is_deleted = 0 AND albums.model <> 'StoreSlot' AND images.model='Album' " . $imageSearch . ")  
        +
        (SELECT count(links.id) as cnt FROM links WHERE is_deleted = 0 " . $linkSearch . ")   
        +    
        (SELECT count(videos.id) as cnt FROM videos WHERE is_deleted = 0 AND videos.model='Album' " . $videoSearch . ") as cnt";   
        //$sql = 'select * from submissions';
        //$sqlCount = 'select count(id) as cnt  from submissions';
        $queries['query'] = $sql;  
        $queries['count_query'] = $sqlCount;
  
        return $queries;
    }
}
?>