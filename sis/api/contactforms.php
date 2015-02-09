<?php

class ContactForms {
	public function __construct() {
		
	}
	
	public function contact($dest, $remi, $fields) {
		
	}
}

function getReserveForm($tip='cnt') {
	global $cls_cfg, $cls_cnt;
	$eno = '[en]';
	$enc = '[/en]';
	$eso = '[es]';
	$esc = '[/es]';
	$arrival = textoEnIdioma($eno.'Arrival'.$enc.$eso.'Llegada'.$esc);
	$depart = textoEnIdioma($eno.'Depart'.$enc.$eso.'Salida'.$esc);
	$nights = textoEnIdioma($eno.'Nights'.$enc.$eso.'Noches'.$esc);
	$selroom = textoEnIdioma($eno.'Select a Villa'.$enc.$eso.'Seleccione una Villa'.$esc);
	//$adults = textoEnIdioma($eno.'Adults +13yo'.$enc.$eso.'Adultos +13'.$esc);
	$people = textoEnIdioma($eno.'People'.$enc.$eso.'Personas'.$esc);
	//$childs = textoEnIdioma($eno.'Childs -13yo'.$enc.$eso.'Ni&ntilde;os -13'.$esc);
	$comnam = textoEnIdioma($eno.'Complete Name'.$enc.$eso.'Nombre Completo'.$esc);
	$email = textoEnIdioma($eno.'Email'.$enc.$eso.'Correo'.$esc);
	$phone = textoEnIdioma($eno.'Phone'.$enc.$eso.'Tel&eacute;fono'.$esc);
	$city = textoEnIdioma($eno.'City'.$enc.$eso.'Ciudad'.$esc);
	$country = textoEnIdioma($eno.'Country'.$enc.$eso.'Pais'.$esc);
	$message = textoEnIdioma($eno.'Message or comments'.$enc.$eso.'Mensaje o comentarios'.$esc);
	$send = textoEnIdioma($eno.'Send'.$enc.$eso.'Enviar'.$esc);
	$extrabed =  textoEnIdioma('('.$eno.'Extra bed'.$enc.$eso.'Cama extra'.$esc.'  15 usd)');
	$sitetitle = $cls_cfg->gcfg['title'];
	$texto = textoEnIdioma($eso.'Al enviar este formulario esta haciendo una solicitud directa de reservaci&oacute;n con la oficina de <strong>'.$sitetitle.'</strong>.'.$esc.$eno.'To inquire about date availability please fill out this form and we will contact you within 12 hrs with a reply for your dates'.$enc);

	$jsjq = <<< EOD
var dates = jQ('#mydate,#mydateout').datepicker({
	defaultDate: "+1d",
	numberOfMonths: 1,
	changeMonth: false,
	changeYear: false,
	autoSize: false,
	minDate: +1,
	maxDate: '+1y',
	dateFormat: 'yy-mm-dd',
	onSelect: function (selectedDate) {
			var option = this.id == "mydate" ? "minDate" : "maxDate";
			var instance = jQ(this).data("datepicker");
			var date = jQ.datepicker.parseDate(instance.settings.dateFormat || jQ.datepicker._defaults.dateFormat, selectedDate, instance.settings);
			dates.not(this).datepicker("option", option, date);
			datepicked();
			return false;
		}
	});
var datepicked = function () {
	var mydate = jQ('#mydate');
	var mydateout = jQ('#mydateout');
	var mydays = jQ('#mydays');
	var fromDate = mydate.datepicker('getDate');
	var toDate = mydateout.datepicker('getDate');
	
	if (toDate && fromDate) {
		var difference = 0;
		var oneDay = 86400000;
		difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
		mydays.val(difference);
	}
	return false;
};

jQ('INPUT.auto-hint, TEXTAREA.auto-hint, SELECT.auto-hint').focus(function(){
	if(jQ(this).val() === jQ(this).attr('title')){ 
		jQ(this).val('');
		jQ(this).removeClass('auto-hint');
	}
});
jQ('INPUT.auto-hint, TEXTAREA.auto-hint, SELECT.auto-hint').blur(function(){
	if(jQ(this).val() === '' && jQ(this).attr('title') !== ''){ 
		jQ(this).val(jQ(this).attr('title'));
		jQ(this).addClass('auto-hint'); 
	}
});
jQ('INPUT.auto-hint, TEXTAREA.auto-hint, SELECT.auto-hint').each(function(){
	if(jQ(this).attr('title') === ''){ return; }
	if(jQ(this).val() === ''){ jQ(this).val(jQ(this).attr('title')); }
	else { jQ(this).removeClass('auto-hint'); } 
});
  });
  
  jQ(function() {
  jQ('input').focus(function(){
    //jQ(this).css({border:"solid 1px #000000"});
  });
  jQ('input').blur(function(){
    //jQ(this).css({border:"solid 1px #3366cc"});
  });

  jQ("#send").click(function() {
	var arr = jQ("input#mydate").val();
		if (arr === "" || arr ==='Arrival') {
      jQ("input#mydate").focus();
      return false;
    }
	var dep = jQ("input#mydateout").val();
		if (dep === "" || dep ==='Depart') {
      jQ("input#mydateout").focus();
      return false;
    }
	/*var room = jQ("#myroom:selected").text();
	if (room === "" || room ==='Select a Room') {
      jQ("#myroom").css({border:"solid 1px #000000"});
      return false;
    } else{
		jQ("#myroom").css({border:"solid 1px #3366cc"});
		return true;
	}
	 var guest = jQ("#myguests:selected").text();
		if (guest === "" || guest ==='Adults + 8') {
      jQ("#myguests").css({border:"solid 1px #000000"});
      return false;
    } else{
		jQ("#myguests").css({border:"solid 1px #3366cc"});
		return true;
	}
	 var child = jQ("#mychild:selected").text();
		if (child === "" || child ==='Child - 8') {
      jQ("#mychild").css({border:"solid 1px #000000"});
      return false;
    } else{
		jQ("#mychild").css({border:"solid 1px #3366cc"});
		return true;
	}*/
	  var name = jQ("input#myname").val();
		if (name === "" || name ==='$comnam') {
      jQ("input#myname").focus();
      return false;
    }
		var email = jQ("input#myemail").val();
		if (email === "" || email==='Email') {
      jQ("input#myemail").focus();
      return false;
    }
		var phone = jQ("input#myphone").val();
		if (phone === "" || phone==='Phone') {
      jQ("input#myphone").focus();
      return false;
    }
	
		var dataString = jQ('#customForm').serialize();
		
		jQ.ajax({
      type: "POST",
      url: "/sm.zw?nr=true",
      data: dataString,
      success: function() {
        jQ('#customForm').html("<div id='message'></div>");
        jQ('#message').html("<h2>Booking Request Submitted!</h2>")
        .append("<p>We will be in touch soon.</p>")
        .hide()
        .fadeIn(1500, function() {
          jQ('#message').append("<img id='checkmark' src='/lib/img/ok.png' />");
        });
      }
     });
    return false;
	});
EOD;
	$hdx  = <<< EOD
	<script>
	//<![CDATA[
		var jQ = jQuery.noConflict();
		jQ(document).ready(function(){
			$jsjq
		});
	//]]>
	</script>
EOD;

$cnt = <<< EOD
<form method="post" id="customForm">
<fieldset>
<input id="mydate" name="mydate" type="text" title="$arrival" class="shortform auto-hint"/>&nbsp;<input id="mydateout" name="mydateout" type="text" title="$depart" class="shortform auto-hint"/>
<input id="mydays" name="mydays" type="text" value="" title="$nights" class="miniform auto-hint" disabled/>
<br class="clearboth">
<select id="myroom" name="myroom" class="midform auto-hint" title="$selroom">
	<option value="none">$selroom</option>
	<option value="2">Villa 2</option>
	<option value="3">Villa 3</option>
	<option value="4">Villa 4</option>
	<option value="5">Villa 5</option>
	<option value="6">Villa 6</option>
	<option value="7">Villa 7</option>
	<option value="8">Villa 8</option>
</select>&nbsp;

<br class="clearboth" />
<select id="myguests" name="myguests" title="Guests" class="selform auto-hint">
	<option value="none">$people</option>
	<option>1</option>
	<option>2</option>
	<option>3</option>
	<option>4</option>
</select>
&nbsp;$extrabed

<br class="clearboth">
<input id="myname" name="myname" type="text" title="$comnam" class="midform auto-hint" />
<br class="clearboth">
<input id="myemail" name="myemail" type="text" title="$email" class="midform auto-hint"/>
<br class="clearboth"/>
<input id="myphone" name="myphone" type="text" title="$phone" class="midform auto-hint"/>
<br class="clearboth"/>
<input id="mycity" name="mycity" type="text" title="$city" class="midform auto-hint"/>
<br class="clearboth"/>
<input id="mycountry" name="mycountry" type="text" title="$country" class="midform auto-hint"/>
<br class="clearboth"/>
<!--input id="mytransfer" name="mytransfer" type="checkbox" value="yes"> Add Airport to Hotel Transfer</input-->
<br class="clearboth"/>
<textarea id="mybody" name="mybody" title="$message" class="longform auto-hint"></textarea>

<br class="clearboth">
<input id="send" name="send" class="send" type="submit" Value="$send"/> &nbsp;<span id="nameInfo"></span>
<div id="output"><p>$texto</p></div>
</fieldset>
</form>
EOD;

/*
 * <select id="mychild" name="mychild" title="child" class="selform auto-hint">
	<option value="none">$childs</option>
	<option>0</option>
	<option>1</option>
	<option>2</option>
</select>
 */

	
	//$cls_cnt->jsjq .= $jsjq;
	//$cls_cnt->jo['resform'] = true;
	//$cls_cnt->cnt .= $cnt;
	return $$tip;
}