<?php

$edit_view_link = add_query_arg( array( 
		'action' => $this->actionName('edit_win'), 
		'id' => $item_id,
		'duid' => $this->get_post_id(),
		'width' => 600,
		'height' => 'auto'
	), 
	admin_url( 'admin-ajax.php' ) 
);


$active = isset( $item['active'] ) ? $item['active'] : '';
$name = isset( $item['name'] ) ? $item['name'] : '';
$email = isset( $item['email'] ) ? $item['email'] : '';

?>

<div class="wpas_it_ui_item" data-item_id="<?php echo $item_id ?>">
	<div class="wpas_it_ui_item_msg">
		<span class="msg"></span>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'wpas_it' ); ?></span></button>
	</div>
	<div>
		<div class="it_ui_item_header">
			
			<?php if( $this->user_can_add() ) { ?>
			<input data-action_name="active" value="1" data-name="active" data-action="<?php echo $this->actionName('active'); ?>" type="checkbox"<?php echo (( $active ) ? ' checked="checked"' : ''); ?> class="active-cb" />
			<?php 
			} elseif( $item['active'] ) {
				echo '&#9745;';
			} else {
				echo '&#9744;';
			} 
			?>
			<span class="title"><?php echo $name . " " . $email; ?></span>
		</div>
		<?php if( $this->user_can_add() ) : ?>
		<ul class="actions">
			<li><a href="<?php echo $edit_view_link; ?>" data-action="edit" title="<?php _e( 'Edit Email', 'wpas_it' ); ?>" class="wpas_it_ui_item_action wpas_it_ui_item_action_edit"><?php _e( 'Edit', 'wpas_it' ); ?></a></li>
			<li><a href="#" data-action="<?php echo $this->actionName('delete'); ?>" class="wpas_it_ui_item_action wpas_it_ui_item_action_delete" data-confirm="<?php _e( "Are you sure you want to delete this email?" ) ?>"><?php _e( 'Delete', 'wpas_it' ); ?></a></li>
		</ul>
		<?php endif; ?>
		<div class="clear clearfix"></div>
	</div>
	
</div>