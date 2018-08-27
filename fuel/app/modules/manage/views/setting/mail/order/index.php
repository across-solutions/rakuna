<?php echo View::forge('setting/mail/menu', array('selected' => 'order')); ?>

<?php echo $message(); ?>

<?php echo Form::open('/manage/setting/mail/order/save'); ?>

	<!--#config start -->
	<div class="config">
		<div id="orderMailLink" class="configWrap">
			<strong>
				発注受付用メール設定
			</strong>
			<p>
				発注を受けた場合に発注者へ自動返信するメールのタイトル、内容を設定します。
			</p>

			<dl class="clearfix">
				<dt>
					<label for="defMail">
						送信元メールアドレス<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('mail_from', Arr::get($data, 'mail_from'), array('placeholder' => '登録したいメールアドレスを入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>255文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('mail_from'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="orderMailTitle">
						発注受付メールタイトル<span class="red">*</span>
					</label>
				</dt>
				<dd>
					<?php echo Form::input('title', Arr::get($data, 'title'), array('placeholder' => '送信したいメールタイトルを入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>50文字以内で入力してください。">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('title'); ?>
				</dd>
			</dl>

			<dl class="clearfix">
				<dt>
					<label for="orderMailDec">
						発注受付メール内容<span class="red">*</span>
					</label>
				</dt>
				<dd class="hlong">
					<?php echo Form::textarea('message', Arr::get($data, 'message'), array('rows' => 20, 'placeholder' => '送信したいメール内容を入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>5,000文字以内で入力してください。<br/><br/>
						以下のキーワードは送信時に置換されます。<br/>
						{$manage-company}：管理企業名<br/>
						{$manage-name}：管理者名<br/>
						{$order-name}：発注者名<br/>
						{$order-date}：発注日時<br/>
						{$order-delivery-date}：納品希望日<br/>
						{$order-comment}：備考欄<br/>
						{$order-list}：発注内容<br/>
						{$order-amount-case}：ケース合計金額<br/>
						{$order-amount}：バラ合計金額<br/>
						{$order-payment}：合計金額<br/>
						{$order-tax}：消費税額
					">
						<span class="icon-question decEdit"></span>
					</a>
					<?php echo $validate_error_message('message'); ?>
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
	</div>
	<!--#config end -->
<?php echo Form::close(); ?>