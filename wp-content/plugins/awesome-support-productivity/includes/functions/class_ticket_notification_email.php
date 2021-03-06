<?php


class WPAS_PF_Ticket_Notification_Email extends WPAS_PF_Ticket_Meta {
	
	protected static $instance = null;
	
	/**
	 * Unique meta key
	 *
	 * @var string 
	 */
	protected $key = 'notification_emails';
	
	
	public function __construct() {
		global $post;
		
		add_action( 'wp_ajax_pf_add_notification_emails',		array( $this, 'add_email'       ) ); // Add new email.
		add_action( 'wp_ajax_pf_edit_notification_emails',		array( $this, 'edit_email'      ) ); // Edit existing email.
		add_action( 'wp_ajax_pf_delete_notification_emails',		array( $this, 'delete_email'    ) ); // Delete email.
		add_action( 'wp_ajax_pf_active_notification_emails',		array( $this, 'activate_email'    ) ); // Delete email.
		
		add_action( 'wp_ajax_pf_edit_win_notification_emails',		array( $this, 'edit_form'	   ) ); // Generate edit email form
		add_filter( 'wpas_email_notifications_email',			array( $this, 'set_notification_emails'), 11, 3 );
		add_action( 'admin_enqueue_scripts',				array( $this, 'enqueue_scripts') );
		
		
		if( $post && 'ticket' === $post->post_type ) {
			$this->ticket_id = $post->ID;
		}
		
	}
	
	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/**
	 * Check if user have access to emails
	 * 
	 * @param int $user_id
	 * 
	 * @return boolean
	 */
	public function have_access( $user_id = 0 ) {
		
		$user_id = $this->get_user( $user_id );
		$user = get_user_by( 'id', $user_id );
		
		if( $user->has_cap( 'edit_ticket' ) ) {
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * Check if we should display metabox
	 * 
	 * @return boolean
	 */
	public static function should_display() {
		
		$_this = self::get_instance();
		
		if( $_this->have_access() ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check if user have capability to add an email
	 * 
	 * @param int $user_id
	 * @return boolean
	 */
	public function user_can_add( $user_id = 0 ) {
		
		$user_id = $this->get_user( $user_id );
		
		if( $this->have_access( $user_id ) ) {
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * Set notification email addresses from ticket email addresses
	 * 
	 * @param array $args
	 * @param string $case
	 * @param int $ticket_id
	 * 
	 * @return array
	 */
	public function set_notification_emails( $args, $case, $ticket_id ) {
		
		if( "notification_email" === $case && function_exists( 'as_in_default_email_active_types' ) ) {
			
			if( isset( $args['context'] ) ) {
				$active_email_types = maybe_unserialize( wpas_get_option( "{$args['context']}__active_email_types", as_in_default_email_active_types() ) ) ;
				if( !in_array( 'additional_emails', $active_email_types ) ) {
					return $args;
				}
			}
		}
		
		
		$items = $this->getList( $ticket_id );
		
		
		
		$emails = $args['recipient_email'];
		
		if( !empty( $items ) ) {
			
			if( !is_array( $emails ) ) {
				$emails = array( $emails );
			}
			
			foreach ( $items as $item ) {
				if( $item['active'] ) {
					$emails[] = $item['email'];
				}
			}
			
			$args['recipient_email'] = $emails;
		}
		
		return $args;
	}
	
	/**
	 * Generate add new item button
	 */
	public function addItemButton() {
			
		$window_title = __( 'Add Email', 'wpas_productivity' );
		$width = 600;
		$height = 450;
			
		$action_name = $this->actionName( 'add' );
		$window_id = "wpas_{$action_name}_wrapper";
		
		echo '<div class="wpas_pf_tb_button">';
			printf( '<a href="#TB_inline?width=%d&height=%d&inlineId=%s" class="button button-primary" title="%s">%s</a>', $width, $height, $window_id, $window_title, $window_title );
		echo '</div>';
		echo '<div class="clear clearfix"></div>';
	}
	
	/**
	 * Enqueue static resources
	 */
	public function enqueue_scripts() {
		add_thickbox();
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
		wp_enqueue_style('jquery-ui-css', WPAS_PF_URL . 'assets/css/jquery-ui.min.css');
	}
	
	/**
	 * Load whole view
	 * 
	 * @param int $post_id
	 */
	public function display( $post_id = 0 ) {
		
		$this->ticket_id = $post_id;
		
		$user_can_add = $this->user_can_add();
		
		
		echo '<div id="wpas_pf_ui_section_'.$this->key.'" data-section="' . $this->key . '" class="wpas_pf_ui_wrapper" data-duid="' . $this->get_ticket_id( $post_id ) . '">';
		
		if( $user_can_add ) {
			$this->addItemButton();
		}
			
		$this->items_listing() ;
		
		if( $user_can_add ) {
			
			$this->form( array(
				'type'		=> 'add',
				'submit_text'	=> __( 'Add Email', 'wpas_productivity' ),
				'hidden'	=> true
			));
		}
		echo '</div>';
	}
	
	
	/**
	 * Load items view
	 * 
	 * @param int $user_id
	 */
	public function items_listing() {
		
		$items = $this->getList();
		
		$id = "wpas_pf_{$this->key}_items";
		?>
		
		<div id="<?php echo $id; ?>" class="wpas_pf_data_items">
			<div class="no_item_msg"><?php echo _e( "No email exist.", 'wpas_productivity' ); ?></div>
			<?php include $this->itemsTemplate(); ?>
		</div>

		<?php
	}
	
	/**
	 * Generate edit email form
	 */
	public function edit_form() {
		
		
		$id = filter_input( INPUT_GET, 'id' );
		$this->ticket_id = filter_input( INPUT_GET, 'ticket_id' );
		
		$list = $this->getList();
		
		$item = $list[ $id ];
		$item['id'] = $id;
		
		$this->form( array(
			'type'		=> 'edit',
			'submit_text'	=> __( 'Save Email', 'wpas_productivity' ),
			'hidden'	=> false,
			'template'	=> 'add',
			'data'		=> $item
		) );
		
		die();
	}
	
	/**
	 * Validate add/edit email forms
	 * 
	 * @param string $type
	 * 
	 * @return array
	 */
	protected function validate( $type ) {
		
		$error = "";
		$result = array();
		
		$email   = filter_input( INPUT_POST, 'email'  );
		$active  = filter_input( INPUT_POST, 'active' );
		$item_id = filter_input( INPUT_POST, 'id'     );
		
		$action = $this->actionName( $type );
		$nonce = $this->nonce( $action );
		
		if( ! check_ajax_referer( $nonce['action'], $nonce['name'], false ) || !$this->user_can_add() ) {
			
			$error = __( 'Sorry, you are not allowed to add emails.', 'wpas_productivity' );
			
		} elseif( 'edit' === $type && !$this->item_exist( $item_id ) ) {
			
			$error = __( 'Email doesn\'t exist.', 'wpas_productivity' );
			
		} elseif( !$this->get_ticket_id() ) {
			
			$error = __( 'Something went wrong, try again later.', 'wpas_productivity' );
			
		} elseif( empty( trim( $email ) ) || !is_email( $email ) ) {
			
			$error = __( 'Please provide valid email address.', 'wpas_productivity' );
			
		} 
		
		
		if( $error ) {
			$result['error'] = $error;
		} else {
			$result['item'] = array( 
				'email' => $email, 
				'active' => $active ? true : false
			);
			
			if( 'edit' === $type ) {
				$result['item_id'] = $item_id;
			}
		}
		
		
		return $result;
	}
	
	
	/**
	 * Activate email to receive notifications
	 */
	public function activate_email () {
		
		$this->ticket_id = filter_input( INPUT_POST, 'duid' );
		
		$item_id =  filter_input( INPUT_POST, 'id'     );
		$active  =  filter_input( INPUT_POST, 'active' );
		
		$action = $this->actionName( 'active' );
		$nonce = $this->nonce( $action );
		
		$error = "";
		
		if( ! check_ajax_referer( $nonce['action'], $nonce['name'], false ) || !$this->user_can_add() ) {
			
			$error = __( 'Sorry, you are not allowed to add emails.', 'wpas_productivity' );
			
		} elseif( !$this->get_ticket_id() || !$this->item_exist( $item_id )) {
			
			$error = __( 'Something went wrong, try again later.', 'wpas_productivity' );
			
		} 
		
		if( $error ) {
			wp_send_json_error( array( 'msg' => $error ) );
		} else {
			
			$item = $this->get_item( $item_id );
			
			$item['active'] = ($active ? true : false);
			
			$this->update( $item, $item_id );
			
			ob_start();
			include $this->itemTemplate();
			$item_html = ob_get_clean();
			wp_send_json_success( array( 'msg' => __( 'Email Saved', 'wpas_productivity' ), 'update_item' => $item_html, 'item_id' => $item_id ) );
		}
		
		die();
	}
	
	
	/**
	 * Handle add email request
	 */
	public function add_email() {
		
		$this->ticket_id = filter_input( INPUT_POST, 'duid' );
		
		$result = $this->validate( 'add' );
		
		if( isset( $result['error'] ) && !empty( $result['error'] ) ) {
			wp_send_json_error( array( 'msg' => $result['error'] ) );
		} else {
			

			$item = $this->add( $result['item'] );

			$item_id = $item['id'];

			ob_start();
			include $this->itemTemplate();
			$item_html = ob_get_clean();

			wp_send_json_success( array( 'msg' => __( 'Email added.', 'wpas_productivity' ), 'item' => $item_html ) );
			
		}
		
		die();
	}
	
	
	/**
	 * Handle edit email request
	 */
	public function edit_email() {
		
		$this->ticket_id = filter_input( INPUT_POST, 'duid' );
		
		$result = $this->validate( 'edit' );
		
		if( isset( $result['error'] ) && !empty( $result['error'] ) ) {
			wp_send_json_error( array( 'msg' => $result['error'] ) );
		} else {
			
			$item_id = $result['item_id'];
			$item = $result['item'];
			
			$this->update( $item, $item_id );
			
			ob_start();
			include $this->itemTemplate();
			$item_html = ob_get_clean();
			wp_send_json_success( array( 'msg' => __( 'Email Saved', 'wpas_productivity' ), 'update_item' => $item_html, 'item_id' => $item_id ) );
			
		}
		
		die();
		
	}
	
	/**
	 * Handle delete email request
	 */
	public function delete_email() {
		
		$error = "";
		$success = false;
		
		$action = $this->actionName( 'delete' );
		$nonce = $this->nonce( $action );
		
		$this->ticket_id = filter_input( INPUT_POST, 'duid' );
		$item_id =  filter_input( INPUT_POST, 'id'  );
		
		
		if( !check_ajax_referer( $nonce['action'], $nonce['name'], false ) || !$this->user_can_add() ) {
			
			$error = __( 'Sorry, you are not allowed to remove emails.', 'wpas_productivity' );
			
		} elseif( !$this->get_ticket_id() ) {
			
			$error = __( 'Something went wrong, try again later.', 'wpas_productivity' );
			
		} elseif( ! $this->item_exist ( $item_id ) ) {
			
			$error = __( 'Email doesn\'t exist.', 'wpas_productivity' );
			
		} else {
			if( $this->delete( $item_id ) ) {
				$success = true;
			}
		}
		
		
		
		if( true === $success ) {
			wp_send_json_success( array( 'msg' => __( 'Email deleted', 'wpas_productivity' ), 'item_id' => $item_id ) );
		} elseif( !empty( $error ) ) {
			wp_send_json_error( array( 'msg' => $error ) );
		}
		die();
		
	}
	
}