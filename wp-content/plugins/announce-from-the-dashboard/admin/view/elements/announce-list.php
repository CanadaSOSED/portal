<tr class="type-<?php echo $announce['type']; ?> list" id="list-<?php echo $announce_id; ?>">

	<th class="manage-column column-cb check-column">

		<input type="checkbox" value="<?php echo $announce_id; ?>" class="announce-id" />
		<span class="spinner"></span>

	</th>

	<td class="manage-column column-title">
	
		<div class="show">

			<p class="announce-title">
				<strong><?php echo $announce['title']; ?></strong>
			</p>

			<p class="announce-type">
	
				<?php if( !empty( $announce_types[$announce['type']] ) ) : ?>
	
					<?php echo $announce_types[$announce['type']]['label']; ?>(<?php echo $announce_types[$announce['type']]['color']; ?>)
	
				<?php endif; ?>
	
			</p>
		
			<?php if( !empty( $announce['range']['start'] ) or !empty( $announce['range']['end'] ) ) : ?>
			
				<?php foreach( $date_periods as $period_type => $period_label ) : ?>
				
					<p>
						<strong><?php echo $period_label; ?>:</strong>
						
						<?php if( !empty( $announce['range'][$period_type] ) && !empty( $announce['date'][$period_type] ) ) : ?>
						
							<code>
								<?php echo mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $announce['date'][$period_type] ); ?>
							</code>
						
						<?php endif; ?>
	
					</p>
				
				<?php endforeach; ?>
	
			<?php endif; ?>

		</div>

		<div class="inline">

			<p>
				<?php _e( 'Announce title' , $Afd->ltd ); ?>:
				<?php $this->print_form_fields( 'title' , 'edit' , $announce_id , $announce  ); ?>
			</p>
			<p>
				<?php _e( 'Announce type' , $Afd->ltd ); ?>:
				<?php $this->print_form_fields( 'type' , 'edit' , $announce_id , $announce  ); ?>
			</p>
			<p>
				<?php _e( 'Date Range' , $Afd->ltd ); ?>:
				<?php $this->print_form_fields( 'date_range' , 'edit' , $announce_id , $announce  ); ?>
			</p>

		</div>

	</td>

	<td class="manage-column column-content">
	
		<div class="show">

			<?php if( $Afd->Site->is_multisite ): ?>
			
				<p class="show-subsite-description <?php echo $announce['standard']; ?>">
					<strong><?php echo $multisite_show_standard[$announce['standard']]; ?></strong>
				</p>
				
				<?php if( !empty( $announce['subsites'] ) ): ?>
				
					<ul>
					
						<?php foreach( $announce['subsites'] as $blog_id => $v ) : ?>
						
							<?php $child_blog = get_blog_details( array( 'blog_id' => $blog_id ) ); ?>
							<li>[<?php echo $blog_id; ?>] <?php echo $child_blog->blogname; ?></li>
						
						<?php endforeach; ?>
					
					</ul>
				
				<?php endif; ?>
			
			<?php endif; ?>
			
			<?php echo wpautop( $announce['content'] ); ?>

		</div>
		
		<div class="inline">

			<div class="update-announce">
			
				<form class="<?php echo $Afd->ltd; ?>_form" method="post" action="<?php echo esc_url( $Afd->Helper->get_action_link() ); ?>">
				
					<input type="hidden" name="<?php echo $Afd->Form->field; ?>" value="Y">
					<?php wp_nonce_field( $Afd->Form->nonce . 'update_' . $this->name , $Afd->Form->nonce . 'update_' . $this->name ); ?>
				
					<?php if( $Afd->Site->is_multisite ): ?>
		
						<div>
							<?php _e( 'Default show for announce of Child-sites' , $Afd->ltd ); ?>:
							<?php $this->print_form_fields( 'show_standard' , 'edit' , $announce_id , $announce  ); ?>
						</div>
						<div>
							<?php _e( 'Select the sub-sites' , $Afd->ltd ); ?>:
							<?php $this->print_form_fields( 'subsites' , 'edit' , $announce_id , $announce  ); ?>
						</div>
		
					<?php endif; ?>

					<?php $this->print_form_fields( 'content' , 'edit' , $announce_id , $announce  ); ?>

					<div class="add-fields-inner"></div>
			
				</form>
			
			</div>

		</div>
	
	</td>

	<td class="manage-column column-role">
	
		<div class="show">

			<?php if( !empty( $announce['role'] ) ) : ?>
	
				<ul>
	
					<?php foreach( $announce['role'] as $role => $val ) : ?>
	
						<?php if( !empty( $all_user_roles[$role]['label'] ) ) : ?>
						
							<li><?php echo $all_user_roles[$role]['label']; ?></li>
							
						<?php endif; ?>
	
					<?php endforeach; ?>
	
				</ul>
	
			<?php endif; ?>
			
		</div>

		<div class="inline">

			<p>
				<?php $this->print_form_fields( 'user_role' , 'edit' , $announce_id , $announce  ); ?>
			</p>

		</div>

	</td>

	<td class="manage-column column-operation">
	
		<div class="show">

			<ul>
			
				<li><a class="announce-edit-inline button button-primary" href="javascript:void(0);" id="inline-id-<?php echo $announce_id; ?>"><?php _e( 'Edit' ); ?></a></li>
				<li><a class="announce-delete button" href="javascript:void(0);"><?php _e( 'Delete' ); ?></a></li>
			
			</ul>
			
		</div>
	
		<div class="inline">
		
			<ul>

				<li><a class="do-edit-announce button button-primary" href="javascript:void(0);"><?php _e( 'Save' ); ?></a></li>
				<li><a class="cancel-edit-announce button" href="javascript:void(0);" id="list-id-<?php echo $announce_id; ?>"><?php _e( 'Cancel' ); ?></a></li>

			</ul>
		
		</div>
	
		<span class="spinner"></span>
		
	</td>

</tr>
