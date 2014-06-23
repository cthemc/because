<?php
/**
 * Plugin Name.
 *
 * @package   Social_Polling_Admin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-social-polling.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Social_Polling_Admin
 * @author  Your Name <email@example.com>
 */
class Social_Polling_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Social_Polling" to the name of your initial plugin class
		 *
		 */
		$plugin = Social_Polling::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		
		
		include_once( 'includes/voting_box.php');
		
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );


		//Are we trying to download the CSV file.<br />
		add_action('plugins_loaded', array($this, 'plugins_loaded_verb'), 50);







		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Social_Polling" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Social_Polling::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Social_Polling" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		//Scripts necessary for media uploader on posts or pages.
		
	    wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'assets/js/meta-box-image.js', array( 'jquery' ) );
        wp_localize_script( 'meta-box-image', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
                'button' => __( 'Use this image', 'prfx-textdomain' ),
            )
        );
        wp_enqueue_script( 'meta-box-image' );
   
		
		
		
		
		
		
		//Below line is for scripts that should be included on our plugin admin page.
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Social_Polling::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Social Polling Admin', $this->plugin_slug ),
			__( 'Social Polling', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_social_polling_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_social_polling_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	
	
	//Run this after plugins are loaded.
	//This function is added to set the download of the csv file.
	 function plugins_loaded_verb() {
	  global $pagenow, $wpdb;
	
	  
    if ($pagenow=='options-general.php' && 
          //current_user_can('download_csv') && 
          isset($_GET['page'])  && 
          $_GET['page']=='social-polling' &&
		  isset($_GET['download'])  && 
          ($_GET['download']=='social_polling_voting'  ||
		   $_GET['download']=='social_polling_comments'
		  )
		){
			
		
			
		$file_to_gen = $_GET['download'];
		//echo $file_to_gen;
		
		$poll_question_array = array();	
		
		//Query post meta table to get post meta for posts that have a social poll.
		$postmeta_table = $wpdb->prefix . "postmeta";
		$post_with_poll_question = $wpdb->get_results( "SELECT * FROM $postmeta_table WHERE (meta_key = 'social_polling_question_field'  AND  (meta_value IS NOT NULL AND meta_value != ''))", 'ARRAY_A' );
		
		//Create a poll Question Array.
		//Contains in array of just our poul questions
		//Array will allow us to easily includ the poll question in each entry of our voting table results.
		foreach($post_with_poll_question as  $key => $poll_values):
			
			//Post ID of post POLL is assigned to.
			$post_id = $poll_values['post_id'];
			//Get post data related to POLL Post is assigned too.
			$post_data =  get_post( $post_id, 'ARRAY_A');
			
			
			//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($post_data ,true))."</pre>";
			
			$poll_question_array[$post_id]['post_id'] = $post_id;
			$poll_question_array[$post_id]['poll_question'] = $poll_values['meta_value'];
			$poll_question_array[$post_id]['post_title'] = $post_data['post_title'];
			$poll_question_array[$post_id]['post_link'] = $post_data['guid'];
		
		endforeach;
		
		
		
		
		
		
		//Depending on whether we are getting votes or comments.
		switch($file_to_gen):
		case('social_polling_voting'):
		
		//Query the DB for social poll votes.
		$voting_table = $wpdb->prefix . "social_polling";
		$voting_table_results = $wpdb->get_results( "SELECT * FROM $voting_table", 'ARRAY_A' );
		
	
				
		
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($poll_question_array,true))."</pre>";
		
		
		$data = $comments_voting_results;
		$social_poll_votes = array();
		
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($voting_table_results,true))."</pre>";
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comments_voting_results ,true))."</pre>";
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($social_poll_votes ,true))."</pre>";
		
		
		
		foreach($voting_table_results as  $key => $voting_data):
		
			$post_id_releated_to_poll = $voting_data['poll_id'];
			$social_poll_votes[$key] = $voting_data;
			$social_poll_votes[$key]['poll_question'] = $poll_question_array[$post_id_releated_to_poll]['poll_question'];
			$social_poll_votes[$key]['post_title'] = $poll_question_array[$post_id_releated_to_poll]['post_title'];
			$social_poll_votes[$key]['post_link'] = $poll_question_array[$post_id_releated_to_poll]['post_link'];
				
		endforeach;
		
		//Add headers onto array.
		$array_keys = array_keys($social_poll_votes[0]);
		array_unshift($social_poll_votes, $array_keys);	
		
		$array_to_csv_ifiy = $social_poll_votes;
		$file_name = 'social_polling_votes';
		$csv_generate = true;
		
		break;
		//END case('social_polling_voting'):
		case('social_polling_comments'):
		
			$comment_voting_table = $wpdb->prefix . "social_polling_comments";
			$comments_voting_results = $wpdb->get_results( "SELECT *  FROM $comment_voting_table", 'ARRAY_A' );		
			
			
			$comments_table = $wpdb->prefix . "comments";
			$comments_results = $wpdb->get_results( "SELECT *  FROM $comments_table", 'ARRAY_A' );		
			
			//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comments_voting_results,true))."</pre>";
			//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comments_results,true))."</pre>";
			
			
			foreach($comments_results as  $key => $comment_data):
			
			//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($comment_data,true))."</pre>";
		
			$comment_id_releated_to_poll = $comment_data['comment_ID'];
			
			//echo $comment_id_releated_to_poll;
			
			
			$social_poll_comments[$key] = $comment_data;
			$upvotes_results = $wpdb->get_results( "SELECT * FROM $comment_voting_table WHERE (comment_id = $comment_id_releated_to_poll AND vote_value = 'upvote')", 'ARRAY_A' );
			$downvote_results = $wpdb->get_results( "SELECT * FROM $comment_voting_table WHERE (comment_id = $comment_id_releated_to_poll AND vote_value = 'downvote')", 'ARRAY_A' );		
			
			//Add upvotes and downvotes to array we are going to turn into a csv.		
			$social_poll_comments[$key]['up_votes'] = count($upvotes_results);
			$social_poll_comments[$key]['down_votes'] =  count($downvote_results);
			
		
				
		endforeach;
		
		
			//Add Column headers onto array.
			$array_keys = array_keys($social_poll_comments[0]);
			array_unshift($social_poll_comments, $array_keys);	
			
			//Set our variable to determine what array is going to be turned into a csv
			$array_to_csv_ifiy = $social_poll_comments;
			$file_name = 'social_polling_comments';
			$csv_generate = true;
			
			
		
		break;
		
		default:
		exit();
		
		endswitch;
		//switch($file_to_gen):
		
		
		
		
		
		
		
		
		
		
		
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($social_poll_votes ,true))."</pre>";
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($post_with_poll_question,true))."</pre>";
		
		
		
		
		
		//Function that generates csv file.	
		
		
		
		
		
		
		
	
		if($csv_generate): 
        	header("Content-type: application/x-msdownload");
        	header("Content-Disposition: attachment; filename=".$file_name.".csv");
        	header("Pragma: no-cache");
        	header("Expires: 0");
					
			
			function outputCSV($data) {
				$output = fopen("php://output", "w");
			
				foreach ($data as $row) {
					fputcsv($output, $row);
				}
				fclose($output);
			}
			outputCSV($array_to_csv_ifiy);
				
		   exit();
		endif;
      	//if($csv_generate): 
	  
	  }
	 
	  
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
