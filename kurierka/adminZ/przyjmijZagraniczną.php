<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kurierex";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("Nie udało się połączyć z bazą: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["nadajPaczke"])) {
        // Pobranie danych nadawcy z formularza
        $imie = $_POST["imie"];
        $nazwisko = $_POST["nazwisko"];
        $email = $_POST["email"];
        $ulica = $_POST["ulica"];
        $nrDomu = $_POST["nrDomu"];
        $nrMieszkania = $_POST["nrMieszkania"];
        $miasto = $_POST["miasto"];
        $kodPocztowy = $_POST["kodPocztowy"];
        $nrTelefonu = $_POST["nrTelefonu"];

        // Pobranie danych odbiorcy z formularza
        $imie2 = $_POST["imie2"];
        $nazwisko2 = $_POST["nazwisko2"];
        $ulica2 = $_POST["ulica2"];
        $email2 = $_POST["email2"];
        $nrDomu2 = $_POST["nrDomu2"];
        $nrMieszkania2 = $_POST["nrMieszkania2"];
        $miasto2 = $_POST["miasto2"];
        $kodPocztowy2 = $_POST["kodPocztowy2"];
        $nrTelefonu2 = $_POST["nrTelefonu2"];

         // Sprawdzenie czy istnieje klient o podanym e-mailu
        $checkExistingClient = "SELECT id FROM klient WHERE email = '$email'";
        $result = $conn->query($checkExistingClient);

        if ($result->num_rows > 0) {
            // Klient o danym emailu już istnieje, pobierz jego ID
            $row = $result->fetch_assoc();
            $nadawcaId = $row["id"];
        } else {
            // Klient o danym emailu nie istnieje, wstawianie jego danych do tabeli 'klient'
            $sqlInsertClient = "INSERT INTO klient (imie, nazwisko, email, ulica, nrDomu, nrMieszkania, miasto, kodPocztowy, nrTelefonu) 
                                VALUES ('$imie', '$nazwisko', '$email', '$ulica', '$nrDomu', '$nrMieszkania', '$miasto', '$kodPocztowy', '$nrTelefonu')";
            $conn->query($sqlInsertClient);

            // Pobranie ID nowo dodanego klienta
            $nadawcaId = $conn->insert_id;
        }


        // Sprawdzenie czy istnieje klient o podanym e-mailu
        $checkExistingClient = "SELECT id FROM klient WHERE email = '$email2'";
        $result = $conn->query($checkExistingClient);

        if ($result->num_rows > 0) {
            // Klient o danym emailu już istnieje, pobierz jego ID
            $row = $result->fetch_assoc();
            $odbiorcaId = $row["id"];
        } else {
            // Klient o danym emailu nie istnieje, wstawianie jego danych do tabeli 'klient'
            $sqlInsertClient2 = "INSERT INTO klient (imie, nazwisko, email, ulica, nrDomu, nrMieszkania, miasto, kodPocztowy, nrTelefonu) 
                                VALUES ('$imie2', '$nazwisko2', '$email2', '$ulica2', '$nrDomu2', '$nrMieszkania2', '$miasto2', '$kodPocztowy2', '$nrTelefonu2')";
            $conn->query($sqlInsertClient2);

            // Pobranie ID nowo dodanego klienta
            $odbiorcaId = $conn->insert_id;
        }

        $dataNadania = date("Y-m-d H:i:s");
        $status = "Za granicą";
		$czyZagraniczna = 1;
        $opis = $_POST['opis'];

        // Wstawianie informacji do tabeli 'zamowienie'
        $sqlInsertParcelNadawca = "INSERT INTO zamowienie (nadawcaId, odbiorcaId, dataNadania, status, czyZagraniczna) 
                                   VALUES ('$nadawcaId','$odbiorcaId', '$dataNadania', '$status','$czyZagraniczna')";
        $conn->query($sqlInsertParcelNadawca);
        
        $zamowienieId = $conn->insert_id;

        $sqlInsrertParcelPrzesylka = "INSERT INTO `przesylka`(`zamowienieId`, `opis`) VALUES ('$zamowienieId', '$opis')";
        $conn->query($sqlInsrertParcelPrzesylka);

        echo "Paczka dodana pomyślnie!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href="style.css">
    <title>Rejestrowanie paczki</title>
</head>

<body class="createbody">
    
    <h1>Dodaj paczkę za granicy do bazy</h1>
    <div class="createformularz">
        <table>
            <tr>
                <td>
                    <form action="przyjmijZagraniczną.php" method="post" class="createform">
                        <!-- Formularz dla nadawcy -->
                        <h3>Dane nadawcy:</h3>
                        <label for="imie">Imię:</label>
                        <input type="text" id="imie" name="imie" required><br>

                        <label for="nazwisko">Nazwisko:</label>
                        <input type="text" id="nazwisko" name="nazwisko" required><br>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br>

                        <label for="ulica">Ulica:</label>
                        <input type="text" id="ulica" name="ulica" required><br>

                        <label for="nrDomu">Nr domu:</label>
                        <input type="number" id="nrDomu" name="nrDomu" required><br>

                        <label for="nrMieszkania">Nr mieszkania:</label>
                        <input type="number" id="nrMieszkania" name="nrMieszkania"><br>

                        <label for="miasto">Miasto:</label>
                        <input type="text" id="miasto" name="miasto" required><br>

                        <label for="kodPocztowy">Kod pocztowy:</label>
                        <input type="text" id="kodPocztowy" name="kodPocztowy" required><br>

                        <label for="nrTelefonu">Nr telefonu:</label>
                        <input type="number" id="nrTelefonu" name="nrTelefonu" required><br>

                        <label for="opis">Opis paczki:</label>
                        <input type="text" id="opis" name="opis"><br>

                        <input type="hidden" name="nadajPaczke">
                </td>
                <td>
        <!-- Formularz dla odbiorcy -->
                        <h3>Dane odbiorcy:</h3>
                        <label for="imie2">Imię:</label>
                        <input type="text" id="imie2" name="imie2" required><br>

                        <label for="nazwisko2">Nazwisko:</label>
                        <input type="text" id="nazwisko2" name="nazwisko2" required><br>

                        <label for="email2">Email:</label>
                        <input type="email" id="email2" name="email2" required><br>

                        <label for="ulica2">Ulica:</label>
                        <input type="text" id="ulica2" name="ulica2" required><br>

                        <label for="nrDomu2">Nr domu:</label>
                        <input type="number" id="nrDomu2" name="nrDomu2" required><br>

                        <label for="nrMieszkania2">Nr mieszkania:</label>
                        <input type="number" id="nrMieszkania2" name="nrMieszkania2"><br>

                        <label for="miasto2">Miasto:</label>
                        <input type="text" id="miasto2" name="miasto2" required><br>

                        <label for="kodPocztowy2">Kod pocztowy:</label>
                        <input type="text" id="kodPocztowy2" name="kodPocztowy2" required><br>

                        <label for="nrTelefonu2">Nr telefonu:</label>
                        <input type="number" id="nrTelefonu2" name="nrTelefonu2" required><br>

                        <br>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                        <input type="submit" name="nadajPaczke" value="Zarejestruj" class="createguzik">
                    </form>
                </td>
            </tr>
        </table>
    </div>
    
    <br><br>

    <a href="index.php">
        <button type="button">Powrót do panelu</button>
    </a>
</body>

</html>