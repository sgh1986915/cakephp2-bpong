<?php
class Package extends AppModel
{

    var $name      = 'Package';
    var $recursive = -1;

       var $hasMany = array(
                                           'Packagedetail' => array(
                                             'className'  => 'Packagedetail'
                                            ,'order'      => ''
                                            ,'foreignKey' => 'package_id'
                                            ,'dependent'  => 'false'
                                            ,'conditions' => array('Packagedetail.is_deleted'=>0),
                                           )//array
        );
        
        var $hasAndBelongsToMany = array(
        
            'User' => array('className' => 'User',
                'joinTable' => 'packages_users',
                'foreignKey' => 'package_id',
                'associationForeignKey' => 'user_id',
                'unique' => true,           
              )

        );

        /**
         * Get count of package Details For the current model
         * @author vovich
         * @param string $modelName - model name
         * @param int    $modelID   - id of the model
         * @return int  count of details
         */
        function cntPackagesDetails($modelName=null,$modelID=null)
        {

            $sql = "SELECT count(*) as cnt FROM packages  packages INNER JOIN packagedetails  packagedetails ON  packages.model='".$modelName."'
        							 AND  packages.model_id=".$modelID."  AND packages.id=packagedetails.package_id   AND packagedetails.is_deleted=0 AND packages.is_deleted=0
        			 WHERE 	packagedetails.start_date <= '".date('Y-m-d')."' AND 	(packagedetails.end_date >= '".date('Y-m-d')."' OR packagedetails.end_date IS NULL OR LENGTH(TRIM(packagedetails.end_date)) = 0)
        							 ";
            $result  = $this->query($sql);
            if (!empty($result ) ) {
                $result = $result[0][0]['cnt'];
            } else {
                $result = 0;
            }
            return $result;

        }

        /**
         * Get count of package Details For the current model
         * @author vovich
         * @param $userID - user id - for hidden packages
         * @param string                                 $modelName - model name
         * @param int                                    $modelID   - id of the model
         * @return int  count of details
         */
        function packagesList($userID = null, $modelName = null,$modelID = null,$dateString = null, $price = 0, $packageType = 'all')
        {

            if (!$dateString) {
                   $date = date('Y-m-d');
            } else {
                $date = strtotime($dateString);
                $date = date('Y-m-d', $date);
            }
                        
                        $ModelObject = ClassRegistry::init($modelName);
            $ModelObject->recursive = -1;
            $model = $ModelObject->find('first', array('conditions' => array($modelName.'.id' => $modelID)));
                        
            $priceCondition = '';                        
                        
            if ($price > 0) {
                if ($packageType == 'all') {
                    $priceCondition = " AND (packagedetails.price >= " . $price . "	OR packagedetails.price_team >= " . $price . ") ";
                } elseif ($packageType == 'team') {
                    $priceCondition = " AND packagedetails.price_team >= " . $price . " ";
                } elseif ($packageType == 'personal') {
                    $priceCondition = " AND packagedetails.price >= " . $price . " ";
                }     
            }
                        
                        $result = array();
                        $sql = "SELECT *  FROM packages  packages 
				        		INNER JOIN packagedetails  packagedetails ON  packages.model='".$modelName."' AND  packages.model_id=".$modelID."  AND packages.id=packagedetails.package_id   AND packagedetails.is_deleted=0 AND packages.is_deleted<>1
				        		WHERE packagedetails.start_date <= '".$date."' AND 	(packagedetails.end_date >= '".$date."' OR packagedetails.end_date IS NULL OR LENGTH(TRIM(packagedetails.end_date)) = 0)
				        							 " . $priceCondition." ORDER BY packages.order ASC";
                        $packages  = $this->query($sql);
            if (!empty($packages)) {
                foreach ($packages as $package) {
                                
                    if (empty($package['packages']['is_hidden']) || $this->PackagesUser->find('count', array('conditions'=>array('user_id' => $userID,'package_id' => $package['packages']['id'] ))) > 0 ) {  
                                $result[$package['packages']['id']]['id'] = $package['packages']['id'];
                                $result[$package['packages']['id']]['price'] = $package['packagedetails']['price'];
                                $result[$package['packages']['id']]['price_team'] = $package['packagedetails']['price_team'];
                                $result[$package['packages']['id']]['deposit'] = $package['packagedetails']['deposit'];
                                                                                                                                    
                        $result[$package['packages']['id']]['info'] = $package['packages']['name'];
                        if ($packageType != 'team' && $package['packagedetails']['price'] > 0 && (!$price || $package['packagedetails']['price'] >= $price )) {
                            $result[$package['packages']['id']]['info'].="<br/>Price per person: $".sprintf("%01.2f", $package['packagedetails']['price']);                                                
                        }
                        if (!intval($package['packagedetails']['price_team']) && !empty($model[$modelName]['people_team']) && $model[$modelName]['people_team'] == $package['packages']['people_in_room'] && $package['packages']['people_in_room'] > 1) {
                            $result[$package['packages']['id']]['price_team'] = $package['packagedetails']['price_team'] = $package['packagedetails']['price'] * $model[$modelName]['people_team'];    
                                                
                        }
                                            
                        if ($packageType != 'personal' && $package['packagedetails']['price_team'] > 0 && $package['packagedetails']['price_team'] != $package['packagedetails']['price']) {
                            $result[$package['packages']['id']]['info'].="<br/>Price per team: $".sprintf("%01.2f", $package['packagedetails']['price_team']);                                                
                        }                                            
                                $result[$package['packages']['id']]['info'].= "<br/>";
                        if (!empty($package['packagedetails']['deposit']) && floatval($package['packagedetails']['deposit'])>0 && !$price) {
                            $result[$package['packages']['id']]['info'] .= "Deposit: $".$package['packagedetails']['deposit']. "<br/>";
                        }
                                    $result[$package['packages']['id']]['info'] .= $package['packages']['description'] ;
                                    
                    }                 
                }
            }        
            return $result;

        }
        /**
         *    Get cheepest package ID - for Free promocodes
         *  @author vovich
         *  @param string $modelName  - model name
         *  @param int    $modelID    - id of the model
         *  @param string $dateString - date for checking cheepest  package if NULL then getting current date
         *  @return array
         */
        function getCheepesPackage($modelName=null,$modelID=null,$dateString = null) 
        {
            //making data
            if (!$dateString) {
                   $date = date('Y-m-d');
            } else {
                $date = strtotime($dateString);
                $date = date('Y-m-d', $date);
            }

               $sql = "SELECT  *  FROM packages  packages INNER JOIN packagedetails  packagedetails ON  packages.model='".$modelName."'
	        							 AND  packages.model_id=".$modelID."  AND packages.id=packagedetails.package_id   AND packagedetails.is_deleted<>1 AND packages.is_deleted=0
	        			 WHERE 	packages.is_hidden <> 1 AND packagedetails.start_date <= '".$date."' AND  ( packagedetails.end_date >= '".$date."' OR packagedetails.end_date IS NULL OR LENGTH(TRIM(packagedetails.end_date)) = 0 )
	        			 ORDER BY packagedetails.price ASC LIMIT 1 ";
               $packages  = $this->query($sql);
            if (empty($packages)) {
                return 0;
            } else {
                $packages = $packages[0];
                $modelName = $packages['packages']['model'];
                $modelID = $packages['packages']['model_id'];
                
                $ModelObject = ClassRegistry::init($modelName);
                $ModelObject->recursive = -1;
                $model = $ModelObject->find('first', array('conditions' => array($modelName.'.id' => $modelID)));          
                
                
                if (!intval($packages['packagedetails']['price_team']) && !empty($model[$modelName]['people_team']) && $model[$modelName]['people_team'] == $packages['packages']['people_in_room'] && $packages['packages']['people_in_room'] > 1) {
                    $packages['packagedetails']['price_team'] = $packages['packagedetails']['price'] * $model[$modelName]['people_team'];                                                    
                }
                return $packages;
            }


        }

