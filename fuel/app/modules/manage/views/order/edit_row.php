<?php $id = Arr::get($row, 'id'); ?>
<?php $item_id = Arr::get($row, 'item_id'); ?>

<tr id="item-<?php echo $item_id; ?>">

	<td class="left">
		<?php echo Arr::get($row, 'category_code'); ?><br>
		<?php echo Arr::get($row, 'category_name'); ?>
	</td>
	<td class="left">
		<?php echo Arr::get($row, 'item_code'); ?><br>
		<?php echo Arr::get($row, 'item_name'); ?>
	</td>

	<?php if (Common_Setting::is_price() && Common_Setting::is_case()) : ?>
		<td class="right">
			<?php echo Common_Util::format_number(Arr::get($row, 'price_case_tax')); ?>
		</td>
	<?php endif; ?>
	<?php if (Common_Setting::is_case()) : ?>
		<td class="center">
		  <?php if(empty($id)): ?>
			  <?php echo Form::input('new_amount_case[' . $item_id . ']', Arr::get($row, 'amount_case')); ?>
			  <?php echo $validate_error_message('new_amount_case.' . $item_id); ?>
		  <?php else: ?>
			  <?php echo Form::input('amount_case[' . $id . ']', Arr::get($row, 'amount_case')); ?>
			  <?php echo $validate_error_message('amount_case.' . $id); ?>
		  <?php endif; ?>
		</td>
	<?php endif; ?>
	<?php if (Common_Setting::is_price()) : ?>
		<td class="right">
			<?php echo Common_Util::format_number(Arr::get($row, 'price_tax')); ?>
		</td>
	<?php endif; ?>
	<td class="center">
		<?php if(empty($id)): ?>
			<?php echo Form::input('new_amount[' . $item_id . ']', Arr::get($row, 'amount')); ?>
			<?php echo $validate_error_message('new_amount.' . $item_id); ?>
		<?php else: ?>
			<?php echo Form::input('amount[' . $id . ']', Arr::get($row, 'amount')); ?>
			<?php echo $validate_error_message('amount.' . $id); ?>
		<?php endif; ?>
	</td>
</tr>
