<?php
session_start();

// Connessione al database
$servername = "localhost";
$username = "root";
$password = ""; // Assumendo che non ci sia una password
$dbname = "db_prova";

// Verifica della connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
} else {
    echo "Connessione al database stabilita con successo.<br>"; // Debug
}

// Verifica la lingua preferita
if(isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif(isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    // Imposta una lingua predefinita se nessuna lingua è stata scelta
    $lang = "it"; // Italiano come predefinito
}

echo "Lingua preferita: " . $lang . "<br>"; // Debug

// Funzione per ottenere il testo tradotto
function translate($conn, $lang, $key) {
    $query = "SELECT $lang FROM translations WHERE `key_name` = '$key'";
    echo "Query di traduzione: " . $query . "<br>"; // Debug
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row[$lang];
    } else {
        return "Translation Not Found";
    }
}

// Imposta lingua preferita per la sessione corrente e per i futuri accessi tramite cookie
$_SESSION['lang'] = $lang;
setcookie('lang', $lang, time() + (86400 * 30), "/"); // 30 giorni

?>

<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate($conn, $lang, 'page_title') ?></title>
</head>
<body>

<h1><?php echo translate($conn, $lang, 'welcome_message') ?></h1>

<?php
// Ottenere le notizie dal database e mostrare i titoli e i testi delle notizie nella lingua corretta
$query = "SELECT title_$lang, text_$lang FROM news";
echo "Query notizie: " . $query . "<br>"; // Debug
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<h2>" . $row['title_' . $lang] . "</h2>";
        echo "<p>" . $row['text_' . $lang] . "</p>";
    }
} else {
    echo "Nessuna notizia disponibile";
}
?>

</body>
</html>

<?php
$conn->close();
?>