        /**
 * Get  package Details For the current package ID for the current date
 * @author vovich
 * @param int $packagelID
 * @return array
 */
        function packagDetails($packageID=null,$dateString = null)
        {
            $result = array();
            if (!$dateString) {
                   $date = date('Y-m-d');
            } else {
                $date = strtotime($dateString);
                $date = date('Y-m-d', $date);
            }

            $sql = "SELECT *  FROM packages  packages INNER JOIN packagedetails  packagedetails ON
        							  packages.id=".$packageID."  AND packages.id=packagedetails.package_id   AND packagedetails.is_deleted=0 AND packages.is_deleted=0
        			 WHERE 	packagedetails.start_date <= '".$date."' AND (packagedetails.end_date >= '".$date."'  OR packagedetails.end_date IS NULL OR LENGTH(TRIM(packagedetails.end_date)) = 0)
        							 ";
            $packages  = $this->query($sql);
            if (!empty($packages)) {
                $packages = $packages[0];
                $modelName = $packages['packages']['model'];
                $modelID = $packages['packages']['model_id'];
            
                $ModelObject = ClassRegistry::init($modelName);
                $ModelObject->recursive = -1;
                $model = $ModelObject->find('first', array('conditions' => array($modelName.'.id' => $modelID)));          
            
            
                if (!intval($packages['packagedetails']['price_team']) && !empty($model[$modelName]['people_team']) && $model[$modelName]['people_team'] == $packages['packages']['people_in_room'] && $packages['packages']['people_in_room'] > 1) {
                    $packages['packagedetails']['price_team'] = $packages['packagedetails']['price'] * $model[$modelName]['people_team'];                                                    
                }
            }                                                
            return $packages;

        }

        /**
* 
         * Get  package name 
         * @author Povstyanoy
         * @param int $user_id
         * @param int $model 
         * @param int $model_id
         * @return string Name of the package
         */
        function getPackageName($user_id = null, $model = null, $model_id = null)
        {
            $user_id = (int)$user_id;
            $model_id = (int)$model_id;
          
            if (empty($user_id) || empty($model) || empty($model_id)) {
                return "";
            }

            $sql = "SELECT Package.name
					FROM signups AS Signup
					LEFT JOIN packagedetails AS Packagedetail ON Signup.packagedetails_id = Packagedetail.id
					LEFT JOIN packages AS Package ON Packagedetail.package_id = Package.id
					WHERE Signup.user_id = $user_id
						AND Signup.model = '$model'
						AND Signup.model_id = $model_id;";
            
            $packagename  = $this->query($sql);
            if (empty( $packagename )) {
                return ""; 
            }

            return $packagename[0]['Package']['name'];

        }
        function isFreeEvent($userID, $modelName, $modelID) 
        {
            $isFree = 1;
            $packages = $this->packagesList($userID, $modelName, $modelID);          
            foreach ($packages as $package) {
                if ($package['price'] > 0 || $package['price_team'] > 0) {
                    $isFree = 0;
                    break;    
                }    
            } 
            return $isFree;
        }
        
}
?>