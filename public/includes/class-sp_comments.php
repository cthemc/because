<?php

/**
 * Calls the class on the post edit screen.
 */
function call_spComments() {
    new spComments();
}

//if ( !is_admin() ) {
    add_action( 'init', 'call_spComments' );
    //add_action( 'load-post-new.php', 'call_spComments' );
//}

/** 
 * The Class.
 */
class spComments {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		//add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		//add_action( 'save_post', array( $this, 'save' ) );
		
		//Add Ajax Actions
			
		//Need to define a javascript variable since our JS is in its own 
		//Seperate JS file.	
		add_action('wp_head', array( $this,'sp_comments_ajaxurl'));

		if ( is_admin() ) {			
    		add_action('wp_ajax_sp_Comments_Ajax',  array( $this,'sp_Comments_Ajax'));
			add_action('wp_ajax_nopriv_sp_Comments_Ajax',  array( $this,'sp_Comments_Ajax'));
   			 // Add other back-end action hooks here
		} else {
			
   		 // Add non-Ajax front-end action hooks here
			add_action('wp_ajax_sp_Comments_Ajax',  array( $this,'sp_Comments_Ajax'));
			add_action('wp_ajax_sp_Comments_Ajax',  array( $this,'sp_Comments_Ajax'));
						
			
			//Add on stuff to right after text box.
			//add_action( 'comment_form', array( $this, 'additional_fields') );
					
			//Filter Comments actual content
			//add_filter('pre_comment_content', array( $this, 'before_comment_saved'),1,2);
			
				
			//$commentdata = apply_filters( 'preprocess_comment', $commentdata );
			//add_filter('preprocess_comment', array( $this, 'preprocess_comment_saved'),1,2);
			//add_action( 'preprocess_comment' , 'preprocess_comment_remove_url' ); 
			
			add_filter('comment_form_field_comment', array( $this, 'comment_form_field_comment_filter'),1,2);
			
			add_filter('pre_comment_approved', array( $this, 'pre_comment_approved_filter'),2,2);
			//apply_filters( 'pre_comment_approved', $approved, $commentdata );
			
			
			add_filter('comment_form_defaults', array( $this, 'text_limit_box'),2);
			//apply_filters( 'comment_form_defaults', $defaults )
			
			
			
			//Add upvote downvote HTML to comment output using a filter
			//I disabled this because I added it directly to my comment call back function.
		    //$comment_text, $comment, $args
		   // add_filter( 'comment_text', array( $this, 'upvote_downvote_html'), 3, 10 );	
		}//End Handle Ajax	
	
	
		 add_filter( "comments_template", array( $this, 'my_plugin_comment_template') );
	
	
	
	}//Contruct
	
	
	  /**
	  * Our Ajax Url
	  * Args = $comment_text, $comment, $args
	  * @param int $post_id The ID of the post being saved.
	  */
	 
	 
	 
	 
	 public function sp_comment_callback($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
	
		
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div ';
			$add_below = 'comment';
		} else {
			$tag = 'li ';
			$add_below = 'div-comment';
		}?>

				<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-
<?php comment_ID() ?>
">
<?php if ( 'div' != $args['style'] ) : ?>
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
  <?php endif; ?>
  <div class="comment-author vcard">
    <?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?> </div>
  <?php if ($comment->comment_approved == '0') : ?>
  <em class="comment-awaiting-moderation">
  <?php _e('Your comment is awaiting moderation.') ?>
  </em> <br />
  <?php endif; ?>
  <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
    <?php
				/* translators: 1: date, 2: time */
				printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
    </a>
    <?php edit_comment_link(__('(Edit)'),'  ','' );
			?>
  </div>
  <?php comment_text() ?>
  <div class="reply">
    <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
  </div>
  <?php if ( 'div' != $args['style'] ) : ?>
