<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email? : El email de destino}';

    protected $description = 'Prueba el envío de correos';

    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';

        $this->info("Intentando enviar correo de prueba a: {$email}");
        $this->info("Configuración actual:");
        $this->info("  - MAIL_MAILER: " . config('mail.default'));
        $this->info("  - MAIL_HOST: " . config('mail.mailers.smtp.host'));
        $this->info("  - MAIL_PORT: " . config('mail.mailers.smtp.port'));
        $this->info("  - MAIL_FROM: " . config('mail.from.address'));
        $this->newLine();

        try {
            $this->info('Enviando correo...');

            Mail::to($email)->send(new TestEmail($email));

            $this->info('✓ Correo enviado exitosamente');
            $this->info("Revisa tu email ({$email}) para verificar que llegó.");
            $this->newLine();
            $this->info('Si no recibes el correo:');
            $this->info('1. Revisa la carpeta de Spam/Basura');
            $this->info('2. Verifica que la dirección de correo sea correcta');
            $this->info('3. Revisa los logs: tail -f storage/logs/laravel.log');

        } catch (\Exception $e) {
            $this->error('✗ Error al enviar correo:');
            $this->error($e->getMessage());
            $this->newLine();

            Log::error('Error enviando email de prueba: ' . $e->getMessage());

            $this->info('Información de debugging:');
            $this->info('- Verifica que MAIL_USERNAME y MAIL_PASSWORD sean correctos');
            $this->info('- Si usas Gmail, asegúrate de usar App Passwords, no la contraseña normal');
            $this->info('- Verifica que el puerto 587 no esté bloqueado por firewall');
            $this->info('- Ejecuta: php artisan config:clear');

            return 1;
        }

        return 0;
    }
}
