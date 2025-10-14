<?php
include "includes/conn.php";
$tables = array();
$i = 0;
$stmt = $conn->prepare("SHOW TABLES");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_NUM))
{
    $tables[$i] = $row[0];
    $i++;
}

$sqlScript = "";
foreach ($tables as $table)
{
    $stmt = $conn->prepare("SHOW CREATE TABLE $table");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $sqlScript .= "\n\n" . $row[1] . ";\n\n";    
    $stmt = $conn->prepare("SELECT * FROM $table");
    $stmt->execute();
    $count = $stmt->columnCount();
    for ($i = 0; $i < $count; $i++)
    {
        while ($row = $stmt->fetch(PDO::FETCH_NUM))
        {
            $sqlScript .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $count; $j ++) {
                $row[$j] = $row[$j];
                
                if (isset($row[$j])) {
                    $sqlScript .= '"' . $row[$j] . '"';
                } else {
                    $sqlScript .= '""';
                }
                if ($j < ($count - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }
    $sqlScript .= "\n"; 
}

if(!empty($sqlScript))
{
    if (!file_exists('DBase'))
    {
        mkdir('DBase', 0777, true);
    }
    $backup_file_name = 'DBase/barbery_backup.sql';
    $fileHandler = fopen($backup_file_name, 'w+');
    $number_of_lines = fwrite($fileHandler, $sqlScript);
    fclose($fileHandler);
}
echo "<script>if (!alert('La Base de Datos ha Sido Guardada en la Carpeta DBase, Dentro del Sitio.')) window.close()</script>";
?>