</div>
<?php endif; ?>
<?php
        }
	 
	 
	 
	 
	 
	 public function sp_comments_ajaxurl() {
	?>
<script type="text/javascript">
          var $comment_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?action=sp_Comments_Ajax';
          </script>
<?php }//sp_comments_ajaxurl
	
	 
	 
	
	 
	 
	 /**
	 *Comment Related Ajax
	 */
	public function sp_Comments_Ajax(){
		//What We Are Doing
		$wwad = isset($_POST['wwad']) ? $_POST['wwad'] : false;
		if(!$wwad):
			echo 'error wwad';
			exit();
		endif;
		
		
		switch($wwad):
		  //Getting some results
		    
		
			case('upvote'):
			case('downvote'):
			case('flag'):
				//Nonce
				$upvote_downvote_flag_nonce = $_POST['nonce'];
				//Verfiy our nonce
				if ( ! wp_verify_nonce( $upvote_downvote_flag_nonce, 'upvote_downvote_flag_nonce' ) ):
				     die( 'Security check' ); 
				endif;	 
				
				//Comment ID
				$comment_id = is_numeric(sanitize_text_field($_POST['comment_id'])) ?  sanitize_text_field($_POST['comment_id'])  :  false;
				if(!$comment_id):
					echo 'error';
				exit();
				endif;
							
							
				//Lets try to our upvote or downvote.
				$upvote_downvote_args['vote_value'] = $wwad;
				$upvote_downvote_args['comment_id'] = $comment_id;
				$voted =  $this->do_upvote_downvote_flag($upvote_downvote_args);
				//If we successfully voted
				if($voted):
				  echo $voted;
				  die();
				endif;///if($voted):
							
			
				echo $wwad;
		
				exit();
					
			
			  break;//upvote downvote	
			
			  
			  
			  
			  
			  
			  case('comment_sorting'):
			  //Nonce
				$comment_sorting_nonce = $_POST['nonce'];
				//Verfiy our nonce
				if ( ! wp_verify_nonce( $comment_sorting_nonce, 'comment_sorting_nonce' ) ):
				    die( 'Security check!' ); 
				endif;	
				
				
				$sortby = $_POST['sortby'];
				
				
				$post_id = $_POST['post_id'];
				
				if($sortby == 'oldest'):
				
					$comment_args['order'] = 'ASC';
				
				elseif($sortby == 'top'):	
					
					$comment_args['orderby'] = 'comment_karma';
					
				endif;
				
				$comment_args['post_id'] = $post_id;
				$comments = get_comments( $comment_args );
				
				
				/*echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comments ,true))."</pre>";
				die();
				*/
				ob_start();
				wp_list_comments(array(
							'callback' => 'mytheme_comment',
							'walker' => new zipGun_walker_comment()
							//'per_page' => 10, //Allow comment pagination
							//'reverse_top_level' => $sortby == 'oldest' ? false : true //Show the latest comments at the top of the list
				), $comments);
				$updated_comments = ob_get_contents();
				ob_end_clean;
				
				return $updated_comments;
				
				die();
				
				 
			  
			  
			  break;//case('comment_sorting'):
			
			
			
			
			default:
		  echo 'error';
		  die(); 
		  break;
		
		endswitch;//switch($wwad):
		
		
			
	
		die(); 
		exit(); 
		 
		
		
		
		
	}//sp_Comments_Ajax
	
	
	function flag($args = array()){
	
	
	}//
	
	function do_upvote_downvote_flag($args = array()){
		 global $wpdb;
 	     $table = $wpdb->prefix . "social_polling_comments";
		
		
		//Args
		//$upvote_downvote_args['vote_value'] = $wwad;
		//$upvote_downvote_args['comment_id'] = $comment_id;
		
		 $defaults = array();
		 $do_vote = false;
		 $args = wp_parse_args( $args, $defaults );
		 extract( $args );
		
		switch($vote_value):
			case('upvote'):
			case('downvote'):
			case('flag'):
			
			$by_ip = false;
			$by_id = false;
			$return_value = 'do_nothing';	
			//Our insert Array.
			$data = $format = array();
				$data['vote_value'] = $vote_value;
				$format[] = '%s';
			
			//Get USER ID AND USER IP.
			if ( is_user_logged_in() ):
				
				$by_id = true;
				$user_id = $this->get_voter('');	
				$data['voter_id'] = $user_id;	
				$format[] = '%d';
			else:
				$by_ip = true;
				$user_id = md5($this->get_voter('ip_address'));	
				$data['voter_ip'] = $user_id;
				$format[] = '%s';				
			endif;
			
			//Make sure our comment ID is numeric and is set.
			if(!$comment_id || !is_numeric($comment_id)):
				return false('Invalid Comment ID');	
			else:
				$data['comment_id'] = $comment_id;	
				$format[] = '%d';		
			endif;	
						
			
			
			if($vote_value == 'upvote'  ||  $vote_value == 'downvote'):
			//Have we already up or downvoted
			 $already_voted = $wpdb->get_row("SELECT * FROM $table WHERE comment_id = ".$comment_id." AND (voter_ip = '".$user_id."'  ||  (voter_id = '".$user_id."'  && voter_id != 0))" );	
		 	 
			elseif($vote_value == 'flag'):
			//Have we already Flagged
			 $already_voted = $wpdb->get_row("SELECT * FROM $table WHERE comment_id = ".$comment_id." AND (vote_value = 'flag'   ||  vote_value = 'unflag')  AND (voter_ip = '".$user_id."'  ||  (voter_id = '".$user_id."'  && voter_id != 0))" );	
			else:
			
			endif; 
			 
			 
			 
			 
			if($vote_value == 'upvote'  ||  $vote_value == 'downvote'): 
				 if($already_voted):
				//You already voted 
				//So update your record in the DB
						$where['id'] = $already_voted->id;
						$do_vote = $wpdb->update( $table, $data, $where, $format );
				 else:
				 
					 //Do the vote	
					 //This is a new vote, so add a new row in the DB
					  $do_vote = $wpdb->insert( $table, $data, $format );
				 endif;//if($already_voted):
			 
			 			  
				  $comment = get_comment( $comment_id, 'ARRAY_A' );
				  $commentarr['comment_ID'] = $comment_id; 
				  
				  //If this is our first time voting on this comment
				  //Or we are changing our vote on this comment,
				  //We need to adjust the karma count
				  if(!$already_voted):
					  $commentarr['comment_karma'] = $vote_value == 'upvote' ? $comment['comment_karma'] + 1 : $comment['comment_karma'] - 1;
					  $update_comment = wp_update_comment( $commentarr );
					  $return_value = $vote_value;
				  elseif($already_voted->vote_value != $vote_value):
					  $return_value = $vote_value;
					  $commentarr['comment_karma'] = $vote_value == 'upvote' ? $comment['comment_karma'] + 1 : $comment['comment_karma'] - 1;
					  $update_comment = wp_update_comment( $commentarr );
				  //Since we are in essence, just canceling out our vote, we are going to go ahead and delete our entire record row from the comment table.
				  //In instances where we voted the wrong way the first time, this will allow us to correct our vote, and not just cancel out our incorrect vote.
					  $wpdb->delete( $table, $where);
				  else:
				  
					  
				  
				  endif;
			
			
			
			
			elseif($vote_value == 'flag'):
			
			
			
			 	if($already_voted):
				//You already flagged this 
				//So update your record in the DB
						$where['id'] = $already_voted->id;
						//$where['vote_value'] = 'flag';
						//$do_vote = $wpdb->delete( $table, $data, array( 'ID' => $where['id'] ) , $format );
						
						//Current Value.
						$current_val = $wpdb->get_results("SELECT * FROM $table WHERE ID = ".$where['id']);
						
						$current_val = $current_val[0]->vote_value;
				
						
						if($current_val == 'flag'):
							
							$update = $wpdb->query("UPDATE $table SET vote_value = 'unflag' WHERE ID = ".$where['id'] );
							$return_value = 'unflagged';
						
						elseif($current_val == 'unflag'):
							
							
							$update = $wpdb->query("UPDATE $table SET vote_value = 'flag' WHERE ID = ".$where['id'] );
							$return_value = 'flagged';
						else:	
						
						
						//$return_value = " <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($current_val,true))."</pre>";
							
						endif;
						
						
						
						
				 else:
				     //Do the vote	
					 //This is a new vote, so add a new row in the DB
					  $do_vote = $wpdb->insert( $table, $data, $format );
					  $return_value = 'flagged';
				 endif;//if($already_voted):		
			
			
			
			endif;	//if($vote_value == 'upvote'  ||  $vote_value == 'downvote'):  
				  
			
			
					
			break;
		
			default:
			return false($return_value);
		
		
		endswitch;	//switch($vote_value):
			
			
		
		
		 return $return_value;
		
		
	}//do_upvote_downvote_flag
	
	
	


	/**
	 *Output Upvote Downvote HTML for comments
	 * Args = $comment_text, $comment, $args
	 * @param int $post_id The ID of the post being saved.
	 */
	
	public function upvote_downvote_html($comment_text, $comment, $args){
	
	
	
		//This stuff if only for front end comments and displaying.
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($args,true))."</pre>";	
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comment,true))."</pre>";	
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comment_text	,true))."</pre>";
		$comment_karma = $comment->comment_karma;
		$comment_nonce = wp_create_nonce("upvote_downvote_flag_nonce");
		
		ob_start();?>

