<?php
/**
 * AfiTwilio Utility.
 *
 */
App::uses('File', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class AfiTwilio {

    /**
     * envia sms al telefono configurado desde las variables de Configuración de Afi Croogo
     * @param  integer $tokenGenerado
     * @param  string $usuario
     * @return json in console || false if not send sms
     */
	public static function enviar_sms( $tokenGenerado,$usuario ) {
		$tokenTwilio   = Configure::read('Afigestion.token_auth_sms'); // Your Auth Token from www.twilio.com/console
		$tel           = Configure::read('Afigestion.numero_de_telefono_sms');
        $sid           = Configure::read('Afigestion.twilio_sid');
        $emailFalloSms = Configure::read('Afigestion.email_fallo_sms');

        $sms = false;
        if( $tel != '0' ){
            $mensaje = "curl 'https://api.twilio.com/2010-04-01/Accounts/".$sid."/Messages.json' -X POST \
                --data-urlencode 'From=(205) 619-1827' \
                --data-urlencode 'To=".$tel."' \
                --data-urlencode 'MessagingServiceSid=MG5e1f20853d4988ed2253881ee4c1d019' \
                --data-urlencode 'Body=El usuario ".$usuario." quiere ver un padrón si desea que lo visualicé, darle este token:".$tokenGenerado."' \
                -u $sid:".$tokenTwilio." > ".WWW_ROOT."sms/resultado.json";
            $sms = exec( $mensaje );
            $file = new File(WWW_ROOT."sms/resultado.json");
            $json = $file->read(true, 'r');
            $jsonRespuestaSms = json_decode($json);
            if( $jsonRespuestaSms->status != 'accepted' ){
                //enviamos mail si no se envio el sms
                $Email = new CakeEmail();
                $Email->viewVars(array(
                    'usuario' => $usuario,
                    'tokenGenerado' => $tokenGenerado,
                ));
                $Email->template('Twilio.enviar_mail_por_sms_fallido');
                $Email->emailFormat('html');
                $Email->from(array(AFIGESTION_EMAIL_SISTEMAS => 'Afigestion'));
                $Email->to($emailFalloSms);
                $Email->subject('Token generado para visualizar padrón');
                $Email->send();
            }
        }
        echo $sms;
	}

}
