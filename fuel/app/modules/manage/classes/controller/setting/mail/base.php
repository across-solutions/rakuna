<?php
namespace Manage;
use Fuel\Core\Input;
use Fuel\Core\Response;
/**
 * メール設定基底コントローラクラス
 */
abstract class Controller_Setting_Mail_Base extends Controller_Base {
	
	/**
	 * メールテンプレート区分を返す
	 */
	abstract protected function get_template_mail_div();
	
	/**
	 * 初期表示
	 */
	public function action_index() {
		$data = $this->get_template_mail();
		if (empty($data)) {
			throw new \HttpServerErrorException();
		}
		$this->render($data);
	}
	
	/**
	 * 更新バリデート
	 * 
	 * @param array $data フォームデータ
	 */
	protected function validate_edit($data) {
		$validation = $this->create_validation();

		$validation->add('mail_from', '送信元メールアドレス')
			->add_rule('required')
			->add_rule('max_length', 255)
			->add_rule('simple_email');
		$validation->add('title', 'メールタイトル')
			->add_rule('required')
			->add_rule('max_length', 50);
		$validation->add('message', 'メール本文')
			->add_rule('required')
			->add_rule('max_length', 5000);
		
		return $this->validate($validation, $data);
	}
	
	/**
	 * メールテンプレートを取得する
	 */
	protected function get_template_mail() {
		return \Model_Template_Mail::query()
			->where('mail_div', $this->get_template_mail_div())
			->get_one();
	}
	
	/**
	 * メールテンプレートを更新する
	 * 
	 * @param Model_Template_Mail $template_mail 元データ
	 * @param array $data フォームデータ
	 */
	protected function update_template_mail($template_mail, $data) {
		$fields = array('mail_from', 'title', 'message');
		\Common_Util::copy($template_mail, $data, $fields);

		return $template_mail->save() !== false;
	}
}