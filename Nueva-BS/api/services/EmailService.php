<?php
/**
 * Email Service - Maneja el envío de correos electrónicos
 * Usa la configuración de sendmail para Gmail
 */

class EmailService {
    private $fromEmail;
    private $fromName;
    private $baseUrl;

    public function __construct() {
        // Configuración del remitente
        $this->fromEmail = 'no-reply@mitienda.com';
        $this->fromName = 'Mi Tienda Online';
        
        // URL base para links de activación
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->baseUrl = $protocol . '://' . $host . '/Nueva-BS';
    }

    /**
     * Enviar email de activación de cuenta
     */
    public function sendActivationEmail($email, $username, $token) {
        // Cambiar el enlace para que apunte a Nueva-BS
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $nuevaBSUrl = $protocol . '://' . $host . '/Nueva-BS';
        $activationLink = $nuevaBSUrl . '/#/activate?token=' . urlencode($token);
        
        $subject = 'Activa tu cuenta - ' . $this->fromName;
        
        $htmlMessage = $this->getActivationEmailTemplate($username, $activationLink);
        
        return $this->sendEmail($email, $subject, $htmlMessage);
    }

    /**
     * Template HTML para email de activación
     */
    private function getActivationEmailTemplate($username, $activationLink) {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa tu cuenta</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="margin: 0; color: white; font-size: 32px; font-weight: 900;">🎉 ¡Bienvenido!</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px;">
                            <h2 style="margin: 0 0 20px 0; color: #333; font-size: 24px; font-weight: 700;">
                                Hola {$username},
                            </h2>
                            <p style="margin: 0 0 20px 0; color: #666; font-size: 16px; line-height: 1.6;">
                                Gracias por registrarte en <strong>{$this->fromName}</strong>. Estamos emocionados de tenerte con nosotros.
                            </p>
                            <p style="margin: 0 0 20px 0; color: #666; font-size: 16px; line-height: 1.6;">
                                Para completar tu registro y comenzar a disfrutar de nuestros productos, por favor activa tu cuenta haciendo clic en el botón de abajo:
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Button -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;" align="center">
                            <a href="{$activationLink}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);">
                                ✓ Activar Mi Cuenta
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Alternative Link -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <p style="margin: 0 0 10px 0; color: #999; font-size: 14px; line-height: 1.6;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin: 0; word-break: break-all;">
                                <a href="{$activationLink}" style="color: #667eea; font-size: 14px;">{$activationLink}</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Warning -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #fff8e1; border-top: 2px solid #ffc107;">
                            <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">
                                ⚠️ <strong>Importante:</strong> Este enlace expirará en 24 horas por seguridad.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f9f9f9; border-top: 1px solid #e0e0e0; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #999; font-size: 14px;">
                                Si no creaste esta cuenta, puedes ignorar este correo.
                            </p>
                            <p style="margin: 0; color: #999; font-size: 12px;">
                                © 2025 {$this->fromName}. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Función principal de envío de email
     */
    private function sendEmail($to, $subject, $htmlMessage) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        $headersString = implode("\r\n", $headers);

        // Enviar email usando mail() (que usa sendmail)
        $sent = mail($to, $subject, $htmlMessage, $headersString);

        // Log para debugging
        if ($sent) {
            error_log("Email enviado exitosamente a: {$to}");
        } else {
            error_log("Error al enviar email a: {$to}");
        }

        return $sent;
    }

    /**
     * Generar token seguro de activación
     */
    public static function generateActivationToken() {
        return bin2hex(random_bytes(32)); // Token de 64 caracteres
    }
}
?>
