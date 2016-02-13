<?php
class Tag extends AppModel
{
    var $name = 'Tag';
    var $recursive = -1;
    
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );
    
    /**
     * Parse the tag
     *
     * @author Oleg D.
     */
    function parseTag($tag) 
    {
                $parseTag = trim(strtolower($tag));
                $parseTag = eregi_replace('[^a-zA-Z0-9 -]', '', $parseTag);    
                 
        return $parseTag;
    }
    /**
     * Parse the tag
     * @author Oleg D.
     */
    function getCloudTags($modelName, $limit = 100) 
    {
        
        $sql = "SELECT COUNT(Tag.id) as size, Tag.id, Tag.tag,Tag.model FROM tags AS Tag
        LEFT JOIN models_tags as ModelsTag ON ModelsTag.tag_id = Tag.id
        GROUP BY Tag
        HAVING Tag.model = '" . $modelName . "'
        LIMIT " . $limit;    
        $findTags = $this->query($sql); 
        
        $tags = array();
        foreach ($findTags as $findTag) {
            $tags[$findTag['Tag']['tag']] = $findTag['0']['size'];
        }    
        $returnTags = $this->formulateTagCloud($tags);
        return $returnTags;
    }
    /**
     * make tag cloud
     */
    function formulateTagCloud($dataSet) 
    {
            asort($dataSet); // Sort array accordingly.

            // Retrieve extreme score values for normalization
            $minimumScore = intval(current($dataSet));
            $maximumScore = intval(end($dataSet));

            // Populate new data array, with score value and size.
            $i = 0;
        foreach ($dataSet as $tagName => $score) {
            $size = $this->getPercentSize($maximumScore, $minimumScore, $score);
            $data[$i] = array('score'=>$score, 'size'=>$size, 'name' => $tagName);
            $i++;
        }
            shuffle($data);
            return $data;
    }

        /*
         *  returns int percentage for current tag.
         */
    function getPercentSize($maximumScore, $minimumScore, $currentValue, $minSize = 90, $maxSize = 200) 
    {
        if ($minimumScore < 1) { $minimumScore = 1; 
        }
          $spread = $maximumScore - $minimumScore;
        if ($spread == 0) { $spread = 1; 
        }
          // determine the font-size increment, this is the increase per tag quantity (times used)
          $step = ($maxSize - $minSize) / $spread;
          // Determine size based on current value and step-size.
          $size = $minSize + (($currentValue - $minimumScore) * $step);
          return $size;
    }

    /*
     *  returns shuffled array of tags for randomness.
     */
    function shuffleTags($tags) 
    {
        while (count($tags) > 0) {
            $val = array_rand($tags);
            $new_arr[$val] = $tags[$val];
            unset($tags[$val]);
        }
        if (isset($new_arr)) {
            return $new_arr; 
        }
    }
}
?>