<div class="comment_controls">
  <div class="comment_karma_count"><?php echo $comment_karma; ?></div>
  <a data-nonce="<?php echo $comment_nonce; ?>" class="upvote comment_vote_action" href="javascript:void(0)" data_action="upvote" data-comment_id="<?php echo $comment->comment_ID;?>">&#9650; Upvote</a><a  data-nonce="<?php echo $comment_nonce; ?>" class="downvote comment_vote_action" href="javascript:void(0)" data_action="downvote"  data-comment_id="<?php echo $comment->comment_ID;?>">&#9660; Downvote</a></div>
<?php 
		$upvote_downvote = ob_get_contents();
		ob_end_clean();
		return $upvote_downvote.$comment_text;	

		//This is for backend displaying of comments.
		return $comment_text;
	
	
	
	
	
	
	
	
	
	
	
	}//upvote_downvote_html




	/**
	 * Social Get Voter.
	 * @return  Voter ID or Voter IP.
	 */
	 
	 private function get_voter($get_user_by = ''){
		 
		 switch($get_user_by):
		
			  case('ip_address'):
			   //Try to Grab IP Address
	  
				if (!empty($_SERVER['HTTP_CLIENT_IP']))
				//check ip from share internet
				{
				  $ip=$_SERVER['HTTP_CLIENT_IP'];
				}
				elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				//to check ip is pass from proxy
				{
				  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else
				{
				  $ip=$_SERVER['REMOTE_ADDR'];
				}
				return $ip;
			  break;//case('ip_address'):
			  
			  default:
			  //Return current user ID.
			  return get_current_user_id();			  
				
			  break;
		endswitch;
		  
	 }//get_voter	




	  function my_plugin_comment_template( $comment_template ) {
		  
		 
		   global $post;
		   return  plugin_dir_path( __FILE__ ).'templates/sp_comments.php';
		  /* if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
			  
			 
			  //require_once( plugin_dir_path( __FILE__ ) . 'public/class-social-polling.php' );
			  return;
		  
		  
		  
		  
		   }
		   if($post->post_type == 'business'){ // assuming there is a post type called business
			  return dirname(__FILE__) . '/reviews.php';
		   }
	 */
	 
	 
	 
	  }//my_plugin_comment_template








	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	
	 //Create Social Polling Database Table
	  global $wpdb;
   	  global $sp_db_version;

   	  $table_name = $wpdb->prefix . "social_polling_comments";
      
	  $sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  vote_time TIMESTAMP NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	  comment_id tinytext NOT NULL,
	  vote_value text NOT NULL,
	  voter_id mediumint(9) NOT NULL,
	  voter_ip VARCHAR(55) DEFAULT '' NOT NULL,
	  UNIQUE KEY id (id)
		);";

   	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  	  dbDelta( $sql );
 
      add_option( "sp_db_version", $sp_db_version );

	
	}//activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}//deactivate.
	
	
	
	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}
	
	
	
	


	
