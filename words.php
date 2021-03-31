<?php
    $numWords = $_POST['numWords'];

    echo '<h1>Select your words:</h1>';
    echo "<form action='search.php' method='POST'>";
    
    for($i = 0; $i < $numWords; $i++) {
        echo "Word $i:<input name='words[]' type='text'/><br>";
    }
    echo "<input type='submit' value='Submit'/>";
    echo '</form>'; 
?>
