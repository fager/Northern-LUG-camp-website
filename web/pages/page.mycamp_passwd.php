<?php

require_once('lib/inc.database.php');

$PAGE['passwd']['name'] = "Passwort &auml;ndern";
$PAGE['passwd']['navilevel'] = 2;
$PAGE['passwd']['login_required'] = 1;
$PAGE['passwd']['phpclass'] = 'HtmlPage_passwd';
$PAGE['passwd']['parent'] = 'mycamp';

class HtmlPage_passwd extends HtmlPage {

	var $data = Array();
	var $errors = Array();

	function _readInput() {
		$this->data['a'] = http_get_var('a');
		$this->data['alt'] = strip_tags(http_get_var('frm_passwd_alt'));
		$this->data['neu1'] = strip_tags(http_get_var('frm_passwd_neu1'));
		$this->data['neu2'] = strip_tags(http_get_var('frm_passwd_neu2'));
	}

	function _verifyInput() {
		global $_SESSION;
		$username = $_SESSION['_username'] ? $_SESSION['_username'] : '';
		$accountid = $_SESSION['_accountid'] ? $_SESSION['_accountid'] : 0;

		$SQL1 = "SELECT accountid FROM account WHERE username='".my_escape_string($username)."' AND passwd=MD5('".$this->data['alt']."')";
		$res1 = my_query($SQL1);
		if($res1) {
			$row1 = mysql_fetch_assoc($res1);
			mysql_free_result($res1);
			if($row1 && $row1['accountid'] = $accountid) {
				// success
			}else{
				$this->errors['alt'] = 'Passwort falsch eingegeben';
			}
		}

		if($this->data['neu1'] != $this->data['neu2']) {
			$this->errors['missmatch'] = 'Passw&ouml;rter stimmen nicht &uuml;berein.';
		}

		if(strlen($this->data['neu1'])<6)
			$this->errors['toshort'] = 'Das neue Passwort ist zu kurz. Bitte w&auml;hle ein Passwort mit mindestens 6 Zeichen.';

	}

	function getPasswordForm() {
		$ret = '
		<p>Hier kannst Du Dein Passwort &auml;ndern. Die &Auml;nderung wird sofort wirksam. Bitte gebe zur Kontrolle Dein altes Passwort an.</p>
		<form action="?" method="post">
			<input type="hidden" name="p" value="passwd"/>
			<input type="hidden" name="a" value="1"/>
			<p>
			<label for-id="frm_passwd_alt">Altes Passwort:</label><br/>
			<input type="password" name="frm_passwd_alt" id="frm_passwd_alt"/>
			</p>

			<p>
			<label for-id="frm_passwd_neu1">Neues Passwort:</label><br/>
			<input type="password" name="frm_passwd_neu1" id="frm_passwd_neu1"/><br/>
			<label for-id="frm_passwd_neu2">Wiederholung:</label><br/>
			<input type="password" name="frm_passwd_neu2" id="frm_passwd_neu2"/>
			</p>
			<p>
			<input type="submit" value="Passwort &auml;ndern"/>
			</p>
		</form>
		';
		return $ret;
	}

	function updatePassword() {
		global $_SESSION;

		$ret = '';

		$username = $_SESSION['_username'] ? $_SESSION['_username'] : '';
		$accountid = $_SESSION['_accountid'] ? $_SESSION['_accountid'] : 0;

		$SQL1 = "UPDATE account SET passwd=MD5('".my_escape_string($this->data['neu1'])."') WHERE username='".my_escape_string($username)."' AND passwd=MD5('".$this->data['alt']."')";
		$res1 = my_query($SQL1);
		$ctr = my_affected_rows();
		if($ctr==1) {
			$ret .= '
			<p>Das Passwort wurde erfolgreich ge&auml;ndert.</p>
			';
		}else{
			$ret .= '
			<!-- Das klappte nich-->
			';
		}
		return $ret;
	}

	function getContent() {

		// Checken, ob die Seite wegen Wartungsarbeiten ausgeschaltet werden soll.
		// Funktion checkMaintenance() kommt aus class.HtmlPage.php
		$ret = $this->checkMaintenance();
		if($ret!='')
			return $ret;

		$this->_readInput();

    $ret .= '
		<h1>Passwort &auml;ndern</h1>
		';
		$a = http_get_var('a');
		switch($a) {
			case '1':
				$this->_verifyInput();
				if(count($this->errors)>0) {
					$ret .= $this->getPasswordForm();
					$ret .= '<ul>';
					foreach($this->errors as $k=>$v) {
						$ret .= '<li>'.$v.'</li>';
					}
					$ret .= '</ul>';
				}else{
					$ret .= $this->updatePassword();
				}
			break;
			default:
				$ret .= $this->getPasswordForm();
			break;
		}
		return $ret;
	}

}


?>
