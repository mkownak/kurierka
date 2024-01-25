<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kurierex";

$conn = new mysqli($servername, $username, $password, $dbname);

function wyswietlKurierow($conn) {
    $sqlGetKuriers = "SELECT id, imie, nazwisko, email, nrTelefonu FROM pracownik WHERE typPracownika = 'Kurier'";
    $result = $conn->query($sqlGetKuriers);

    if ($result->num_rows > 0) {
        echo "<h2>Lista Kurierów:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Email</th>
                    <th>Nr Telefonu</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['imie']}</td>
                    <td>{$row['nazwisko']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['nrTelefonu']}</td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "Brak kurierów w bazie danych.";
    }
}

function wyswietlKlientow($conn, $przesylkaID) {
    // Pobierz informacje o klientach na podstawie przesyłki
    $sqlGetKlientow = "SELECT z.status, k.id, k.imie, k.nazwisko, k.email, k.ulica, k.nrDomu, k.nrMieszkania, k.miasto, k.kodPocztowy, k.nrTelefonu
                       FROM przesylka p
                       JOIN zamowienie z ON p.zamowienieID = z.id
                       JOIN klient k ON z.odbiorcaID = k.id
                       WHERE p.id = ?";

    // Utwórz prepared statement
    $stmt = $conn->prepare($sqlGetKlientow);

    // Zabezpiecz przesyłkaID przed SQL Injection
    $stmt->bind_param("i", $przesylkaID);

    // Wykonaj zapytanie
    $stmt->execute();

    // Pobierz wyniki zapytania
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Informacje na temat Adresu Paczki:</h2>";

        while ($row = $result->fetch_assoc()) {
            // Sprawdź status przesyłki
            if ($row['status'] == 'Odebrana') {
                echo "<p>Paczka już odebrana czeka w sortowni</p>";
                echo "<p>Ulica: Gajdy 4</p>";
                echo "<p>Miasto: Brodnica</p>";
                echo "<p>Kod Pocztowy: 87-300</p>";
            } elseif ($row['status'] == 'Za granicą') {
                echo "<p>Paczka do wysyłki za granice dla przewoźnika</p>";
                echo "<p>Ulica: Kasprzaka</p>";
                echo "<p>Miasto: Frankfurt nad Odrą</p>";
                echo "<p>Kod Pocztowy: 15-326</p>";
            } else {
                echo "<p>Ulica: {$row['ulica']} {$row['nrDomu']}/{$row['nrMieszkania']}</p>";
                echo "<p>Miasto: {$row['miasto']}</p>";
                echo "<p>Kod Pocztowy: {$row['kodPocztowy']}</p>";
                echo "<p>Numer Telefonu: {$row['nrTelefonu']}</p>";
            }
            echo "<hr>";
        }
    } 

    // Zwolnij zasoby
    $stmt->close();
}

function dodajTrase($conn, $kurierID, $dlugoscTrasy, $czasDostawy) {
    // Dodaj walidację danych wejściowych, aby uniknąć SQL Injection
    // ...

    // Tworzenie zapytania SQL
    $sqlDodajTrase = "INSERT INTO trasa (kurierID, dlugoscTrasy, czasDostawy) VALUES (?, ?, ?)";

    // Utwórz prepared statement
    $stmt = $conn->prepare($sqlDodajTrase);

    // Zabezpiecz dane przed SQL Injection
    $stmt->bind_param("ids", $kurierID, $dlugoscTrasy, $czasDostawy);

    // Wykonaj zapytanie
    if ($stmt->execute()) {
        echo "Trasa została dodana pomyślnie.";
    } else {
        echo "Błąd podczas dodawania trasy: " . $stmt->error;
    }

    // Zamknij prepared statement
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bodyy">

    <div class="left-panel">
        <h1>Panel Logistyka</br>Dodaj Trasę</h1>

        <div class="createformularz">
            <form action="dodajTrase.php" method="post" class="createform">
                <!-- Dodane pole kurierID -->
                <label for="kurierID">ID Kuriera:</label>
                <select id="kurierID" name="kurierID" required>
                    <?php
                    // Pobranie listy kurierów z bazy danych
                    $sqlGetKuriers = "SELECT id, imie, nazwisko FROM pracownik WHERE typPracownika = 'Kurier'";
                    $result = $conn->query($sqlGetKuriers);

                    // Wyświetlanie opcji dla każdego kuriera
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['imie']} {$row['nazwisko']}</option>";
                    }
                    ?>
                </select><br>

                <label for="dlugoscTrasy">Długość trasy:</label>
                <input type="number" id="dlugoscTrasy" name="dlugoscTrasy" required><br>

                <label for="czasDostawy">Czas dostawy:</label>
                <input type="time" id="czasDostawy" name="czasDostawy" required><br>

                <input type="submit" name="dodajTrase" value="Dodaj trasę" class="createguzik">
            </form>

            <?php
            // Wywołanie funkcji
            wyswietlKurierow($conn);

            // Obsługa dodawania trasy po zatwierdzeniu formularza
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dodajTrase"])) {
                $kurierID = $_POST["kurierID"];
                $dlugoscTrasy = $_POST["dlugoscTrasy"];
                $czasDostawy = $_POST["czasDostawy"];
                dodajTrase($conn, $kurierID, $dlugoscTrasy, $czasDostawy);
            }
            ?>
            
        </div>
        <a href="index.php">
            <button type="button" class='createguzik'>Powrót do strony głównej</button>
        </a>
    </div>

    <!-- Prawa strona do wyświetlania klientów -->
    <div class="right-panel">
        <div class="createformularz">
            <form action="" method="post">
                <label for="przesylkaID">Wybierz przesyłkę:</label>
                <select id="przesylkaID" name="przesylkaID" required>
                    <?php
                    // Pobranie listy przesyłek z bazy danych
                    $sqlGetPrzesylki = "SELECT id FROM przesylka";
                    $resultPrzesylki = $conn->query($sqlGetPrzesylki);

                    while ($rowPrzesylki = $resultPrzesylki->fetch_assoc()) {
                        $selected = (isset($_POST["przesylkaID"]) && $_POST["przesylkaID"] == $rowPrzesylki['id']) ? "selected" : "";
                        echo "<option value='{$rowPrzesylki['id']}' $selected>Przesyłka ID: {$rowPrzesylki['id']}</option>";
                    }
                    ?>
                </select>
                <input type="submit" value="Wyświetl Adres paczki" class="createguzik">
            </form>

            <?php
            // Wyświetlanie informacji o klientach po zatwierdzeniu formularza
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $selectedPrzesylkaID = isset($_POST["przesylkaID"]) ? $_POST["przesylkaID"] : null;
                wyswietlKlientow($conn, $selectedPrzesylkaID);
            }
            ?>
        </div>
    </div>

</body>

</html>

<?php
$conn->close();
?>
