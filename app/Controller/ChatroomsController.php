<?php
class ChatroomsController extends AppController
{
    var $name = 'Chatrooms';
    var $uses = array('User','Chatroom','Xmppserver');
    function m_getBestXMPPServer($lat=null, $lng=null, $amf = 0) 
    {
        if (!empty($this->request->params['form']['lat'])) { 
            $lat = $this->request->params['form']['lat']; 
        }
        if (!empty($this->request->params['form']['lng'])) { 
            $lng = $this->request->params['form']['lng']; 
        }
        if (!empty($this->request->params['form']['amf'])) { 
            $amf = $this->request->params['form']['amf']; 
        }
        $chatrooms = $this->Chatroom->returnGeo($lat, $lng, 10000, 'xmppservers', 1);
        return $this->returnMobileResult($chatrooms[0]['hostname'], $amf);    
        return $this->returnMobileResult($chatrooms, $amf);
    }  
    function m_returnXMPPRooms($lat=null, $lng=null, $radius=null,$amf = 0) 
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
        if (!empty($this->request->params['form']['amf'])) { 
            $amf = $this->request->params['form']['amf']; 
        }
        $chatrooms = $this->Chatroom->returnGeo($lat, $lng, $radius, 'chatrooms');    
        return $this->returnMobileResult($chatrooms, $amf);
    }  
      
    function m_createXMPPRoom($lat=null, $lng=null, $xmpp_name=null, $display_name=null, $country=null, $state=null,$amf = 0) 
    {
        if (!empty($this->request->params['form']['lat'])) { 
            $lat = $this->request->params['form']['lat']; 
        }
        if (!empty($this->request->params['form']['lng'])) { 
            $lng = $this->request->params['form']['lng']; 
        }
        if (!empty($this->request->params['form']['xmpp_name'])) {
            $xmpp_name = $this->request->params['form']['xmpp_name']; 
        }
        if (!empty($this->request->params['form']['display_name'])) {
            $display_name = $this->request->params['form']['display_name']; 
        }
        if (!empty($this->request->params['form']['country'])) {
            $country = $this->request->params['form']['country']; 
        }
        if (!empty($this->request->params['form']['state'])) {
            $state = $this->request->params['form']['state']; 
        }
        if (!empty($this->request->params['form']['amf'])) { 
            $amf = $this->request->params['form']['amf']; 
        }
    
        $this->Chatroom->create();
        $this->Chatroom->save(
            array('displayname'=>$display_name,'xmppname'=>$xmpp_name,'state'=>$state,
            'country'=>$country,'latitude'=>$lat,'longitude'=>$lng)
        );
            
        return $this->returnMobileResult('ok', $amf);
    }  
    function m_deleteXMPPRoom($room_name=null,$amf = 0) 
    {
        Configure::write('debug', 0);
        if (!empty($this->request->params['form']['room_name'])) {
            $room_name = $this->request->params['form']['room_name']; 
        }
        if (!empty($this->request->params['form']['amf'])) { 
            $amf = $this->request->params['form']['amf']; 
        }
            
        $chatroom = $this->Chatroom->find('first', array('conditions'=>array('xmppname'=>$room_name)));
        if ($chatroom) {
            $this->Chatroom->delete($chatroom['Chatroom']['id']);
            return $this->returnMobileResult('ok', $amf);
        }
        else {
            return $this->returnMobileResult('Chatroom not found', $amf); 
        }
    } 
}
?>