//Add Text Length limit field to comment box.
function text_limit_box($defaults ){
	

	//Change comment box field.
	$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">Comment (<span id="charcters_left">280</span> characters left)</label> <textarea id="comment" class="sp_comment_reply" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
	
	
	
	return $defaults ;
	
}


	
	

//Add a special field after the textarea, that will be the field that wordpress things is the current comment textarea.
//We will use javascript to remove it after a few seconds.
function comment_form_field_comment_filter($text_area){
	ob_start();?>	
    <input type="hidden"  name="comment" id="thecomment_field" value="socialpolling_verb" readonly="readonly" />
	<?php
    $extrafield = ob_get_contents();
	ob_end_clean();	
	$text_area = $text_area.$extrafield;
	return $text_area;
}


//Check whether to spam this or not.
//If this field equals are key word which is  'socialpolling_verb'
//That means that it had not been removed from the DOM yet, meaning, its from a bot. 	
function pre_comment_approved_filter($approved, $commentdata){
	if(trim($commentdata['comment_content']) == 'socialpolling_verb'):
		return 'spam';
	else:
		return $approved;
	endif;
	
}
	
	
	
	
	
	
	
	
	


}//CLASS spComments



function mytheme_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li ';
			$add_below = 'div-comment';
		}
?>
<li <?php //echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
        
        
         <div class="comment-meta commentmetadata">
  	<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
    <?php
		/* translators: 1: date, 2: time */
		printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
    </a>
    <?php edit_comment_link(__('(Edit)'),'  ','' );?>
  </div><?php //.comment-meta.commentmetadata ?>
        
        
  	<?php endif; ?>
  	<div class="comment-author vcard">
	  <?php 
	  
      if ($args['avatar_size'] != 0):
          echo get_avatar( $comment, $args['avatar_size'] ); 
      endif;
      ?>
   	
	
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?> 
      
      
   
     
      
      <div style="clear:both"></div>
    </div><?php //.comment-author vcard ?>
    
  <?php	
  	if($comment->comment_approved == '0') : ?>
  		<em class="comment-awaiting-moderation">
  			<?php _e('Your comment is awaiting moderation.') ?>
  		</em> 
        <br />
  <?php endif; ?>
  
 
  
