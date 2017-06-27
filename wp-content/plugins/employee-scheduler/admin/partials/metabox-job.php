<div class="shiftee-metabox" id="jobinfo">

	<table class="form-table">

		<tr>
			<th scope="row"><label><?php _e('Pay Rate', 'wpaesm'); ?></label></th>
			<td>
				<input id="amount" type="number" size="4" name="<?php $metabox->the_name('pay_rate'); ?>" value="<?php $metabox->the_value('pay_rate'); ?>"/>
				<p class="explain"><?php _e('If you enter a pay rate here, it will over-ride the employee\'s default pay rate for this job.', 'wpaesm'); ?></p>
			</td>
		</tr>

	</table>

</div>