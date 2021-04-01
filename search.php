<?php
echo "<h1>Cross Word:</h1>";

$words = $_POST['words'];

echo "<b>Find the words:</b><br>";
foreach ($words as $w) {
    echo $w . '<br>';
}
echo '<hr>';

// constants
$scale = 500;
$center = 25;

// blank out table
array_multisort(array_map('strlen', $words), $words);
$words = array_reverse($words);
for($i = 0; $i < $scale; $i++) {
    for($j = 0; $j < $scale; $j++) {
        $table[$i][$j] = 'X';
    }    
}

// place the first word in the table
for($i = 0; $i < strlen($words[0]); $i++) {
    $table[$center][$center + $i] = $words[0][$i];
}
$onTable = [];

$obj = [
    'word' => $words[0],
    'posX' => $center,
    'posY' => $center,
    'dir' => 0,
];

$onTable[0] = $obj;
array_splice($words, 0, 1);


// insert into the table
function insert($word, $posX, $posY, $dir) {
    global $table;

    if($dir == 0) {
        for($i = 0; $i < strlen($word); $i++) {
            $table[$posX][$posY + $i] = $word[$i]; 
        }
    }
    else {
        for($i = 0; $i < strlen($word); $i++) {
            $table[$posX + $i][$posY] = $word[$i]; 
        }
    }
}


// determine if an insertion interferes with the table
function doesntInterfere($word, $posX, $posY, $dir) {
    global $table;    

    if($dir == 0) {
        for($i = 0; $i < strlen($word); $i++) {
            if(strcmp($word[$i], $table[$posX][$posY + $i]) && strcmp('X', $table[$posX][$posY + $i]))
                return false;

            $top3 = 0;
            if(strcmp($table[$posX - 1][$posY + $i - 1], 'X'))
                $top3 += 1;
            if(strcmp($table[$posX - 1][$posY + $i], 'X'))
                $top3 += 1;
            if(strcmp($table[$posX - 1][$posY + $i + 1], 'X'))
                $top3 += 1;

            $bot3 = 0;
            if(strcmp($table[$posX + 1][$posY + $i - 1], 'X'))
                $bot3 += 1;
            if(strcmp($table[$posX + 1][$posY + $i], 'X'))
                $bot3 += 1;
            if(strcmp($table[$posX + 1][$posY + $i + 1], 'X'))
                $bot3 += 1;
            
            if($top3 > 1 || $bot3 > 1) {
                return false;
            }
        }
    }
    else {
        for($i = 0; $i < strlen($word); $i++) {
            if(strcmp($word[$i], $table[$posX + $i][$posY]) && strcmp('X', $table[$posX + $i][$posY]))
                return false;

           $top3 = 0;
            if(strcmp($table[$posX + $i - 1][$posY - 1], 'X'))
                $top3 += 1;
            if(strcmp($table[$posX + $i][$posY - 1], 'X'))
                $top3 += 1;
            if(strcmp($table[$posX + $i + 1][$posY - 1], 'X'))
                $top3 += 1;

            $bot3 = 0;
            if(strcmp($table[$posX + $i - 1][$posY + 1], 'X'))
                $bot3 += 1;
            if(strcmp($table[$posX + $i][$posY + 1], 'X'))
                $bot3 += 1;
            if(strcmp($table[$posX + $i + 1][$posY + 1], 'X'))
                $bot3 += 1;
            
            if($top3 > 1 || $bot3 > 1) {
                return false;
            }
        }
    }
    
    return true;
}

// get the position of a letter in a word on the table
function getLetterPos($letterNum, $posX, $posY, $dir) {
    if($dir == 0) {
        return [$posX, $posY + $letterNum]; 
    }
    else {
        return [$posX + $letterNum, $posY]; 
    }
}

// get the position of the first character in a word on the table

    function getWordOrigin($letterNum, $posX, $posY, $dir) {
    if($dir == 0) {
        return [$posX, $posY - $letterNum];
    }
    else {
        return [$posX - $letterNum, $posY];
    }
}


// 0 = left to right, 1 = up to down
$direction = 1;

loop:
// for each word not on the board
for($i = 0; $i < count($words); $i++) {

    // for each word on the board
    for($j = 0; $j < count($onTable); $j++) {
        
        $a = $words[$i];
        $b = $onTable[$j];

        //echo 'Comparing: ' . $a . ' to ' . $b['word'] . '<br>';
        
        // for each letter in word a
        for($l = 0; $l < strlen($a); $l++) {
            
            // for each letter in word b
            for($k = 0; $k < strlen($b['word']); $k++) {

                // if they share a character
                if($a[$l] == $b['word'][$k]) {
                    $posB = getLetterPos($k, $b['posX'], $b['posY'], $b['dir']);
                    $posA = getWordOrigin($l, $posB[0], $posB[1], $direction); 

                    if($direction != $b['dir'] && doesntInterfere($a, $posA[0], $posA[1], $direction)) {
                        //echo 'Inserting<br>';

                        insert($a, $posA[0], $posA[1], $direction);
                        array_splice($words, $i, 1);
                   
                        // another word has been added to the table 
                        $obj = [
                            'word' => $a,
                            'posX' => $posA[0],
                            'posY' => $posA[1],
                            'dir' => $direction,
                        ];
                        array_push($onTable, $obj);
                        
                        // change direction
                        $direction = !$direction;
                        goto loop;
                    }
                }
            }
        }
    }
}

// find the height of the crossword
for($i = 0; $i < $scale; $i++) {
    for($j = 0; $j < $scale; $j++) {
        // the first character
        if(strcmp($table[$i][$j], 'X')) {
            $startR = $i;     
            goto endR;
        }
    }
}
endR:
for($i = $startR; $i < $scale; $i++) {
    $allX = true;
    for($j = 0; $j < $scale; $j++) {
        // the first character
        if(strcmp($table[$i][$j], 'X')) {
            $allX = false;
        }
    }
    if($allX) {
        $endR = $i;
        goto startC;
    }        
}
// find the width of the crossword
startC:
for($j = 0; $j < $scale; $j++) {
    for($i = 0; $i < $scale; $i++) {
        // the first character
        if(strcmp($table[$i][$j], 'X')) {
            $startC = $j;     
            goto endC;
        }
    }
}
endC:
for($j = $startC; $j < $scale; $j++) {
    $allX = true;
    for($i = 0; $i < $scale; $i++) {
        // the first character
        if(strcmp($table[$i][$j], 'X')) {
            $allX = false;
        }
    }
    if($allX) {
        $endC = $j;
        goto printC;
    }        
}
printC:

// print the crossword
echo "<p style='font-family: monospace'>";
for($i = $startR; $i < $endR; $i++) {
    for($j = $startC; $j < $endC; $j++) {
        
        if(strcmp($table[$i][$j], 'X')) {
            echo $table[$i][$j] . ' ';
        }
        else {
            echo '.  ';
        }
    }    
    echo '<br>';
}
echo '</p>';

?>
