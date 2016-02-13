<?php
/*
 * Image Behavior for cakePHP
 *
 * @author Oleg Dikusar
 *
 * @version 1.0
 */

App::import('Model', 'Image');
App::import('Vendor', 'example', array('file' => 'class.upload.php'));

class ImageBehavior extends ModelBehavior
{

    var $settings = null;

    var $error = array();
    var $format='';
    var $possible_formats=array(
    'jpg'=>'',
    'jpeg'=>'',
    'gif'=>'',
    'png'=>''
    );
    /**
  * Behaviors Setup
  * Called before all actions
  *
  * @param  Model $model
  * @param  $config
  * @author Oleg
  */
    function setup(Model $model, $config = array())
    {

        $this->settings=$config;
        if (! isset($this->settings['required'])) {
            $this->settings['required'] = false;
        }
        $this->settings['baseDir']=WWW_ROOT.'img'.DS.$model->name.DS;
        $this->settings['thumbsDir']=WWW_ROOT.'img'.DS.$model->name.DS.'thumbs'.DS;
        $model->bindModel(
            array('hasMany' => array(
                  'Image' => array('className'     => 'Image',
                  'className'     => 'Image',
                  'conditions'    => 'model="'.$model->name.'"',
                  'order'         => '',
                  'limit'         => '',
                  'foreignKey'    => 'model_id',
                  'dependent'     => false,
                  'exclusive'     => false,
                  'finderQuery'   => ''
                  )))
        );
    }

    /**
     * Before save method. Called before all saves
     * @param Model $model
     * @param array $options
     * @return bool True to continue, false to abort the save
     * @author Oleg
     */
    function beforeSave(Model $model, $options = array())
    {
        if(isset($model->data['Image'])) {
            //abort if file not uploaded on the server
            $myImages=$model->data['Image'];
            foreach($myImages as $key=>$myImage){

                if(isset($myImage['size'])&&$myImage['size']>0) {
                    if(!$this->correct_format($myImage)) {
                        $model->Image->invalidate($key, 'Incorrect Image Format.');
                        //$this->log("record is not saved", LOG_DEBUG);
                        return false;
                    }

                }
            }

            $this->settings['images'] = $model->data['Image'];
            unset($model->data['Image']);

        }
        return true;
    }

