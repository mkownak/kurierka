<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

function WyswietlPaczke($id)
{
    global $link;

    // Validate if the package ID exists in the database
    $checkPackageQuery = "SELECT COUNT(*) as count FROM zamowienie WHERE id = $id";
    $checkPackageResult = mysqli_query($link, $checkPackageQuery);
    $count = mysqli_fetch_assoc($checkPackageResult)['count'];

    if ($count > 0) {
        $query = "SELECT zam.id AS zamowienie_id, zam.nadawcaID, zam.odbiorcaID, zam.dataNadania, zam.status, 
              prz.id AS przesylka_id, prz.opis, prz.koszt, prz.waga, 
              nad.imie AS nadawca_imie, nad.nazwisko AS nadawca_nazwisko, 
              odb.imie AS odbiorca_imie, odb.nazwisko AS odbiorca_nazwisko
              FROM zamowienie AS zam
              JOIN przesylka AS prz ON zam.id = prz.zamowienieID
              JOIN klient AS nad ON zam.nadawcaID = nad.id
              JOIN klient AS odb ON zam.odbiorcaID = odb.id
              WHERE zam.id = $id AND zam.status='Oczekujące na odbiór przez kuriera'";

        $result = mysqli_query($link, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            echo '<table border="1">
                    <tr>
                        <th>Zamówienie ID</th>
                        <th>Data Nadania</th>
                        <th>Status</th>
                        <th>Opis</th>
                        <th>Nadawca</th>
                        <th>Odbiorca</th>
                        <th>Akcje</th>
                    </tr>';

            if ($row) {
                echo '<tr>
                        <td>' . $row['zamowienie_id'] . '</td>
                        <td>' . $row['dataNadania'] . '</td>
                        <td>' . $row['status'] . '</td>
                        <td>' . $row['opis'] . '</td>
                        <td>' . $row['nadawca_imie'] . ' ' . $row['nadawca_nazwisko'] . '</td>
                        <td>' . $row['odbiorca_imie'] . ' ' . $row['odbiorca_nazwisko'] . '</td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="zamowienie_id" value="' . $row['zamowienie_id'] . '">
                                <input type="submit" name="odbierz_paczke" value="Odbierz">
                            </form>
                        </td>
                    </tr>';
            }

            echo '</table>';
        } else {
            echo 'Błąd zapytania: ' . mysqli_error($link);
        }
    } else {
        echo 'Paczka o podanym ID nie istnieje.';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["odbierz_paczke"])) {
        $zamowienie_id = $_POST["zamowienie_id"];
        OdbierzPaczke($zamowienie_id);
    }

    if (isset($_POST["szukaj_paczki"])) {
        $paczka_id = $_POST["paczka_id"];
        WyswietlPaczke($paczka_id);
    }
}

function OdbierzPaczke($id)
{
    global $link;
    $query = "UPDATE zamowienie SET status='Odebrana' WHERE id ='$id'";
    mysqli_query($link, $query);
    echo "Paczka została odebrana!";
}

echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
echo '<form method="post">
        <label for="paczka_id">Szukaj paczki po ID:</label>
        <input type="text" id="paczka_id" name="paczka_id" required>
        <input type="submit" name="szukaj_paczki" value="Szukaj">
    </form>';
?>

