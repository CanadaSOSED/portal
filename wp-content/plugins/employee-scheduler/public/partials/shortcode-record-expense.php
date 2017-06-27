<?php

/**
 * Extra Work shortcode
 *
 * Display a form where users can record work they do outside of scheduled shifts.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 */
?>

<p><?php _e( 'Use this form to record your expenses and mileage.', 'employee-scheduler' ); ?></p>
<form method="post" action="<?php the_permalink(); ?>" id="shiftee-expense-form" enctype="multipart/form-data">
	<p>
		<label><?php _e( 'Date', 'employee-scheduler' ); ?></label>
		<input type="text" name="shiftee-expense-date" id="shiftee-expense-date" class="shiftee-date-picker" required>
	</p>
	<p>
		<label><?php _e( 'Expense Type', 'employee-scheduler' ); ?></label>
		<select name="shiftee-expense-type" id="shiftee-expense-type" required>
			<option value=""> </option>';
			<?php echo $this->expense_category_dropdown(); ?>
		</select>
	<p>
		<label><?php _e( 'Amount (currency or number of miles)', 'employee-scheduler' ); ?></label>
		<input type="number" min="0" value="0" step=".01" name="shiftee-expense-amount" id="shiftee-expense-amount" required>
	</p>
	<?php echo $this->job_dropdown(); ?>
	<p>
		<label><?php _e( 'Description', 'employee-scheduler' ); ?></label>
		<textarea name="shiftee-expense-description" id="shiftee-expense-description"></textarea>
	</p>
	<p>
		<label><?php _e( 'Receipt', 'employee-scheduler' ); ?></label>
		<input type="file" name="shiftee-expense-receipt" id="shiftee-expense-receipt" accept="image/*">
	</p>
	<?php wp_nonce_field( 'shiftee_record_expense', 'shiftee_record_expense_nonce' ); ?>
	<p>
		<input type="submit" name="shiftee-expense-form" id="shiftee-expense-form" value="<?php _e( 'Record Expense', 'employee-scheduler' ); ?>">
	</p>
</form>