    /**
     * After save method. Called after all saves
     *
     * @param  Model $model
     * @param bool $created
     * @param array $options
     * @return bool
     * @author Oleg
     */
    function afterSave(Model $model, $created, $options = array())
    {

        if(isset($this->settings['images'])) {
            if(count($this->settings['images'])) {
                foreach($this->settings['images'] as $key=>$value){
                    if($key=='new') {
                        // Add action
                        if(isset($this->settings['images']['new']['size'])&&$this->settings['images']['new']['size']>0) {
                            $this->saveImage($model, $this->settings['images']['new']); 
                        }
                    }else{
                        // Edit action
                        if(isset($this->settings['images'][$key]['size'])&&$this->settings['images'][$key]['size']>0) {
                            $this->saveImage($model, $this->settings['images'][$key], $key); 
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     *    Save Image
     * @param Model $model
     * @param $pict Image object
     * @param $edit_id Image ID (used for edit action)
     * @author Oleg
     * @return bool
     */
    function saveImage(Model $model, $pict, $edit_id=null )
    {

        $foo = new Upload($pict);

        $user_id=$_SESSION['loggedUser']['id'];

        if($edit_id) {
            // If Edit action
            $objImage = new Image();
            $editFile=$objImage->field('filename', 'id='.$edit_id);
            $id=$objImage->field('model_id', 'id='.$edit_id);

            @unlink($this->settings['baseDir'].$editFile);

            if($this->settings['thumbs']['create']) {
                @unlink($this->settings['thumbsDir'].$editFile);
            }

        }else{
            // If Add action

            $id = $model->getLastInsertId();

        }
        if(!$id && isset( $model->{$model->primaryKey} ) && $model->{$model->primaryKey} ) {
            $id = $model->{$model->primaryKey};
        }

        $filename=$this->getNewFilename($pict['name'], $id);
        $foo->file_new_name_body = $filename;
        //$foo->image_convert = 'jpg';

        if ($foo->uploaded) {
            $foo->Process($this->settings['baseDir']);
            if ($foo->processed) {
                if (isset($pict['prop']) && !empty($pict['prop'])) {
                    $prop = $pict['prop'];
                } else {
                    $prop = 'All';
                }
                  $this->ImageTableSave($filename.'.'.$this->format, $model, $id, $prop, $user_id, $edit_id);
            }

            if($this->settings['thumbs']['create']) {
                $foo->file_new_name_body = $filename;
                //$foo->image_convert = 'jpg';
                $foo->image_resize          = true;
                $foo->image_ratio_fill      = 'C';
                if(isset($this->settings['thumbs']['height'])) {
                    $foo->image_y               = $this->settings['thumbs']['height']; 
                }
                if(isset($this->settings['thumbs']['width'])) {
                    $foo->image_x               = $this->settings['thumbs']['width']; 
                }
                if(isset($this->settings['thumbs']['bgcolor'])) {
                    $foo->image_background_color = $this->settings['thumbs']['bgcolor'];
                }else{
                    $foo->image_background_color = '#FFFFFF';
                }
                $foo->Process($this->settings['thumbsDir']);
            }

            if (isset( $this->settings['watermark'] )
                && !empty($this->settings['watermark']['image'])
            ) {
                  //Create watermark bpong
                  $watermarked_image = $this->watermark(
                      WWW_ROOT . "img/" . $model->name . "/$filename".'.'.$this->format, WWW_ROOT . $this->settings['watermark']['image'] 
                  );

                  imagejpeg($watermarked_image, WWW_ROOT . "img/" . $model->name . "/$filename".'.'.$this->format);

                  unset( $watermarked_image );
                  //end of watermark create
            }

            if(isset($this->settings['versions'])&&count(($this->settings['versions']))) {

                foreach($this->settings['versions'] as $version_name=>$version){
                        $foo->file_new_name_body = $filename;
                        //$foo->image_convert = 'jpg';
                        $foo->image_resize          = true;
                        $foo->image_ratio_fill      = 'C';
                    if(isset($version['height'])) {
                        $foo->image_y               = $version['height']; 
                    }
                    if(isset($version['width'])) {
                        $foo->image_x               = $version['width']; 
                    }
                    if(isset($version['bgcolor'])) {
                        $foo->image_background_color = $version['bgcolor'];
                    }else{
                        $foo->image_background_color = '#FFFFFF';
                    }
                        $foo->Process($this->settings['baseDir'].$version_name.DS);
                }
            }
            return true;
        }

        return false;

    }

    /**
     *
     * After Delete
     * @author Oleg
     * @param Model $model
     * @return bool|void
     */
    function afterDelete(Model $model)
    {

        $objImage = new Image();
        $images=$objImage->findAll(array('model'=>$model->name,'model_id'=>$model->id));

        if(count($images)) {
            foreach($images as $image){

                $id=$image['Image']['id'];
                $filename=$image['Image']['filename'];
                @unlink($this->settings['baseDir'].$filename);

                if($this->settings['thumbs']['create']) {
                    @unlink($this->settings['thumbsDir'].$filename);
                }
                $objImage->delete($id);


            }
        }

        return true;
    }

    /**
* 
   * Make water mark
   * @author Oleg
   */
    function watermark( $base_image, $logo ) 
    {
          $size = getimagesize($base_image);
          $IMAGE_WIDTH  = $size[0];
          $IMAGE_HEIGHT = $size[1];

          //Load and resize the image
          $uploaded = imagecreatefromjpeg($base_image);
          $image = imagecreatetruecolor($IMAGE_WIDTH, $IMAGE_HEIGHT);
          imagecopyresampled($image, $uploaded, 0, 0, 0, 0, $IMAGE_WIDTH, $IMAGE_HEIGHT, imagesx($uploaded), imagesy($uploaded));
          imagealphablending($image, true); //allows us to apply a 24-bit watermark over $image

          $size2 = getimagesize($logo);
          $SOLD_WIDTH  = $size2[0];
          $SOLD_HEIGHT = $size2[1];

          //Load the sold watermark
          $sold_band = imagecreatefrompng($logo);
          imagealphablending($sold_band, true);

          //Apply watermark and save
          //$image = image_overlap($image, $sold_band);
          imagecopy($image, $sold_band, $IMAGE_WIDTH - $SOLD_WIDTH, $IMAGE_HEIGHT - $SOLD_HEIGHT, 0, 0, $SOLD_WIDTH, $SOLD_HEIGHT);
          imagedestroy($uploaded);
          imagedestroy($sold_band);
          return $image;
    }

    /**
     * Gernerate Image name
   * @author Oleg
     */
    function getNewFilename($uploadFilename,$id) 
    {
         $Filename=explode(".", $uploadFilename);
         $newFilename=$Filename[0].'_'.$id;
         return $this->escapeFile($newFilename);
    }
      /**
* 
     * Gernerate Image name
   * @author Oleg
     */
    function correct_format($pict) 
    {
        $explodes=explode('.', $pict['name']);
        $ponts=(count($explodes)-1);
        $thisFormat=$explodes[$ponts];
        $thisFormat=strtolower($thisFormat);
        $this->format=$thisFormat;
        if(!isset($this->possible_formats[$thisFormat])) {
            return false;

        }else{
            return true;
        }
    }

    /**
   * Save Data into Image table
   * @author Oleg
   */
    function ImageTableSave($fileName,$model,$id,$prop,$user_id,$edit_id=null)
    {
        $objImage = new Image();

        if($edit_id) {
            $data ['Image'] ['id'] = $edit_id;
        }
        $data ['Image'] ['prop']     = $prop;
        $data ['Image'] ['user_id']  = $user_id;
        $data ['Image'] ['model']    = $model->name;
        $data ['Image'] ['filename'] = $fileName;
        $data ['Image'] ['model_id'] = $id;

        $objImage->save($data);

    }
    /**
* 
   * escape name of the File
   * @author Oleg
   */
    function escapeFile($string,$nums=0)
    {
          $string = str_replace(" ", "_", $string);
          $string = str_replace("&amp;", "and", $string);
          $string = str_replace("&", "and", $string);
          $string = eregi_replace('[^a-zA-Z0-9]', '_', $string);
        $string = str_replace("___", "_", $string);
        $string = str_replace("__", "_", $string);
        if($nums) {
            $string=substr($string, 0, $nums); 
        }
        $string=strtolower($string);

          return $string;
    }
}
?>
