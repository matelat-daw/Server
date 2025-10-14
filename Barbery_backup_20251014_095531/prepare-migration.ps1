# Script de Preparación para Reorganización
# Ejecutar desde PowerShell en: c:\Nginx-Server\html\Barbery

Write-Host "🚀 Preparando reorganización del proyecto Barbery..." -ForegroundColor Cyan
Write-Host ""

# 1. Crear backup
$backupPath = "c:\Nginx-Server\html\Barbery_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Write-Host "📦 Creando backup en: $backupPath" -ForegroundColor Yellow

if (Test-Path $backupPath) {
    Write-Host "⚠️  El backup ya existe" -ForegroundColor Red
} else {
    Copy-Item -Path "c:\Nginx-Server\html\Barbery" -Destination $backupPath -Recurse
    Write-Host "✅ Backup creado exitosamente" -ForegroundColor Green
}

Write-Host ""

# 2. Crear estructura de directorios
Write-Host "📁 Creando nueva estructura de directorios..." -ForegroundColor Yellow

$directories = @(
    "app\auth",
    "app\client\appointments",
    "app\client\invoices",
    "app\admin",
    "app\public",
    "api\auth",
    "api\client",
    "api\invoices",
    "api\services",
    "api\backup",
    "config",
    "assets\css",
    "assets\js",
    "assets\img",
    "database\backups"
)

foreach ($dir in $directories) {
    $fullPath = "c:\Nginx-Server\html\Barbery\$dir"
    if (!(Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        Write-Host "  ✅ Creado: $dir" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Ya existe: $dir" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "✅ Estructura de directorios creada" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Siguientes pasos:" -ForegroundColor Cyan
Write-Host "  1. Revisa el archivo MIGRACION.md para el plan completo"
Write-Host "  2. Revisa el archivo config/paths.php para la configuración de rutas"
Write-Host "  3. Comienza moviendo archivos según el mapeo en MIGRACION.md"
Write-Host "  4. Actualiza los includes en cada archivo movido"
Write-Host ""
Write-Host "⚠️  IMPORTANTE: NO elimines los archivos originales hasta verificar que todo funciona" -ForegroundColor Red
Write-Host ""
Write-Host "¿Deseas un reporte de los archivos a mover? (S/N): " -NoNewline -ForegroundColor Yellow
$respuesta = Read-Host

if ($respuesta -eq "S" -or $respuesta -eq "s") {
    Write-Host ""
    Write-Host "📊 Archivos PHP en el directorio raíz:" -ForegroundColor Cyan
    Get-ChildItem -Path "c:\Nginx-Server\html\Barbery\*.php" | Select-Object Name | Format-Table -AutoSize
    
    Write-Host "Total: $((Get-ChildItem -Path 'c:\Nginx-Server\html\Barbery\*.php').Count) archivos" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "✨ Preparación completada!" -ForegroundColor Green
