<?php
/**
 * AfiTwilio Utility.
 *
 */

class AfiTwilio {

    /**
     * envia sms al telefono configurado desde las variables de Configuración de Afi Croogo
     * @param  integer $tokenGenerado
     * @param  string $usuario
     * @return json in console || false if not send sms
     */
	public static function enviar_sms( $tokenGenerado,$usuario ) {
		$tokenTwilio = Configure::read('Afigestion.token_auth_sms'); // Your Auth Token from www.twilio.com/console
		$tel         = Configure::read('Afigestion.numero_de_telefono_sms');
        $sid         = Configure::read('Afigestion.twilio_sid');

        $sms = false;
        if( $tel != '0' ){
            $mensaje = "curl 'https://api.twilio.com/2010-04-01/Accounts/".$sid."/Messages.json' -X POST \
                --data-urlencode 'From=(205) 619-1827' \
                --data-urlencode 'To=".$tel."' \
                --data-urlencode 'MessagingServiceSid=MG5e1f20853d4988ed2253881ee4c1d019' \
                --data-urlencode 'Body=El usuario ".$usuario." quiere ver un padrón si desea que lo visualicé, darle este token:".$tokenGenerado."' \
                -u $sid:".$tokenTwilio;
            $sms = exec( $mensaje );
        }
        echo $sms;
	}

}