<?php   //Upvote DownVote Actions
  
  $comment_karma = $comment->comment_karma;
  $comment_nonce = wp_create_nonce("upvote_downvote_flag_nonce");
  $flag_nonce = wp_create_nonce("flag_nonce");
  ob_start();?>
	<style type="text/css">
		.comment_vote_action{ display:inline-block; overflow:hidden; margin-right:5px;}
		 .comment_karma_count{ float:left; margin-right:5px; vertical-align:middle;}
	</style>
	<div class="comment_controls">
  		<div class="comment_karma_count"><?php echo $comment_karma; ?></div>
  			<a data-nonce="<?php echo $comment_nonce; ?>" class="upvote comment_vote_action" href="javascript:void(0)" data_action="upvote" data-comment_id="<?php echo $comment->comment_ID;?>">&#9650; Upvote</a><a  data-nonce="<?php echo $comment_nonce; ?>" class="downvote comment_vote_action" href="javascript:void(0)" data_action="downvote"  data-comment_id="<?php echo $comment->comment_ID;?>">&#9660; Downvote</a><?php /*?><a  data-nonce="<?php echo $comment_nonce; ?>" class="flag comment_vote_action" href="javascript:void(0)" data_action="flag"  data-comment_id="<?php echo $comment->comment_ID;?>"><strong>!</strong>Flag Comment</a><?php */?>
        </div>
<?php 
	  $upvote_downvote = ob_get_contents();
	  ob_end_clean();
	  echo $upvote_downvote
   //End Upvote Down Vote Controls. ?> 
  
  
  <?php comment_text() ?>
  
  
  
  <div class="reply">
    <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
  </div>
  <?php if ( 'div' != $args['style'] ) : ?>
		</div>
<?php endif; ?>
<?php
}//mytheme_comment



//----------------------------
//Comment_walker
//----------------------------
/** COMMENTS WALKER */
class zipGun_walker_comment extends Walker_Comment {/**
	 * What the class handles.
	 *
	 * @see Walker::$tree_type
	 *
	 * @since 2.7.0
	 * @var string
	 */
	var $tree_type = 'comment';

	/**
	 * DB fields to use.
	 *
	 * @see Walker::$db_fields
	 *
	 * @since 2.7.0
	 * @var array
	 */
	var $db_fields = array ('parent' => 'comment_parent', 'id' => 'comment_ID');

