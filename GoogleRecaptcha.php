<?php
#---------------------------------
//參數 secret key
class GoogleRecaptcha {
	/* Google recaptcha API url */
	//https://www.google.com/recaptcha/admin
	private $google_url = "https://www.google.com/recaptcha/api/siteverify";
	private $secretkey;

	function __construct($secretkey = "") {
		$this->set_secretkey($secretkey);
	}

	#--------- 設定類 --------------------
	#設定secretkey
	public function set_secretkey($value = "") {
		$this->secretkey = $value;
	}

	public function VerifyCaptcha($response) {
		//google recaptcha 驗證
		$url = $this->google_url . "?secret=" . $this->secretkey . "&response=" . $response;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		$curlData = curl_exec($curl);

		curl_close($curl);

		$res = json_decode($curlData, TRUE);
		if ($res['success'] == 'true') {
			return TRUE;
		} else {
			return FALSE;
		}

	}

}

class GoogleRecaptchaJs {
	private $sitekey;
	private $language;

	function __construct($sitekey = "", $language = "zh-TW") {
		$this->set_sitekey($sitekey);
		$this->set_language($language);
	}

	#--------- 設定類 --------------------
	#設定sitekey
	public function set_sitekey($value = "") {
		$this->sitekey = $value;
	}

	#設定language
	public function set_language($value = "") {
		$this->language = $value;
	}

	//產生語法
	public function render($ajaxUrl="",$message = "message", $id = "my-widget") {
		$ajaxUrl=$ajaxUrl ?$ajaxUrl:$_SERVER['PHP_SELF'];
		$main = "
			<script type='text/javascript' src='https://www.google.com/recaptcha/api.js?hl={$this->language}&onload=onloadCallback&render=explicit' async defer></script>
      <script type='text/javascript'>
      	// recaptcha API 參考 https://developers.google.com/recaptcha/docs/display
        var verifyCallback = function(response) {

          // 如果 JavaScript 驗證成功
          if (response) {

      			//console.log(response);
            \$.post('{$ajaxUrl}', { 'g-recaptcha-response': response,'op':'captcha' }, function(data) {
                // 如果 PHP 驗證成功
                if(data){
                  \$('#{$message}').hide();
                }else{
                  \$('#{$message}').show();
                }
            });
          }
        };
        var onloadCallback = function() {
          grecaptcha.render(
            '{$id}', {                              // widget 驗證碼視窗在 id='my-widget' 顯示
                'sitekey' : '{$this->sitekey}',     // API Key
                'callback' : verifyCallback,        // 要呼叫的回調函式
                'theme' : 'dark'                    // 主題
            }
          );
        };
      </script>
		";
		return $main;
	}

}
