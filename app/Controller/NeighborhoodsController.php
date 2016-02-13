<?php
class NeighborhoodsController extends AppController
{
    var $name = 'Neighborhoods';
    var $uses = array('Neighborhood');
      
    function m_getNeighborhoods($city_id) 
    {
        if (isset($this->request->params['form']['city_id'])) {
            $city_id = $this->request->params['form']['city_id']; 
        }
          
        $result = $this->Neighborhood->find(
            'all', array(
            'conditions'=>array('city_id'=>$city_id),
            'fields'=>array('id','name'))
        );
        return $this->returnJSONResult($result);
    }
    function m_getNeighborhoodByID($id) 
    {
        if (isset($this->request->params['form'])) {
            $id = $this->request->params['form']['id']; 
        }
        $result = $this->Neighborhood->find(
            'first', array(
            'conditions'=>array('id'=>$id))
        );
        return $this->returnJSONResult($result);
    }             
    function m_getNeighborhoodsByGeo($lat,$lng,$radius,$limit = 10) 
    {
        if (!empty($this->request->params['form']['lat'])) { 
            $lat = $this->request->params['form']['lat']; 
        }
        if (!empty($this->request->params['form']['lng'])) { 
            $lng = $this->request->params['form']['lng']; 
        }
        if (!empty($this->request->params['form']['radius'])) { 
            $radius = $this->request->params['form']['radius']; 
        }
              
        $result = $this->Neighborhood->returnGeo($lat, $lng, $radius, 'neighborhoods', $limit = 10);
        return $this->returnJSONResult($result);
    }
}
?>
