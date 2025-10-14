<?php
/**
 * Script de Reorganización de Estructura de Proyecto
 * Este script mueve archivos a la nueva estructura manteniendo todas las funcionalidades
 */

echo "🚀 Iniciando reorganización del proyecto Barbery...\n\n";

// Definir la estructura de carpetas a crear
$directories = [
    'app/auth',
    'app/client/appointments',
    'app/client/invoices',
    'app/admin',
    'app/public',
    'api/auth',
    'api/client',
    'api/invoices',
    'api/services',
    'api/backup',
    'config',
    'assets/css',
    'assets/js',
    'assets/img',
    'database/backups'
];

// Crear directorios
echo "📁 Creando estructura de directorios...\n";
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "✅ Creado: $dir\n";
    } else {
        echo "⚠️  Ya existe: $dir\n";
    }
}

echo "\n📦 Moviendo archivos...\n";

// Mapeo de archivos antiguos a nuevos
$fileMap = [
    // AUTH
    'index.php' => 'app/auth/index.php',
    'register.php' => 'api/auth/register.php',
    'recover.php' => 'app/auth/recover.php',
    'endsession.php' => 'api/auth/logout.php',
    
    // CLIENT
    'profile.php' => 'app/client/profile.php',
    'modify.php' => 'api/client/update.php',
    'delete.php' => 'api/client/delete.php',
    'getdata.php' => 'api/client/getdata.php',
    
    // APPOINTMENTS
    'request.php' => 'app/client/appointments/request.php',
    'turn.php' => 'app/client/appointments/turn.php',
    'turnview.php' => 'app/client/appointments/turnview.php',
    
    // INVOICES
    'invoice.php' => 'app/client/invoices/invoice.php',
    'lastinvoice.php' => 'app/client/invoices/lastinvoice.php',
    'showinvoices.php' => 'app/client/invoices/showinvoices.php',
    'addInvoice.php' => 'api/invoices/add.php',
    'export.php' => 'api/invoices/export.php',
    'saveIt.php' => 'api/invoices/save.php',
    'showtotal.php' => 'app/admin/showtotal.php',
    
    // ADMIN
    'admin.php' => 'app/admin/admin.php',
    'admin.html' => 'app/admin/admin.html',
    'admin-online.html' => 'app/admin/admin-online.html',
    'clients.php' => 'app/admin/clients.php',
    
    // SERVICES
    'added.php' => 'api/services/add.php',
    'modrem.php' => 'api/services/remove.php',
    'remove.php' => 'api/services/delete.php',
    
    // PUBLIC
    'contact.php' => 'app/public/contact.php',
    
    // BACKUP
    'db-backup.php' => 'api/backup/db-backup.php',
    'zip.php' => 'api/backup/zip.php',
    
    // CONFIG
    'connect.php' => 'config/database.php',
    
    // DATABASE
    'barbery.sql' => 'database/barbery.sql',
    
    // ASSETS
    'css/style.css' => 'assets/css/style.css',
    'js/script.js' => 'assets/js/script.js'
];

// NO mover, solo informar
echo "\n⚠️  IMPORTANTE: Este es un script de PRUEBA\n";
echo "Para mayor seguridad, recomiendo hacer los cambios manualmente o usar un sistema de control de versiones.\n\n";

echo "📋 Resumen de cambios propuestos:\n\n";
foreach ($fileMap as $old => $new) {
    if (file_exists($old)) {
        echo "  $old → $new\n";
    }
}

echo "\n\n⚠️  ANTES DE CONTINUAR:\n";
echo "1. Haz un backup completo de la carpeta Barbery\n";
echo "2. Asegúrate de tener control de versiones (Git)\n";
echo "3. Los includes deberán actualizarse en cada archivo\n\n";

echo "🔧 ¿Deseas que genere un archivo config.php con las nuevas rutas? (y/n): ";

?>
