<?php echo View::forge('setting/user/menu', array('selected' => 'maintenance')); ?>

<?php echo $message(); ?>

<!--#config start -->
	<div class="config">
	<?php echo Form::open('/manage/setting/user/maintenance/save'); ?>
		<div id="accLink" class="configWrap">
			<strong>
				メンテナンス設定
			</strong>
			<p>
				メンテナンスの設定をおこないます。
			</p>

			<dl class="clearfix">
				<dt>
					<label for="maintenanceFlg">
						メンテナンス表示<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::radio('maintenance_flg', DISPLAY, Arr::get($data, 'maintenance_flg'), array('id' => 'form_maintenance_flg_' . DISPLAY)); ?>
					<?php echo Form::label('メンテナンスを表示する', 'maintenance_flg_' . DISPLAY); ?>
					<?php echo Form::radio('maintenance_flg', NON_DISPLAY, Arr::get($data, 'maintenance_flg'), array('id' => 'form_maintenance_flg_' . NON_DISPLAY)); ?>
					<?php echo Form::label('メンテナンスを表示しない', 'maintenance_flg_' . NON_DISPLAY); ?>
					<?php echo $validate_error_message('maintenance_flg'); ?>
				</dd>
			</dl>

			<!--#dig nav start -->
			<div class="digNav configN">
				<ul>
					<li>
						<a href="#" title="保存" class="submit">
							<span class="icon-save mr"></span>保存
						</a>
					</li>
				</ul>
			</div>
			<!--#dig nav end -->
		</div>
	<?php echo Form::close(); ?>
</div>
<!--#config end -->