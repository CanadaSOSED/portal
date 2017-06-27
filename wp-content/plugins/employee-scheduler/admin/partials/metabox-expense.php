<div class="shiftee-metabox" id="expenseinfo">

<table class="form-table">
	<tr>
		<th scope="row"><label><?php _e('Date', 'wpaesm'); ?></label></th>
		<td>
			<input id="thisdate" class="required" type="text" class="shiftee-date-picker" size="10" name="<?php $metabox->the_name('date'); ?>" value="<?php $metabox->the_value('date'); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php _e('Amount (miles or dollars)', 'wpaesm'); ?></label></th>
		<td>
			<input id="amount" type="text" size="10" name="<?php $metabox->the_name('amount'); ?>" value="<?php $metabox->the_value('amount'); ?>"/>
			<p class="explain"><?php _e('Numbers only, no currency symbol', 'wpaesm'); ?></p>
		</td>
	</tr>

</table>

</div>