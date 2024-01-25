<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Rozpocznij sesję
session_start();

function WyswietlPaczkeZagraniczna()
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
              prz.id AS przesylka_id, prz.opis, prz.koszt, prz.waga, zam.czyZagraniczna,
              doc.pdf_file
              FROM zamowienie AS zam
              JOIN przesylka AS prz ON zam.id = prz.zamowienieID
              LEFT JOIN dokument AS doc ON zam.id = doc.zamowienieID
              WHERE prz.KurierID = $kurier_id
              AND zam.status IN ('Na sortowni', 'Realizowanie dostarczenia')
              AND zam.czyZagraniczna = 1";

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
                    <th>Koszt</th>
                    <th>Waga</th>
                    <th>Czy Zagraniczna</th>
                    <th>Akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['zamowienie_id'] . '</td>
                    <td>' . $row['dataNadania'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . $row['opis'] . '</td>
                    <td>' . $row['koszt'] . '</td>
                    <td>' . $row['waga'] . '</td>
                    <td>' . ($row['czyZagraniczna'] ? 'Tak' : 'Nie') . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="zamowienie_id" value="' . $row['zamowienie_id'] . '">
                            <input type="submit" name="realizuj_zam" value="Realizuj">
                            <input type="submit" name="dostarcz_paczke" value="Przekaż">
                            <input type="hidden" name="pdf_file" value="' . $row['pdf_file'] . '">
                            <input type="submit" name="pobierz_pdf" value="Pobierz PDF">
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Błąd zapytania: ' . mysqli_error($link);
    }
}

// Funkcja do pobierania pliku PDF
function PobierzPDF($pdf_file)
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($pdf_file) . '"');
    readfile($pdf_file);
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
    $query = "UPDATE zamowienie SET status='Za granicą' WHERE id=$id";
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

    if (isset($_POST['pobierz_pdf'])) {
        $pdf_file = $_POST["pdf_file"];
        PobierzPDF($pdf_file);
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
WyswietlPaczkeZagraniczna();
?>
