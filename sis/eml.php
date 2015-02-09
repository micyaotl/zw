<?php
/**
 * Funciones de mail smtp
 * 
 * @package ZihWeb CMS
 * @subpackage eml
 * @version 0.5
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2010 feelRiviera.com
 */

class Email {
	var $mail; 
	
	public function __construct() {
		global $gcfg;
		include_once ZW_DIR.'sis'.Ds.'api'.Ds.'class.phpmailer.php';
		//  info@retiromaya.com|mail.retiromaya.com|mail@retiromaya.com|meamomucho
		$mailconfig = explode('|', $gcfg['mailconfig']);
		$this->mail = new PHPMailer();
		$this->mail->IsSMTP();
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPDebug  = false;
		$this->mail->Port = 587;
		$this->mail->Host = $mailconfig[1];
		$this->mail->Username = $mailconfig[2];
		$this->mail->Password = $mailconfig[3];
		$this->mail->CharSet = "UTF-8";
		$this->mail->IsSendmail();
	}
	
	public function &reqReserve() {
		global $cls_cfg, $gcfg;
		$lang = $cls_cfg->idis;
		$fecha = date("D d M Y H:i");
		$s_mail = $_POST['myemail']; 
		$s_name = $_POST['myname']; 
		if (isset($_POST['myroom'])) $s_room = $_POST['myroom'];;
		$s_date = $_POST['mydate'];
		$s_out = $_POST['mydateout'];
		$s_days = $_POST['mydays'];
		$s_phone = $_POST['myphone'];
		$s_city = $_POST['mycity'];
		$s_country = $_POST['mycountry'];
		$s_guests = $_POST['myguests'];
		if (isset($_POST['mychild'])) $s_child = $_POST['mychild'];
		if (isset($_POST['mytransfer'])) {
			$s_transfer = $_POST['mytransfer'];
			if($s_transfer!='yes'){
				$s_transfer='no';
			}
		}
	
		$to =  $gcfg['mail-reservations'].""; 
		$subject = "New Reservation Request from Website";//$_REQUEST['mysubject'];
		if($lang == 'es'){
		$subjectodo = "Gracias Desde ".$cls_cfg->gcfg['title'];
		}else if($lang == 'en'){
		$subjectodo = "Thank You From ".$cls_cfg->gcfg['title'];
		}else if($lang == 'it'){
		$subjectodo = "Grazie, ".$cls_cfg->gcfg['title'];
		}else if($lang == 'de'){
		$subjectodo = "Vielen Dank, ".$cls_cfg->gcfg['title'];
		}
		$message = "Name: $s_name\n"; 
		$message .= "Email: $s_mail\n";
		$message .= "Phone: $s_phone\n";
		$message .= "City: $s_city\n";
		$message .= "Country: $s_country\n";
		if (isset($_POST['myroom']))  $message .= "Room Type: $s_room\n"; 
		$message .= "Checkin Date: $s_date\n";
		$message .= "Checkout Date: $s_out\n";
		if (isset($_POST['mytransfer'])) $message .= "Transfer: $s_transfer\n";
		if (isset($_POST['mydays'])) { $message .= "Number of Nights: $s_days\n"; }
		$message .= "Guests: $s_guests\n";
		if (isset($_POST['mychild'])) $message .= "Number of Children: $s_child\n";
		$message .= $_POST['mybody'];
		$message .= "\n\n ".$s_mail." send this message the : ".$fecha." from ".ZW_URL." to " .$to;
		$message = utf8_decode($message);
		if($lang == 'en'){
		$messagetodo = "Thank you for your reservation inquiry. \n";
		$messagetodo .= "We will reply as soon as possible. \n";
		}else if($lang == 'es'){
		$messagetodo = "Gracias por su solicitud de reservaci&oacute;n. \n";
		$messagetodo .= "Contestaremos a la brevedad posible. \n";
		}else if($lang == 'it'){
		$messagetodo = "Grazie per  l'invio della proposta di prenotazione. \n";
		$messagetodo .= "Una risposta verra inviata il piò presto possibile. \n";
		}else if($lang == 'de'){
		$messagetodo = "Vielen Dank für Ihre Reservierungsanfrage. \n";
		$messagetodo .= "Wir weren Ihnen in Körze antworten.\n";
		}
		$messagetodo .= $cls_cfg->gcfg['title'];
		$messagetodo .= ZW_URL;
		$headers = "From: $s_name <$s_mail>\n";   
		$headers .= "Reply-To: $s_name <$s_mail>\n"; 
		if (mail($to,$subject,$message,$headers)) {
			mail('info@feelriviera.com',$subject,$message,$headers);
		   echo "&results=Thank you. Your request has been sent.";
		} else { 
		   echo "&results=Error request was not sent.";
		}
		mail($s_mail,$subjectodo,$messagetodo,$headers);
		exit();
	}
	
