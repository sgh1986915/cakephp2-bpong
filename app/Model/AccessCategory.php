<?php
class AccessCategory extends AppModel
{
    
     var $name = 'AccessCategory';
    
    /**
     * Getting categories with security objects cnt
     * @author vovich
     * @return array
     */
    function getAccessCategories() 
    {

        $sql = "SELECT access_categories.id, access_categories.name, count(objects.id) as cnt FROM ".$this->tablePrefix."access_categories AS access_categories 
        		LEFT JOIN ".$this->tablePrefix."objects AS objects
        		ON  objects.category_id = access_categories.id GROUP BY id";
        
        $result = $this->query($sql);
        
        return $result; 
        
    }
    
    /**
     * Returns count of objects by current access category
     * @param $categoryID
     * @return int
     */
    function GetObjectsCnt($categoryID = null) 
    {

             $sql = "SELECT count(id) as cnt FROM ".$this->tablePrefix."objects AS objects 
        		       WHERE category_id = ".$categoryID;
        
             $result = $this->query($sql);
             
        if(empty($result)) {
            return 0;
        } else {
            return $result[0][0]['cnt'];
        }
    }
}
?>