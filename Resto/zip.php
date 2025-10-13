<?php
    $rootPath = getcwd(); // Asigno a $rootPath el directorio en el que se encuentra.
    $zip = new ZipArchive(); // Inicializa el Objeto zip.
    $zip->open('backup.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE); // Lo crea o lo sobreescribe con el nombre backup.zip.

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY); // Crea la lista de arhivos del directorio $rootPath.

    foreach ($files as $name => $file) // Para cada nombre $name de archivo en la lista de archivos
    {
        if (!$file->isDir()) // Si el nombre de $file no es un directorio, los directorios y sus contenidos, los agrega automaticamente.
        {
            $fileName = substr($file, strlen($rootPath) + 1); // Asigna a $fileName solo el nombre del archivo, a la ruta completa al archivo $file, le resta la ruta del directorio más la \.
            $zip->addFile($file, $fileName); // Agrega los Archivos a backup.zip, $file es la ruta completa al archivo, $relativePath nombre del archivo que estará en el archivo backup.zip.
        }
    }

    $zip->close(); // Al cerrar el Objeto zip se crea el archivo backup.zip.
    echo '<script>if(!alert("Archivo backup.zip Guardado en la Carpeta del Proyecto.")) window.open("index.php", "_self");</script>'; // Muestro una alerta y vuelvo a index.php.
?>