	public function usrMail() {
		global $cls_cfg;
		$mensajesend = '
'.$_POST['comm'].'<br />
<br />
   Mensaje enviado desde la web<br />
   '.$ZW_URL;
	
		$mensajesend = utf8_decode(remTxt($mensajesend));
		$emlusr = $_POST['mail'];
		//$nomusr = $_POST['nom'].' >> PuzzleWedding.com';
		//$mail->IsSendmail();
		$this->mail->SetLanguage ( $cls_cfg->idis);
		$this->mail->SetFrom($emlusr);
		$this->mail->Subject = $_POST['subj'];
	
		$this->mail->AddReplyTo($emlusr);
		$this->mail->From = $emlusr;
		$this->mail->FromName = $emlusr;
	
		$this->mail->WordWrap = 80;
		$this->mail->AltBody = strip_tags($mensajesend);
		$this->mail->Body = $mensajesend;
	
		$this->mail->MsgHTML($mensajesend);
		$this->mail->IsHTML (true);
	
		$this->mail->AddAddress ('info@feelriviera.com');
	
	}
	
}

$cls_eml = new Email();

function reqReserve() {
	global $cls_eml;
	return $cls_eml->reqReserve();
}


include_once ZW_DIR.'sis'.Ds.'api'.Ds.'class.phpmailer.php';
//  info@retiromaya.com|mail.retiromaya.com|mail@retiromaya.com|meamomucho
$mailconfig = explode('|', $gcfg['mailconfig']);
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug  = false;   
$mail->Port = 587;
$mail->Host = $mailconfig[1];
$mail->Username = $mailconfig[2];
$mail->Password = $mailconfig[3];
$mail->CharSet = "UTF-8";
$mail->IsSendmail();

/**
 * ADMIN Mail
 */
function sendAdminMail() {
	global $gcfg, $_POST, $cls_cnt, $mailconfig;
	include_once ZW_DIR.'sis'.Ds.'api'.Ds.'class.phpmailer.php';
	//  info@retiromaya.com|mail.retiromaya.com|mail@retiromaya.com|meamomucho
	$mailconfig = explode('|', $gcfg['mailconfig']);
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->SMTPDebug  = false;   
	$mail->Port = 587;
	$mail->Host = $mailconfig[1];
	$mail->Username = $mailconfig[2];
	$mail->Password = $mailconfig[3];
	$mail->CharSet = "UTF-8";
	$mail->IsSendmail();
	$mensajesend = $cls_cnt->remTxt($gcfg['mailheader'].$_POST['msgedit'].$gcfg['mailfooter']);
	$mensajesend = $cls_cnt->remTxt($mensajesend);
	$mensajesend = utf8_decode($mensajesend);
	$emlusr = $mailconfig[0];
	$mail->SetFrom($emlusr);
	$mail->Subject = $_POST['subj'];
	$mail->AddReplyTo($emlusr);
	$mail->From = $emlusr;
	$mail->FromName = $emlusr;
	$mail->WordWrap = 80;
	$mail->AltBody = strip_tags($mensajesend);
	$mail->Body = $mensajesend;
	$mail->MsgHTML($mensajesend);
	$mail->IsHTML (true);
	$mail->AddAddress ($_POST['dest']);
	
	$maila = new PHPMailer();
	$maila->IsSMTP();
	$maila->SMTPAuth = true;
	$maila->SMTPDebug  = false;   
	$maila->Port = 587;
	$maila->Host = $mailconfig[1];
	$maila->Username = $mailconfig[2];
	$maila->Password = $mailconfig[3];
	$maila->CharSet = "UTF-8";
	$maila->SetFrom($emlusr);
	$maila->Subject = '('.$_POST['dest'].') '.$_POST['subj'];
	$maila->AddReplyTo($emlusr);
	$maila->From = $emlusr;
	$maila->FromName = $emlusr;
	$maila->WordWrap = 80;
	$maila->AltBody = strip_tags($mensajesend);
	$maila->Body = $mensajesend;
	$maila->MsgHTML($mensajesend);
	$maila->IsHTML (true);
	$maila->AddAddress('info@feelriviera.com');
	$maila->Send();
	unset($maila);
}


/**
 * USR Mail
 */
