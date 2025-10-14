<?php
for ($i = 0; $i < 5; $i++)
{
    $array[$i] = [];
}

for ($i = 0; $i < 5; $i++)
{
    for ($j = 0; $j < 5; $j++)
    {
        switch ($i)
        {
            case 0:
            $array[$i][$j] = 1;
            break;
            case 1:
                $array[$i][$j] = 2;
                break;
            case 2:
                $array[$i][$j] = 3;
                break;
            case 3:
                $array[$i][$j] = 4;
                break;
            default:
                $array[$i][$j] = 5;
        }
    }
}

print_r ($array[3]);
echo "<br><br>"
// print_r ($array[1]);
?>