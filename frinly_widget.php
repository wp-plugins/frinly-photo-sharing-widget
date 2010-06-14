<?php
/*
Plugin Name: Frin.ly - Photo Sharing
Plugin URI: http://frin.ly/recommend/wordpress/
Description: Display photos from your Frin.ly photo album onto your WordPress Blog. Requires cURL to be installed. 
Version: 1.0
Author: Joseph Tinsley
Author URI: http://twitter.com/frinly
*/

class frinly_widget extends WP_Widget
{

    function frinly_widget(){
    $widget_ops = array('classname' => 'frinly_photo_widget', 'description' => 'Display photos from your Frin.ly photo stream' );
    $control_ops = array('width' => 300, 'height' => 300);
    $this->WP_Widget('frinly_widget', 'Frin.ly Photo\'s', $widget_ops, $control_ops);
    }

function frinly_Photo_XML($user_name) {

	$url='http://frin.ly/'.$user_name.'/rss/rss.xml';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	
	$output= curl_exec($ch);
	curl_close($ch);
	
	$data=explode('<item>',$output);
	
	$nodeCnt=count($data);
	
		for($y=1;$y < $nodeCnt;$y++)
		{
	
			if (preg_match('#<link>(.*?)</link>#i',$data[$y], $match_1)) {
			$link = trim($match_1[1]);	
			}else{}
			
			if (preg_match('#<imageURL>(.*?)</imageURL>#i',$data[$y], $match_2)) {
			$imgUrl = trim($match_2[1]);	
			}else{}	
			
		    if (preg_match('#<title>(.*?)</title>#i',$data[$y], $match_3)) {
			$eTitle = trim( str_replace('<![CDATA[','', str_replace(']]>','',$match_3[1] ) ) );	
			}else{}
			
				$photoArray[]=array($link, $imgUrl, $eTitle);			
		}

return $photoArray;		
}

function widget($args, $instance) {

	extract($args);

	$title = $instance['title'];
	$user_name = $instance['user_name'];
	$display = $instance['display'];

	$photoDisplay = $this->frinly_Photo_XML($user_name);

	echo $before_widget;

	if ( $user_name )

	echo "<h2 style=\"text-align:left;\"><a href=\"http://frin.ly/".$user_name."\">". $title ."</a></h2>";
	for($c=0; $c < $display; $c++){  
	    echo "<span style=\"text-align:center;display:block;padding:2px;\">";        
	    echo "<a href=\"".$photoDisplay[$c][0]."\" title=\"".$photoDisplay[$c][2]."\">".
	    "<img src=\"".$photoDisplay[$c][1]."\" border=\"0\" style=\"padding: 5px; border: 1px solid #aaa;\" alt=\"".$photoDisplay[$c][2]."\"></a>";
	    echo "</span>";            
	}
	 
	echo $after_widget;		
}


    function update($new_instance, $old_instance){
      $instance = $old_instance;
      $instance['user_name'] = strip_tags(stripslashes($new_instance['user_name']));
      $instance['title'] = strip_tags(stripslashes($new_instance['title']));
      $instance['display'] = strip_tags(stripslashes($new_instance['display']));

    return $instance;
  }

    function form($instance){

      $instance = wp_parse_args( (array) $instance, array('user_name'=>'Username', 'title'=>'Album Title', 'display'=>'3') );

      $user_name= htmlspecialchars($instance['user_name']);
      $title = htmlspecialchars($instance['title']);
      $display = htmlspecialchars($instance['display']);

?> 

<p style="text-align:right;">
<label for="<?php echo $this->get_field_name('title');?>">
<?php echo 'Title:'; ?> 
<input style="width: 150px;" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title;?>" />
</label>
</p>
    
<p style="text-align:right;">
<label for="<?php echo $this->get_field_name('user_name');?>">
<?php echo 'http://Frin.ly/'; ?> 
<input style="width: 150px;" id="<?php echo $this->get_field_id('user_name');?>" name="<?php echo $this->get_field_name('user_name');?>" type="text" value="<?php echo $user_name;?>" />
</label>
</p>


<p style="text-align:right;">
<label for="<?php echo $this->get_field_id( 'display' ); ?>">Images Displayed:</label>
<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" style="width:150px;">
    <option <?php if ( '1' == $instance['display'] ) echo 'selected="selected"'; ?>>1</option>
    <option <?php if ( '2' == $instance['display'] ) echo 'selected="selected"'; ?>>2</option>
    <option <?php if ( '3' == $instance['display'] ) echo 'selected="selected"'; ?>>3</option>
    <option <?php if ( '4' == $instance['display'] ) echo 'selected="selected"'; ?>>4</option>
    <option <?php if ( '5' == $instance['display'] ) echo 'selected="selected"'; ?>>5</option>
</select>
</p>

<?
  }

}

  function frinly_widget_Init() {
  register_widget('frinly_widget');
  }
  
  add_action('widgets_init', 'frinly_widget_Init');
?>
