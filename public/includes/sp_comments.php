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
		
		}//End Handle Ajax	
		
		
		//Add upvote downvote HTML to comment output.
		//$comment_text, $comment, $args
		add_filter( 'comment_text', array( $this, 'upvote_downvote_html'), 3, 10 );
	
	
	
	
	
	}//Contruct
	
	
	  /**
	  * Our Ajax Url
	  * Args = $comment_text, $comment, $args
	  * @param int $post_id The ID of the post being saved.
	  */
	 
	 function sp_comments_ajaxurl() {
	?>
		  <script type="text/javascript">
          var $comment_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>?action=sp_Comments_Ajax';
          </script>
    <?php }//sp_comments_ajaxurl
	
	 
	 
	
	 
	 
	 /**
	 *Comment Related Ajax
	 * 
	 * 
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
				echo $wwad;
		
				exit();
					
			
			break;//show_results	
			
			
			
			
			
		  //Doing a vote			
		  case('social_polling_vote'):
		 	//Post we are voting on.
		 	$vote = false;
			$post_id = is_numeric(sanitize_text_field($_POST['post_id'])) ?  sanitize_text_field($_POST['post_id'])  :  false;
			$vote_value = sanitize_text_field($_POST['vote_value']);
		 	
			//If we have an issue with the post ID.
			if(!$post_id):
				echo 'error';
			exit();
			endif;
			
			
			//Lets take our post_id and use this to get our to possible answers.
			$answers = array();
			$answer_1 = get_post_meta($post_id,'_social_polling_answer_one_field',true ); 
			$answer_2 = get_post_meta($post_id,'_social_polling_answer_two_field',true ); 
			
			//Get answer 1 from post meta
			if(!empty($answer_1)  &&   $answer_1 != NULL):
				$answers[] = $answer_1;
			endif;
			
			//Get answer 2 from post meta
			if(!empty($answer_2)  &&   $answer_2 != NULL):
				$answers[] = $answer_2;
			endif;
			
			//If our vote value is in out answer array, then do the vote.
			if(in_array($vote_value, $answers)):
			
				$args = array();
				$args['post_id'] = $post_id;
				$args['vote_value'] = $vote_value;			
				
				$vote = $this->social_polling_vote($args);
			
			
			endif;
			
			switch($vote): 	
		 	case('voted'):
				echo 'You were voting on Post '.get_post_meta($post_id,'social_polling_question_field',true );
		 		echo 'You voted '.$vote_value;
		  	break;
			
			case('already_voted'):
				echo 'You already voted';
		  	break;			
			
			default:
			echo 'Error';
			endswitch;
		  
		  
		  die(); 
		  break;
		
		endswitch;//switch($wwad):
		
		
			
	
		die(); 
		exit(); 
		 
		
		
		
		
	}//sp_Comments_Ajax
	
	
	
	


	/**
	 *Output Upvote Downvote HTML for comments
	 * Args = $comment_text, $comment, $args
	 * @param int $post_id The ID of the post being saved.
	 */
	
	public function upvote_downvote_html($comment_text, $comment, $args){
		
	//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($args,true))."</pre>";	
	//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comment,true))."</pre>";	
	//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comment_text	,true))."</pre>";
	
	
	ob_start();?>
    <style type="text/css">
    .comment_vote_action{ display:inline-block; overflow:hidden;}
    
    </style>
	<div class="comment_controls">
		<a class="upvote comment_vote_action" href="javascript:void(0)" data_action="upvote" data-comment_id="<?php echo $comment->comment_ID;?>">&#9650; Upvote</a><a class="downvote comment_vote_action" href="javascript:void(0)" data_action="downvote"  data-comment_id="<?php echo $comment->comment_ID;?>">&#9660; Downvote</a>
    </div>    
	<?php 
	$upvote_downvote = ob_get_contents();
	ob_end_clean();
	return $upvote_downvote.$comment_text;	
	}//upvote_downvote_html


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


}//CLASS spComments
