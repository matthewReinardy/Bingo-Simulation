<?php
//Vikings themed, SKOL!!!!! 31-29

session_start();

/*Checks to see if any numbers are called, if they are I am going to
store it in the same variable. */
$_SESSION['calledNumbers'] = $_SESSION['calledNumbers'] ?? [];

/* I originally had all of my bingo card logic outside of a function, but I quickly
realized when I was calling numbers that the entire board was generating again which
was not the intended functionality. I put it in a function so that it is only going to 
go off twice: when the page first loads for the user and when they want a new game */
if (!isset($_SESSION['bingoCard'])) {
    generateBingoCard();
}


//Generate the table
function generateBingoCard()
{
    //Values for each column
    $bColumnValues = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
    $iColumnValues = [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
    $nColumnValues = [31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45];
    $gColumnValues = [46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60];
    $oColumnValues = [61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75];

    $bRandomValues = [];
    $iRandomValues = [];
    $nRandomValues = [];
    $gRandomValues = [];
    $oRandomValues = [];

    //Generate 5 random numbers for each column
    for ($i = 0; $i < 5; $i++) {
        $bRandomValues[] = mt_rand($bColumnValues[0], $bColumnValues[14]);
        $iRandomValues[] = mt_rand($iColumnValues[0], $iColumnValues[14]);
        $nRandomValues[] = mt_rand($nColumnValues[0], $nColumnValues[14]);
        $gRandomValues[] = mt_rand($gColumnValues[0], $gColumnValues[14]);
        $oRandomValues[] = mt_rand($oColumnValues[0], $oColumnValues[14]);
    }

    // Store the card values in the session, using associative arrays with the letters as keys
    $_SESSION['bingoCard'] = [
        'B' => $bRandomValues,
        'I' => $iRandomValues,
        'N' => $nRandomValues,
        'G' => $gRandomValues,
        'O' => $oRandomValues
    ];
}

//Set off when user wants a new number, not called when the page first loads
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Generating the random number, allowing duplicates as you said was allowed
    $currentCall = mt_rand(1, 75);

    //Storing the call in the session
    $_SESSION['calledNumbers'][] = $currentCall;
}

// Check for a new game request using GET
if (isset($_GET['reset'])) {
    //Destroy the session to clear all of the information
    session_destroy();

    //Using the header feature that we learned about on page 227 to redirect
    header("Location: index.php");
    exit;
}

//The current called number and bingo card that are in the session
$calledNumbers = $_SESSION['calledNumbers'];
$bingoCard = $_SESSION['bingoCard'];

function countAllCalledNumbers($calledNumbers, $rangeStart, $rangeEnd)
{
    $count = 0;
    foreach ($calledNumbers as $number) {
        if ($number >= $rangeStart && $number <= $rangeEnd) {
            $count++;
        }
    }
    return $count;
}

// Total numbers called within each range, using function since we will be reusing code
$bCount = countAllCalledNumbers($calledNumbers, 1, 15);
$iCount = countAllCalledNumbers($calledNumbers, 16, 30);
$nCount = countAllCalledNumbers($calledNumbers, 31, 45);
$gCount = countAllCalledNumbers($calledNumbers, 46, 60);
$oCount = countAllCalledNumbers($calledNumbers, 61, 75);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assignment 6</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!--Display the "The number called is: " and then will echo the number from php-->
    <?php if (!empty($calledNumbers)) { ?>
        <h1>The Number Called Is: <?= $calledNumbers[count($calledNumbers) - 1] ?></h1>
    <?php } else { ?>
        <h1>The Number Called Is: None</h1>
    <?php } ?>

    <!--Table that displays the bingo card, im going to create the headers in html since it will
    be the same every time, and then the numbers I will generate dynamically-->
    <table>
        <tr>
            <th>B</th>
            <th>I</th>
            <th>N</th>
            <th>G</th>
            <th>O</th>
        </tr>
        <!--Filling the table values-->
        <?php for ($i = 0; $i < 5; $i++) { ?>
            <tr>
                <!-- Fill the table with values, I first set the class to called or null so I can highlight
                the squares that are called. I used the associative array with the 'in_array' function to check
                if the number has been called in the session. After that, we echo out the number from the bingo card -->
                <td class="<?= in_array($bingoCard['B'][$i], $calledNumbers) ? 'called' : '' ?>">
                    <?= $bingoCard['B'][$i] ?>
                </td>
                <td class="<?= in_array($bingoCard['I'][$i], $calledNumbers) ? 'called' : '' ?>">
                    <?= $bingoCard['I'][$i] ?>
                </td>
                <td class="<?= in_array($bingoCard['N'][$i], $calledNumbers) ? 'called' : '' ?>">
                    <?= $bingoCard['N'][$i] ?>
                </td>
                <td class="<?= in_array($bingoCard['G'][$i], $calledNumbers) ? 'called' : '' ?>">
                    <?= $bingoCard['G'][$i] ?>
                </td>
                <td class="<?= in_array($bingoCard['O'][$i], $calledNumbers) ? 'called' : '' ?>">
                    <?= $bingoCard['O'][$i] ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Displaying how many have been called so far -->
    <p>B's called thus far: <?= $bCount ?></p>
    <p>I's called thus far: <?= $iCount ?></p>
    <p>N's called thus far: <?= $nCount ?></p>
    <p>G's called thus far: <?= $gCount ?></p>
    <p>O's called thus far: <?= $oCount ?></p>


    <!--Had to use a button instead of a link because I realized
    the <a> tag does not trigger anything -->
    <form method="POST" action='index.php'>
        <button type="submit" name="callNumber">Call Another Bingo Number</button>
    </form>

    <br>
    <br>
    <!--Link to restart game, set reset to 'true' which is what we are checking in the 
    " (isset($_GET['reset'])) " statement -->
    <a href="index.php?reset=true">Restart Game</a>
</body>

</html>