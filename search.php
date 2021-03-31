<?php
echo "<h1>Cross Word:</h1>";

$words = $_POST['words'];

echo "<b>Find the words:</b><br>";
foreach ($words as $w) {
    echo $w . '<br>';
}
echo '<hr>';

// constants
$scale = 50;
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
    'word' => $word,
    'posX' => $center,
    'poxY' => $center,
    'dir' => 0,
];

//$onTable[0] = $obj;
//array_splice($words, 0, 0);

// insert into the table
function insert($word, $posX, $posY, $dir) {
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
    if($dir == 0) {
        for($i = 0; $i < strlen($word); $i++) {
            if($table[$posX][$posY + $i] == 'X') {
                continue;
            }
            else if($table[$posX][$posY + $i] != $word[$i]) {
                return false;
            } 
        }
    }
    else {
        for($i = 0; $i < strlen($word); $i++) {
            if($table[$posX + $i][$posY] == 'X') {
                continue;
            } 
            else if($table[$posX + $i][$posY] != $word[$i]) {
                return false;
            } 
        }
    }
    return true;
}

// get the position of a letter in a word on the table
function getLetterPos($letterNum, $posX, $posY, $dir) {
    if($dir == 0) {
        return [$posX + $letterNum, $posY]; 
    }
    else {
        return [$posX, $posY + $letterNum]; 
    }
}

// get the position of the first character in a word on the table
function getWordOrigin($letterNum, $posX, $posY, $dir) {
    if($dir == 0) {
        return [$posX - $letterNum, $posY];
    }
    else {
        return [$posX, $posY - $letterNum];
    }
}


// 0 = left to right, 1 = up to down
$direction = 1;

// for each word not on the board
for($i = 0; $i < count($words); $i++) {

    // for each word on the board
    for($j = 0; $j < count($onTable); $j++) {
        
        $a = $words[i];
        $b = $onTable[j];

        echo 'Comparing: ' . $a . ' to ' . $b['word'] . '<br>';
        
        // for each letter in word a
        for($l = 0; $l < strlen($a); $l++) {
            
            // for each letter in word b
            for($k = 0; $k < strlen($b['word']); $k++) {
                
                // if they share a character
                if($a[$l] == $b['word'][$k]) {
                    $posB = getLetterPos($k, $b['posX'], $b['posY'], $b['dir']);
                    $posA = getWordOrigin($l, $posB[0], $posB[1], $direction); 

                    if(doesntInterfere($a, $posA[0], $posA[1], $direction)) {
                        insert($a, $posA[0], $posA[1], $direction);
                        array_splice($words, $i, $i);
                   
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
                    }
                }
            }
        }
    }
}

// print the crossword
echo "<p style='font-family: monospace'>";
for($i = 0; $i < 50; $i++) {
    for($j = 0; $j < 50; $j++) {
        echo $table[$i][$j] . ' ';
    }    
    echo '<br>';
}
echo '</p>';

?>
