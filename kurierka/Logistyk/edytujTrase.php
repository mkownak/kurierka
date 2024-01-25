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

function edytujTrase($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["edytujTraseSubmit"])) {
            $trasaIDdoEdycji = $_POST["trasaIDdoEdycji"];

            // Pobranie szczegółów wybranej trasy
            $sqlGetTrasaDetails = "SELECT * FROM trasa WHERE trasaID='$trasaIDdoEdycji'";
            $resultDetails = $conn->query($sqlGetTrasaDetails);

            if ($resultDetails->num_rows > 0) {
                echo "<h2>Edytuj trasę</h2>";
                echo "<div class='createformularz'>";
                echo "<form action='edytujTrase.php' method='post' class='createform'>";
                
                // Dodanie ukrytego pola z ID trasy
                echo "<input type='hidden' name='trasaID' value='{$trasaIDdoEdycji}'>";

                while ($rowDetails = $resultDetails->fetch_assoc()) {
                    echo "<label>ID Trasy: {$rowDetails['trasaID']}</label><br>";
                    echo "<label>Kurier ID:</label> <input type='text' name='kurierID' value='{$rowDetails['kurierID']}' readonly><br>";
                    echo "<label>Długość trasy:</label> <input type='text' name='dlugoscTrasy' value='{$rowDetails['dlugoscTrasy']}'><br>";
                    echo "<label>Czas dostawy:</label> <input type='text' name='czasDostawy' value='{$rowDetails['czasDostawy']}'><br>";
                }

                echo "<input type='submit' name='zapiszEdycje' value='Zapisz edycję' class='createguzik'>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "Nie znaleziono szczegółów trasy.";
            }
        }
    }
}

// Funkcja do aktualizacji trasy w bazie danych
function zapiszEdycje($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["zapiszEdycje"])) {
            $trasaID = $_POST["trasaID"];
            $nowaDlugoscTrasy = $_POST["dlugoscTrasy"];
            $nowyCzasDostawy = $_POST["czasDostawy"];

            // Aktualizacja informacji o trasie w tabeli 'Trasa'
            $sqlUpdateTrasa = "UPDATE trasa SET dlugoscTrasy='$nowaDlugoscTrasy', czasDostawy='$nowyCzasDostawy' WHERE trasaID='$trasaID'";
            if ($conn->query($sqlUpdateTrasa) === TRUE) {
                echo "Trasa zaktualizowana pomyślnie!";
            } else {
                echo "Błąd podczas aktualizacji trasy: " . $conn->error;
            }
        }
    }
}

function wyswietlKlientow($conn, $przesylkaID) {
    // Pobierz informacje o klientach na podstawie przesyłki
    $sqlGetKlientow = "SELECT z.status, k.id, k.imie, k.nazwisko, k.email, k.ulica, k.nrDomu, k.nrMieszkania, k.miasto, k.kodPocztowy, k.nrTelefonu
                       FROM przesylka p
                       JOIN zamowienie z ON p.zamowienieID = z.id
                       JOIN klient k ON z.odbiorcaID = k.id
                       WHERE p.id = " . ($przesylkaID ? $przesylkaID : 0);

    $result = $conn->query($sqlGetKlientow);

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
    } else {
        echo "Brak informacji o Adresie o ID $przesylkaID";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
       <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href="style.css">
</head>

<body class="bodyy">
    
    <div class="left-panel">
        <h1>Panel Logistyka - Edytuj trasę</h1>

        <h2>Wybierz trasę do edycji:</h2>
        <?php
        // Wyświetlanie listy tras do edycji
        $sqlGetTrasy = "SELECT trasaID, kurierID, dlugoscTrasy, czasDostawy FROM trasa";
        $result = $conn->query($sqlGetTrasy);

        if ($result->num_rows > 0) {
            echo "<form action='edytujTrase.php' method='post'>";
            echo "<label for='trasaIDdoEdycji'>Wybierz trasę do edycji:</label>";
            echo "<select id='trasaIDdoEdycji' name='trasaIDdoEdycji' required>";

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['trasaID']}'>Trasa {$row['trasaID']} - Kurier {$row['kurierID']}, Długość trasy: {$row['dlugoscTrasy']}, Czas dostawy: {$row['czasDostawy']}</option>";
            }

            echo "</select>";
            echo "<input type='submit' name='edytujTraseSubmit' value='Edytuj trasę' class='createguzik'>";
            echo "</form>";
        } else {
            echo "Brak tras do edycji.";
        }

        // Wywołanie funkcji edytującej trasę
        edytujTrase($conn);
        ?>

        <?php
        // Wywołanie funkcji zapisującej edycję
        zapiszEdycje($conn);
        ?>
            <?php
            // Wywołanie funkcji
            wyswietlKurierow($conn);
            ?>
        <br><br>

        <a href="index.php">
            <button type="button" class='createguzik'>Powrót do strony głównej</button>
        </a>
    </div>

    <!-- Prawa strona do wyświetlania klientów -->
    <div class="right-panel">
        <div class="display-info">
            <div class="createformularz">
                <form action="" method="post">
                    <label for="przesylkaID">Wybierz przesyłkę:</label>
                    <select id="przesylkaID" name="przesylkaID" required>
                        <?php
                        // Pobranie listy przesyłek z bazy danych
                        $sqlGetPrzesylki = "SELECT id FROM przesylka";
                        $resultPrzesylki = $conn->query($sqlGetPrzesylki);

                        while ($rowPrzesylki = $resultPrzesylki->fetch_assoc()) {
                            echo "<option value='{$rowPrzesylki['id']}'>Przesyłka ID: {$rowPrzesylki['id']}</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" value="Wyświetl Adres paczki" class="createguzik">
                </form>

                <?php
                // Wyświetlanie informacji o klientach po zatwierdzeniu formularza
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST["przesylkaID"])) {
                        $selectedPrzesylkaID = $_POST["przesylkaID"];
                        wyswietlKlientow($conn, $selectedPrzesylkaID);
                    } else {
                        echo "Nie wybrano przesyłki.";
                    }
                }
                ?>

            </div>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>