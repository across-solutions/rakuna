<?php echo View::forge('setting/mail/menu', array('selected' => 'notice')); ?>

<?php echo $message(); ?>

<?php echo Form::open('/manage/setting/mail/notice/save'); ?>
	<!--#config start -->
	<div class="config">
		<div id="newsLink" class="configWrap">
			<strong>
				お知らせ用メール設定
			</strong>
			<p>
				発注者へお知らせメールを送信する際のメールタイトル、内容を設定します。
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
					<label for="newsMailTitle">
						お知らせメールタイトル<span class="red">*</span>
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
					<label for="newsMailDec">
						お知らせメール内容<span class="red">*</span>
					</label>
				</dt>
				<dd class="hlong">
					<?php echo Form::textarea('message', Arr::get($data, 'message'), array('rows' => 20, 'placeholder' => '送信したいメール内容を入力してください。')); ?>
					<a class="tooltip" rel="tooltip" title="必須項目です。<br/>5,000文字以内で入力してください。<br/><br/>
						以下のキーワードは送信時に置換されます。<br/>
						{$manage-company}：管理企業名<br/>
						{$manage-name}：管理者名<br/>
						{$order-name}：発注者名<br/>
						{$info-title}：お知らせタイトル<br/>
						{$info-text}：お知らせ本文<br/>
						{$info-url}：お知らせURL
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