	/**
	 * Start the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of comment.
	 * @param array $args Uses 'style' argument for type of HTML list.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				$output .= '<ol class="children">' . "\n";
				break;
			default:
			case 'ul':
				$output .= '<ul class="children">' . "\n";
				break;
		}
	}

	/**
	 * End the list of items after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of comment.
	 * @param array  $args   Will only append content if style argument value is 'ol' or 'ul'.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				$output .= "</ol><!-- .children -->\n";
				break;
			default:
			case 'ul':
				$output .= "</ul><!-- .children -->\n";
				break;
		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * This function is designed to enhance Walker::display_element() to
	 * display children of higher nesting levels than selected inline on
	 * the highest depth level displayed. This prevents them being orphaned
	 * at the end of the comment list.
	 *
	 * Example: max_depth = 2, with 5 levels of nested content.
	 * 1
	 *  1.1
	 *    1.1.1
	 *    1.1.1.1
	 *    1.1.1.1.1
	 *    1.1.2
	 *    1.1.2.1
	 * 2
	 *  2.2
	 *
	 * @see Walker::display_element()
	 *
	 * @since 2.7.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments. @see wp_list_comments()
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];
		$id = $element->$id_field;

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		// If we're at the max depth, and the current element still has children, loop over those and display them at this level
		// This is to prevent them being orphaned to the end of the list.
		if ( $max_depth <= $depth + 1 && isset( $children_elements[$id]) ) {
			foreach ( $children_elements[ $id ] as $child )
				$this->display_element( $child, $children_elements, $max_depth, $depth, $args, $output );

			unset( $children_elements[ $id ] );
		}

	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output  Passed by reference. Used to append additional content.
	 * @param object $comment Comment data object.
	 * @param int    $depth   Depth of comment in reference to parents.
	 * @param array  $args    An array of arguments. @see wp_list_comments()
	 */
	function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;

		if ( !empty( $args['callback'] ) ) {
			ob_start();
			call_user_func( $args['callback'], $comment, $args, $depth );
			$output .= ob_get_clean();
			return;
		}

		if ( ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) && $args['short_ping'] ) {
			ob_start();
			$this->ping( $comment, $depth, $args );
			$output .= ob_get_clean();
		} elseif ( 'html5' === $args['format'] ) {
			ob_start();
			$this->html5_comment( $comment, $depth, $args );
			$output .= ob_get_clean();
		} else {
			ob_start();
			$this->comment( $comment, $depth, $args );
			$output .= ob_get_clean();
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.7.0
	 *
	 * @param string $output  Passed by reference. Used to append additional content.
	 * @param object $comment The comment object. Default current comment.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments. @see wp_list_comments()
	 */
	function end_el( &$output, $comment, $depth = 0, $args = array() ) {
		if ( !empty( $args['end-callback'] ) ) {
			ob_start();
			call_user_func( $args['end-callback'], $comment, $args, $depth );
			$output .= ob_get_clean();
			return;
		}
		if ( 'div' == $args['style'] )
			$output .= "</div><!-- #comment-## -->\n";
		else
			$output .= "</li><!-- #comment-## -->\n";
	}

	/**
	 * Output a pingback comment.
	 *
	 * @access protected
	 * @since 3.6.0
	 *
	 * @param object $comment The comment object.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments. @see wp_list_comments()
	 */
	protected function ping( $comment, $depth, $args ) {
		$tag = ( 'div' == $args['style'] ) ? 'div' : 'li';
?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-body">
				<?php _e( 'Pingback:' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
			</div>
<?php
	}

	/**
	 * Output a single comment.
	 *
	 * @access protected
	 * @since 3.6.0
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments. @see wp_list_comments()
	 */
	protected function comment( $comment, $depth, $args ) {
		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		
?>
		< <?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-author vcard">
			<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
			<?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ); ?>
		</div>
		<?php if ( '0' == $comment->comment_approved ) : ?>
		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ) ?></em>
		<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)' ), '&nbsp;&nbsp;', '' );
			?>
		</div>

		<?php comment_text( get_comment_id(), array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
<?php
	}

	/**
	 * Output a comment in the HTML5 format.
	 *
	 * @access protected
	 * @since 3.6.0
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments. @see wp_list_comments()
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
						<?php printf( __( '%s <span class="says">says:</span>' ), sprintf( '<b class="fn">%s</b>', get_comment_author_link() ) ); ?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php printf( _x( '%1$s at %2$s', '1: date, 2: time' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>
						<?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->
			</article><!-- .comment-body -->
<?php
	}
}
//----------------------------
//END Comment_walker
//----------------------------