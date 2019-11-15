<?php
	namespace Library\Utils;
	
	class Mail
	{
		public static function sendMail( $from_head, $array_mail, $to )
	{

	// Headers
	$headers  ='From: "'.$from_head.'"<'.$to.'>'."\r\n"; 
	$headers .= 'Mime-Version: 1.0'."\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	$headers .= "\r\n";

	// Message
	$message = '<table class="eleveur" width="90%" border="0" cellpadding="0" cellspacing="2" align="center" style="background-color:#FFF;">
		<tr>
			<td width="20%" class="txt" align="right" style="background-color:#FFF;">
				 Message de : &nbsp;
			</td>
			<td width="80%" style="background-color:#FFF;">
				 <strong>'.$array_mail['nom'].' '.$array_mail['prenom'].'</strong>
			</td>
		</tr>
		<tr>

			<td class="txt" align="right" style="background-color:#FFF;">
				 le : &nbsp;
			</td>
			<td style="background-color:#FFF;">
			'.$array_mail['date'].'		</td>
		</tr>
		<tr>
			<td class="txt" align="right" style="background-color:#FFF;">

				 Type de contact :  &nbsp;
			</td>
			<td style="background-color:#FFF;">
			'.$array_mail['sujet'].'	</td>
		</tr>
		<tr>
			<td class="txt" align="right" style="background-color:#FFF;">
				 email :  &nbsp;

			</td>
			<td style="background-color:#FFF;">
				 <a href="mailto:'.$array_mail['mail'].'"><font color="#0066FF"><u>'.$array_mail['mail'].'</u></font></a>
			</td>
		</tr>
		<tr>
			<td class="txt" align="right" style="background-color:#FFF;">
				 Téléphone : &nbsp; 
			</td>

			<td style="background-color:#FFF;">
			'.$array_mail['telephone'].'	</td>
		</tr>
		<tr>
				<td class="txt" align="right" style="background-color:#FFF;">
					Adresse IP : &nbsp; 
				</td>
				<td style="background-color:#FFF;">

				'.$array_mail['ip'].'	</td>
		</tr>
        <tr><td colspan="2" style="background-color:#FFF;">&nbsp;</td></tr>
		<tr>
			<td class="txt_int" colspan="2" align="center" style="font-size:13px;text-align:center;">
				<strong>MESSAGE</strong>
			</td>
		</tr>

		<tr>
			<td colspan="2" style="border:1px #CCCCCC solid;background-color:#FFF;">
	 '.$array_mail['texte'].'</td>
		</tr>
	</table>
';

	// Function mail()
	mail($to, $array_mail['sujet'], $message, $headers);
	}
}