function usrMail() {
	global $mail, $_POST;
		$mensajesend = '
'.$_POST['comm'].'<br />
<br />
   Mensaje enviado desde la web<br />
   '.$ZW_URL;

		$mensajesend = utf8_decode(remTxt($mensajesend));
		$emlusr = $_POST['mail'];
		//$nomusr = $_POST['nom'].' >> PuzzleWedding.com';
		//$mail->IsSendmail();
		//if ($cfg['idi'] != 'en') { $mail->SetLanguage ($cfg['idi']); }
		$mail->SetFrom($emlusr);
		$mail->Subject = $_POST['subj'];
		
		$mail->AddReplyTo($emlusr);
		$mail->From = $emlusr;
		$mail->FromName = $emlusr;
		
		$mail->WordWrap = 80;
		$mail->AltBody = strip_tags($mensajesend);
		$mail->Body = $mensajesend;

		$mail->MsgHTML($mensajesend);
		$mail->IsHTML (true);

		$mail->AddAddress ('info@feelriviera.com');
	
}
/**
function reqReserve() {
	global $cls_cfg, $gcfg;
	$lang = $cls_cfg->idis;
	$fecha = date("D d M Y H:i");
	$s_mail = $_POST['myemail']; 
	$s_name = $_POST['myname']; 
	if (isset($_POST['myroom'])) $s_room = $_POST['myroom'];;
	$s_date = $_POST['mydate'];
	$s_out = $_POST['mydateout'];
	$s_days = $_POST['mydays'];
	$s_phone = $_POST['myphone'];
	$s_city = $_POST['mycity'];
	$s_country = $_POST['mycountry'];
	$s_guests = $_POST['myguests'];
	if (isset($_POST['mychild'])) $s_child = $_POST['mychild'];
	if (isset($_POST['mytransfer'])) {
		$s_transfer = $_POST['mytransfer'];
		if($s_transfer!='yes'){
			$s_transfer='no';
		}
	}

	$to =  $gcfg['mail-reservations'].""; 
	$subject = "New Reservation Request from Website";//$_REQUEST['mysubject'];
	if($lang == 'es'){
	$subjectodo = "Gracias Desde ".$cls_cfg->gcfg['title'];
	}else if($lang == 'en'){
	$subjectodo = "Thank You From ".$cls_cfg->gcfg['title'];
	}else if($lang == 'it'){
	$subjectodo = "Grazie, ".$cls_cfg->gcfg['title'];
	}else if($lang == 'de'){
	$subjectodo = "Vielen Dank, ".$cls_cfg->gcfg['title'];
	}
	$message = "Name: $s_name\n"; 
	$message .= "Email: $s_mail\n";
	$message .= "Phone: $s_phone\n";
	$message .= "City: $s_city\n";
	$message .= "Country: $s_country\n";
	if (isset($_POST['myroom']))  $message .= "Room Type: $s_room\n"; 
	$message .= "Checkin Date: $s_date\n";
	$message .= "Checkout Date: $s_out\n";
	if (isset($_POST['mytransfer'])) $message .= "Transfer: $s_transfer\n";
	if (isset($_POST['mydays'])) { $message .= "Number of Nights: $s_days\n"; }
	$message .= "Guests: $s_guests\n";
	if (isset($_POST['mychild'])) $message .= "Number of Children: $s_child\n";
	$message .= $_POST['mybody'];
	$message .= "\n\n ".$s_mail." send this message the : ".$fecha." from ".ZW_URL." to " .$to;
	if($lang == 'en'){
	$messagetodo = "Thank you for your reservation inquiry. \n";
	$messagetodo .= "We will reply as soon as possible. \n";
	}else if($lang == 'es'){
	$messagetodo = "Gracias por su solicitud de reservaci&oacute;n. \n";
	$messagetodo .= "Contestaremos a la brevedad posible. \n";
	}else if($lang == 'it'){
	$messagetodo = "Grazie per  l'invio della proposta di prenotazione. \n";
	$messagetodo .= "Una risposta verra inviata il piò presto possibile. \n";
	}else if($lang == 'de'){
	$messagetodo = "Vielen Dank für Ihre Reservierungsanfrage. \n";
	$messagetodo .= "Wir weren Ihnen in Körze antworten.\n";
	}
	$messagetodo .= $cls_cfg->gcfg['title'];
	$messagetodo .= ZW_URL;
	$headers = "From: $s_name <$s_mail>\n";   
	$headers .= "Reply-To: $s_name <$s_mail>\n"; 
	if (mail($to,$subject,$message,$headers)) {
		mail('info@feelriviera.com',$subject,$message,$headers);
	   echo "&results=Thank you. Your request has been sent.";
	} else { 
	   echo "&results=Error request was not sent.";
	}
	mail($s_mail,$subjectodo,$messagetodo,$headers);
	exit();
}

*/