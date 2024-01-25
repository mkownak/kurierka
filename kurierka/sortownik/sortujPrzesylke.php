<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

function PokazPaczki()
{
    global $link;
    $query = "SELECT prz.id AS paczka_id, zam.id AS zamowienie_id, k_nadawca.imie AS nadawca_imie, k_nadawca.nazwisko AS nadawca_nazwisko, k_odbiorca.imie AS odbiorca_imie, k_odbiorca.nazwisko AS odbiorca_nazwisko, prz.waga, prz.koszt
              FROM przesylka AS prz
              JOIN zamowienie AS zam ON prz.zamowienieID = zam.id
              JOIN klient AS k_nadawca ON zam.nadawcaID = k_nadawca.id
              JOIN klient AS k_odbiorca ON zam.odbiorcaID = k_odbiorca.id
              WHERE zam.status = 'Na sortowni'";

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table border="1">
                <tr>
                    <th>ID Paczki</th>
                    <th>ID Zamówienia</th>
                    <th>Nadawca</th>
                    <th>Odbiorca</th>
                    <th>Waga</th>
                    <th>Koszt</th>
                    <th>Akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['paczka_id'] . '</td>
                    <td>' . $row['zamowienie_id'] . '</td>
                    <td>' . $row['nadawca_imie'] . ' ' . $row['nadawca_nazwisko'] . '</td>
                    <td>' . $row['odbiorca_imie'] . ' ' . $row['odbiorca_nazwisko'] . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="paczka_id" value="' . $row['paczka_id'] . '">
                            <input type="text" name="nowa_waga" value="' . $row['waga'] . '">
                            <input type="submit" name="aktualizuj_wage" value="Aktualizuj wagę">
                        </form>
                    </td>
                    <td>' . $row['koszt'] . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="paczka_id" value="' . $row['paczka_id'] . '">
                            <input type="submit" name="kalkuluj_koszt" value="Kalkuluj koszt">
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Brak paczek do wyświetlenia.';
    }
}

// Funkcja do aktualizacji wagi
function aktualizujWage($paczka_id, $nowa_waga)
{
    global $link;
    $query = "UPDATE przesylka SET waga='$nowa_waga' WHERE id='$paczka_id'";
    mysqli_query($link, $query);
}

// Funkcja do kalkulacji kosztu
function kalkulujKoszt($paczka_id)
{
    global $link;

    // Pobierz dane paczki
    $queryPaczka = "SELECT waga, zam.CzyZagraniczna
                    FROM przesylka AS prz
                    JOIN zamowienie AS zam ON prz.zamowienieID = zam.id
                    WHERE prz.id = '$paczka_id'";
    $resultPaczka = mysqli_query($link, $queryPaczka);

    if ($rowPaczka = mysqli_fetch_assoc($resultPaczka)) {
        $waga = $rowPaczka['waga'];
        $czyZagraniczna = $rowPaczka['CzyZagraniczna'];

        // stawki kurierskie
        $stawkiKrajowe = array(
            'do5kg' => 10,
            'powyzej5kg' => 15,
        );

        $stawkiZagraniczne = array(
            'do5kg' => 20,
            'powyzej5kg' => 30,
        );

        // Wybierz odpowiednie stawki w zależności od wagi i obszaru dostawy
        $stawki = $czyZagraniczna ? $stawkiZagraniczne : $stawkiKrajowe;

        // Kalkulacja kosztu na podstawie wagi
        $koszt = $waga <= 5 ? $stawki['do5kg'] : $stawki['powyzej5kg'];

        // Zaktualizuj wartość kosztu w bazie danych
        $queryUpdateKoszt = "UPDATE przesylka SET koszt='$koszt' WHERE id='$paczka_id'";
        mysqli_query($link, $queryUpdateKoszt);

        echo 'Koszt paczki został obliczony i zaktualizowany.';
    } else {
        echo 'Nie udało się pobrać danych paczki.';
    }
}

// Obsługa formularza
if (isset($_POST['aktualizuj_wage'])) {
    $paczka_id = $_POST['paczka_id'];
    $nowa_waga = $_POST['nowa_waga'];

    aktualizujWage($paczka_id, $nowa_waga);
}

if (isset($_POST['kalkuluj_koszt'])) {
    $paczka_id = $_POST['paczka_id'];

    kalkulujKoszt($paczka_id);
}
echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
PokazPaczki();
?>
