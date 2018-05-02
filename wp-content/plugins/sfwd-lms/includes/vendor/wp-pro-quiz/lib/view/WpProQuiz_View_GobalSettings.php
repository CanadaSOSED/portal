<?php
class WpProQuiz_View_GobalSettings extends WpProQuiz_View_View {
	
	public function show() {
?>		
<div class="wrap wpProQuiz_globalSettings">
	<h2 style="margin-bottom: 10px;"><?php echo sprintf( esc_html_x('%s Options', 'Quiz Options', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></h2>
	
	<a class="button-secondary" style="display:none" href="admin.php?page=ldAdvQuiz"><?php esc_html_e('back to overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
	
	<div class="wpProQuiz_tab_wrapper" style="padding: 10px 0px;">
		<a class="button-primary" href="#" data-tab="#globalContent"><?php echo sprintf( esc_html_x('%s Options', 'Quiz Options', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></a>
		<a class="button-secondary" href="#" data-tab="#emailSettingsTab"><?php esc_html_e('E-Mail settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
		<a class="button-secondary" href="#" data-tab="#problemContent"><?php esc_html_e('Settings in case of problems', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
	</div>
	
	<form method="post">
		<div id="poststuff">
			<div id="globalContent">
				
				<?php $this->globalSettings(); ?>
				
			</div>
			<div id="emailSettingsTab" style="display: none;">
				<?php $this->emailSettingsTab(); ?>
			</div>
			<div class="postbox" id="problemContent" style="display: none;">
				<?php $this->problemSettings(); ?>
			</div>
			<input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="<?php esc_html_e('Save', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>">
		</div>
	</form>
</div>
		
<?php 	
	}
	
	private function globalSettings() {

?>
		<div class="postbox">
			<h3 class="hndle"><?php esc_html_e('Global settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php esc_html_e('Leaderboard time format', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php esc_html_e('Leaderboard time format', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
									</legend>
									<label>
										<input type="radio" name="toplist_date_format" value="d.m.Y H:i" <?php $this->checked($this->toplistDataFormat, 'd.m.Y H:i'); ?>> 06.11.2010 12:50
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d g:i A'); ?>> 2010/11/06 12:50 AM
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:i A'); ?>> 2010/11/06 at 12:50 AM
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:ia" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:ia'); ?>> 2010/11/06 at 12:50am
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="F j, Y g:i a" <?php $this->checked($this->toplistDataFormat, 'F j, Y g:i a'); ?>> November 6, 2010 12:50 am
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="M j, Y @ G:i" <?php $this->checked($this->toplistDataFormat, 'M j, Y @ G:i'); ?>> Nov 6, 2010 @ 0:50
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="custom" <?php echo in_array($this->toplistDataFormat, array('d.m.Y H:i', 'Y/m/d g:i A', 'Y/m/d \a\t g:i A', 'Y/m/d \a\t g:ia', 'F j, Y g:i a', 'M j, Y @ G:i')) ? '' : 'checked="checked"'; ?> >
										<?php esc_html_e('Custom', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>:
										<input class="medium-text" name="toplist_date_format_custom" style="width: 100px;" value="<?php echo $this->toplistDataFormat; ?>">
									</label>
									<p>
										<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php esc_html_e('Documentation on date and time formatting', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
									</p>
								</fieldset>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php esc_html_e('Statistic time format', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php esc_html_e('Statistic time format', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
									</legend>
									
									<label>
										<?php esc_html_e('Select example:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
										<select id="statistic_time_format_select">
											<option value="0"></option>
											<option value="d.m.Y H:i"> 06.11.2010 12:50</option>
											<option value="Y/m/d g:i A"> 2010/11/06 12:50 AM</option>
											<option value="Y/m/d \a\t g:i A"> 2010/11/06 at 12:50 AM</option>
											<option value="Y/m/d \a\t g:ia"> 2010/11/06 at 12:50am</option>
											<option value="F j, Y g:i a"> November 6, 2010 12:50 am</option>
											<option value="M j, Y @ G:i"> Nov 6, 2010 @ 0:50</option>
										</select>
									</label>
									<div style="margin-top: 10px;">
										<label>
											<?php esc_html_e('Time format:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>:
											<input class="medium-text" name="statisticTimeFormat" value="<?php echo $this->statisticTimeFormat; ?>">
										</label>
										<p>
											<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php esc_html_e('Documentation on date and time formatting', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
										</p>
									</div>
								</fieldset>
							</td>
						</tr>
						<?php if (count($this->category)) { ?>
						<tr>
							<th scope="row">
								<?php esc_html_e('Question Category management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php esc_html_e('Question Category management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
									</legend>
									<select name="category">
										<option value=""><?php esc_html_e('Select Question Category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
										<?php foreach($this->category as $cat) { 
											echo '<option value="'.$cat->getCategoryId().'">'.$cat->getCategoryName().'</option>';
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="categoryEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php esc_html_e('Delete selected Question Category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?>" value="<?php esc_html_e('Delete', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="categoryDelete" class="button-secondary">
										<input type="button" title="<?php esc_html_e('Save changed to selected Question Category', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?>" value="<?php esc_html_e('Save Changes', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="categoryEdit" class="button-secondary">
										<div class="categorySpinner spinner"></div>
										<span class="categoryEditUpdate" style="display:none"><?php esc_html_e('Question Category Saved', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?></span>
										<span class="categoryDeleteUpdate" style="display:none"><?php esc_html_e('Question Category Deleted', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?></span>
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
						<?php if (count($this->templateQuiz)) { ?>
						<tr>
							<th scope="row">
								<?php echo sprintf( esc_html_x('%s template management', 'Quiz template management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' )); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php echo sprintf( esc_html_x('%s template management', 'Quiz template management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' )); ?></span>
									</legend>
									<select name="templateQuiz">
										<option value=""><?php echo sprintf( esc_html_x('Select %s template', 'Select Quiz template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' )); ?></option>
										<?php foreach($this->templateQuiz as $templateQuiz) { 
											echo '<option value="'.$templateQuiz->getTemplateId().'">'.esc_html($templateQuiz->getName()).'</option>';
											
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="templateQuizEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php echo sprintf( esc_html_x('Delete selected %s template', 'Delete selected Quiz template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' )) ?>" value="<?php esc_html_e('Delete', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="templateQuizDelete" class="button-secondary">
										<input type="button" title="<?php echo sprintf( esc_html_x('Save changed to selected %s template', 'Save changed to selected Quiz template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?>" value="<?php esc_html_e('Save Changes', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="templateQuizEdit" class="button-secondary">
										<div class="templateQuizSpinner spinner"></div>
										<span class="templateQuizEditUpdate" style="display:none"><?php echo sprintf( esc_html_x('%s template Saved', 'Quiz template Saved', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?></span>
										<span class="templateQuizDeleteUpdate" style="display:none"><?php echo sprintf( esc_html_x('%s template Deleted', 'Quiz template Deleted', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?></span>
										
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
						<?php if (count($this->templateQuestion)) { ?>
						<tr>
							<th scope="row">
								<?php esc_html_e('Question template management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php esc_html_e('Question template management', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
									</legend>
									<select name="templateQuestion">
										<option value=""><?php esc_html_e('Select Question template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></option>
										
										<?php foreach($this->templateQuestion as $templateQuestion) { 
											echo '<option value="'.$templateQuestion->getTemplateId().'">'.esc_html($templateQuestion->getName()).'</option>';
											
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="templateQuestionEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php esc_html_e('Delete selected Question template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?>" value="<?php esc_html_e('Delete', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="templateQuestionDelete" class="button-secondary">
										<input type="button" title="<?php esc_html_e('Save changed to selected Question template', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?>" value="<?php esc_html_e('Save Changes', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>" name="templateQuestionEdit" class="button-secondary">
										<div class="templateQuestionSpinner spinner"></div>
										<span class="templateQuestionEditUpdate" style="display:none"><?php esc_html_e('Question template Saved', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?></span>
										<span class="templateQuestionDeleteUpdate" style="display:none"><?php esc_html_e('Question template Deleted', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ?></span>
										
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

<?php
	}
	private function emailSettings() {
?>
		<div class="postbox" id="adminEmailSettings">
			<h3 class="hndle"><?php esc_html_e('Admin e-mail settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php esc_html_e('To:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[to]" value="<?php echo $this->email['to']; ?>" class="regular-text">
								</label>
								<p class="description">
									<?php esc_html_e('Separate multiple email addresses with a comma, e.g. wp@test.com, test@test.com', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('From Name:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[from_name]" value="<?php echo (isset($this->email['from_name']))? $this->email['from_name'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description"><?php esc_html_e('This is the email name of the sender. If not provided will default to the system email name.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('From Email:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<?php 
								if ( ( !empty( $this->email['from'] ) ) && ( !is_email( $this->email['from'] ) ) ) {
									?><p class="ld-error error-message"><?php esc_html_e('The value entered is not a valid email address', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></p><?php
								} 
								?>
								<label>
									<input type="text" name="email[from]" value="<?php echo (isset($this->email['from'])) ? $this->email['from'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php echo sprintf( wp_kses_post( __('This is the email address of the sender. If not provided the admin email <strong>(%s)</strong> will be used.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ), get_option('admin_email') ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Subject:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[subject]" value="<?php echo $this->email['subject']; ?>" class="regular-text">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('HTML', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="email[html]" value="1" <?php $this->checked(isset($this->email['html']) ? $this->email['html'] : false); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Message body:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<?php
									wp_editor($this->email['message'], 'adminEmailEditor', array('textarea_rows' => 20, 'textarea_name' => 'email[message]'));
								?>
								
								<div>
									<h4><?php esc_html_e('Allowed variables', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>:</h4>
									<ul>
										<li><span>$userId</span> - <?php esc_html_e('User-ID', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$username</span> - <?php esc_html_e('Username', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$quizname</span> - <?php esc_html_e('Quiz-Name', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$result</span> - <?php esc_html_e('Result in precent', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$points</span> - <?php esc_html_e('Reached points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$ip</span> - <?php esc_html_e('IP-address of the user', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$categories</span> - <?php esc_html_e('Category-Overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
									</ul>	
								</div>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
<?php 
	}
	
	private function userEmailSettings() {
?>
		<div class="postbox" id="userEmailSettings" style="display: none;">
			<h3 class="hndle"><?php esc_html_e('User e-mail settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php esc_html_e('From Name:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="text" name="userEmail[from_name]" value="<?php echo (isset($this->userEmail['from_name'])) ? $this->userEmail['from_name'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php esc_html_e('This is the email name of the sender. If not provided will default to the system email name.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('From Email:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<?php 
								if ( ( !empty( $this->email['from'] ) ) && ( !is_email( $this->userEmail['from'] ) ) ) {
									?><p class="ld-error error-message"><?php esc_html_e('The value entered is not a valid email address', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></p><?php
								} 
								?>
								<label>
									<input type="text" name="userEmail[from]" value="<?php echo (isset($this->userEmail['from'])) ? $this->userEmail['from'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php echo sprintf( wp_kses_post( __('This is the email address of the sender. If not provided the admin email <strong>(%s)</strong> will be used.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN) ), get_option('admin_email') ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Subject:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="text" name="userEmail[subject]" value="<?php echo $this->userEmail['subject']; ?>" class="regular-text">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('HTML', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="userEmail[html]" value="1" <?php $this->checked($this->userEmail['html']); ?>> <?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e('Message body:', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
							</th>
							<td>
								<?php
									wp_editor($this->userEmail['message'], 'userEmailEditor', array('textarea_rows' => 20, 'textarea_name' => 'userEmail[message]'));
								?>
								
								<div>
									<h4><?php esc_html_e('Allowed variables', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>:</h4>
									<ul>
										<li><span>$userId</span> - <?php esc_html_e('User-ID', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$username</span> - <?php esc_html_e('Username', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$quizname</span> - <?php esc_html_e('Quiz-Name', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$result</span> - <?php esc_html_e('Result in precent', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$points</span> - <?php esc_html_e('Reached points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$ip</span> - <?php esc_html_e('IP-address of the user', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
										<li><span>$categories</span> - <?php esc_html_e('Category-Overview', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></li>
									</ul>	
								</div>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
<?php 
	}
	
	private function problemSettings() {
		if($this->isRaw) {
			$rawSystem = esc_html__('to activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN);
		} else {
			$rawSystem = esc_html__('not to activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN);
		}

		?>
		
		<div class="updated" id="problemInfo" style="display: none;">
			<h3><?php esc_html_e('Please note', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
			<p>
				<?php esc_html_e('These settings should only be set in cases of problems with LD Advanced Quiz.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
			</p>
		</div>
		
		<h3 class="hndle"><?php esc_html_e('Settings in case of problems', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></h3>
		<div class="wrap">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e('Automatically add [raw] shortcode', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e('Automatically add [raw] shortcode', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="addRawShortcode" <?php echo $this->settings->isAddRawShortcode() ? 'checked="checked"' : '' ?> >
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?> <span class="description">( <?php printf(__('It is recommended %s this option on your system.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), '<span style=" font-weight: bold;">'.$rawSystem.'</span>'); ?> )</span>
								</label>
								<p class="description">
									<?php esc_html_e('If this option is activated, a [raw] shortcode is automatically set around LDAdvQuiz shortcode ( [LDAdvQuiz X] ) into [raw] [LDAdvQuiz X] [/raw]', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('Own themes changes internal  order of filters, what causes the problems. With additional shortcode [raw] this is prevented.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e('Do not load the Javascript-files in the footer', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e('Do not load the Javascript-files in the footer', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="jsLoadInHead" <?php echo $this->settings->isJsLoadInHead() ? 'checked="checked"' : '' ?> >
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('Generally all LDAdvQuiz-Javascript files are loaded in the footer and only when they are really needed.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('In very old Wordpress themes this can lead to problems.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('If you activate this option, all LDAdvQuiz-Javascript files are loaded in the header even if they are not needed.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e('Touch Library', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e('Touch Library', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="touchLibraryDeactivate" <?php echo $this->settings->isTouchLibraryDeactivate() ? 'checked="checked"' : '' ?> >
									<?php esc_html_e('Deactivate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('In Version 0.13 a new Touch Library was added for mobile devices.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('If you have any problems with the Touch Library, please deactivate it.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e('jQuery support cors', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e('jQuery support cors', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="corsActivated" <?php echo $this->settings->isCorsActivated() ? 'checked="checked"' : '' ?> >
									<?php esc_html_e('Activate', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</label>
								<p class="description">
									<?php esc_html_e('Is required only in rare cases.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('If you have problems with the front ajax, please activate it.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
								<p class="description">
									<?php esc_html_e('e.g. Domain with special characters in combination with IE', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e('Repair database', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e('Repair database', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></span>
								</legend>
								<input type="submit" name="databaseFix" class="button-primary" value="<?php esc_html_e('Repair database', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN);?>">
								<p class="description">
									<?php esc_html_e('No data will be deleted. Only LDAdvQuiz tables will be repaired.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php
	}
	
	private function emailSettingsTab() {
		?>
		
		<div class="wpProQuiz_tab_wrapper" style="padding-bottom: 10px;">
			<a class="button-primary" href="#" data-tab="#adminEmailSettings"><?php esc_html_e('Admin e-mail settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
			<a class="button-secondary" href="#" data-tab="#userEmailSettings"><?php esc_html_e('User e-mail settings', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></a>
		</div>
		
		<?php $this->emailSettings(); ?>
		<?php $this->userEmailSettings(); ?>
		
		<?php 
	}
}