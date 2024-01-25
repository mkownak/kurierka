<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Rozpocznij sesję
session_start();

function WyswietlPaczke()
{
    global $link;

    // Sprawdź, czy ustawiono zmienną sesyjną
    if (!isset($_SESSION['kurier_id'])) {
        echo 'Nie wybrano kuriera.';
        return;
    }

    $kurier_id = $_SESSION['kurier_id'];

    // Zapytanie SQL
    $query = "SELECT zam.id AS zamowienie_id, zam.dataNadania, zam.status, 
              prz.id AS przesylka_id, prz.opis, prz.koszt, prz.waga, 
              nad.imie AS nadawca_imie, nad.nazwisko AS nadawca_nazwisko, 
              odb.imie AS odbiorca_imie, odb.nazwisko AS odbiorca_nazwisko, 
              odb.ulica AS odbiorca_ulica, odb.nrDomu AS odbiorca_nr_domu, odb.nrMieszkania AS odbiorca_nr_mieszkania, 
              odb.miasto AS odbiorca_miasto
              FROM zamowienie AS zam
              JOIN przesylka AS prz ON zam.id = prz.zamowienieID
              JOIN klient AS nad ON zam.nadawcaID = nad.id
              JOIN klient AS odb ON zam.odbiorcaID = odb.id
              WHERE prz.KurierID = $kurier_id
              AND zam.status IN ('Na sortowni', 'Realizowanie dostarczenia')";

    $result = mysqli_query($link, $query);
	
    $queryKurierzy = "SELECT imie, nazwisko FROM pracownik WHERE id=$kurier_id";
    $resultKurierzy = mysqli_query($link, $queryKurierzy);
    echo "Wyswietlanie zamówień dla ";

    if ($resultKurierzy) {
        $rowKurier = mysqli_fetch_assoc($resultKurierzy);
        echo $rowKurier['imie'] . ' ' . $rowKurier['nazwisko'];
    } else {
        echo 'Błąd zapytania dotyczącego kuriera: ' . mysqli_error($link);
    }
	
    if ($result) {
        echo '<table border="1">
                <tr>
                    <th>Zamówienie ID</th>
                    <th>Data Nadania</th>
                    <th>Status</th>
                    <th>Opis</th>
                    <th>Nadawca</th>
                    <th>Odbiorca</th>
                    <th>Adres Odbiorcy</th>
                    <th>Akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['zamowienie_id'] . '</td>
                    <td>' . $row['dataNadania'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . $row['opis'] . '</td>
                    <td>' . $row['nadawca_imie'] . ' ' . $row['nadawca_nazwisko'] . '</td>
                    <td>' . $row['odbiorca_imie'] . ' ' . $row['odbiorca_nazwisko'] . '</td>
                    <td>' . $row['odbiorca_miasto'] . ', ul. ' . $row['odbiorca_ulica'] . ' ' . $row['odbiorca_nr_domu'] . '/' . $row['odbiorca_nr_mieszkania'] . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="zamowienie_id" value="' . $row['zamowienie_id'] . '">
                            <input type="submit" name="realizuj_zam" value="Realizuj">
                            <input type="submit" name="dostarcz_paczke" value="Dostarcz">
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Błąd zapytania: ' . mysqli_error($link);
    }
}

function RealizujDostawe($id)
{
    global $link;
    $query = "UPDATE zamowienie SET status='Realizowanie dostarczenia' WHERE id=$id";
    mysqli_query($link, $query);
}

function Dostarcz($id)
{
    global $link;
    $query = "UPDATE zamowienie SET status='Dostarczona' WHERE id=$id";
    mysqli_query($link, $query);
}

// Sprawdź, czy formularz został wysłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['realizuj_zam'])) {
        $zamowienie_id = $_POST["zamowienie_id"];
        RealizujDostawe($zamowienie_id);
    }

    if (isset($_POST['dostarcz_paczke'])) {
        $zamowienie_id = $_POST["zamowienie_id"];
        Dostarcz($zamowienie_id);
    }
	
	    if (isset($_POST['setKurier'])) {
			$kurier_id = $_POST["kurier_id"];
			$_SESSION['kurier_id'] = $kurier_id;
		}

    }


$queryKurierzy = "SELECT id, imie, nazwisko FROM pracownik WHERE typPracownika = 'Kurier'";
$resultKurierzy = mysqli_query($link, $queryKurierzy);

echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
echo '<form method="post">
        <label for="kurier_id">Wybierz kuriera:</label>
        <select name="kurier_id" id="kurier_id">';
while ($rowKurier = mysqli_fetch_assoc($resultKurierzy)) {
    echo '<option value="' . $rowKurier['id'] . '">' . $rowKurier['imie'] . ' ' . $rowKurier['nazwisko'] . '</option>';
}
echo '</select>
        <input type="submit" name="setKurier" value="Wybierz">
    </form>';

// Wyświetl paczki dla wybranego kuriera
WyswietlPaczke();
?>

