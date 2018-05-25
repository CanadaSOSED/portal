<?php
class WpProQuiz_View_FrontToplist extends WpProQuiz_View_View {
	
	public function show() {
?>
<div style="margin-bottom: 30px; margin-top: 10px;" class="wpProQuiz_toplist" data-quiz_id="<?php echo $this->quiz->getId(); ?>">
	<?php if(!$this->inQuiz) { ?>
	<h2><?php esc_html_e('Leaderboard', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?>: <?php echo $this->quiz->getName(); ?></h2>
	<?php } ?>
	<table class="wpProQuiz_toplistTable">
		<caption><?php printf(__('maximum of %s points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN), '<span class="wpProQuiz_max_points">'. $this->points .'</span>'); ?></caption>
		<thead>
			<tr>
				<th style="width: 40px;"><?php esc_html_e('Pos.', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
				<th style="text-align: left ;"><?php esc_html_e('Name', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
				<th style="width: 140px;"><?php esc_html_e('Entered on', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
				<th style="width: 60px;"><?php esc_html_e('Points', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
				<th style="width: 75px;"><?php esc_html_e('Result', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="5"><?php esc_html_e('Table is loading', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></td>
			</tr>
			<tr style="display: none;">
				<td colspan="5"><?php esc_html_e('No data available', LEARNDASH_WPPROQUIZ_TEXT_DOMAIN); ?></td>
			</tr>
			<tr style="display: none;">
				<td></td>
				<td style="text-align: left ;"></td>
				<td style=" color: rgb(124, 124, 124); font-size: x-small;"></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>

<?php 
	}
}