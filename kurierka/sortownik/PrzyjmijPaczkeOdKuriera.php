<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

function PokazPaczki()
{
    global $link;

    // Zapytanie SQL
    $query = "SELECT prz.id AS przesylka_id, zam.id AS zamowienie_id, zam.status AS status_zamowienia, nad.imie AS nadawca_imie, nad.nazwisko AS nadawca_nazwisko, odb.imie AS odbiorca_imie, odb.nazwisko AS odbiorca_nazwisko, prac.imie AS kurier_imie, prac.nazwisko AS kurier_nazwisko
          FROM przesylka AS prz
          JOIN zamowienie AS zam ON prz.zamowienieID = zam.id
          LEFT JOIN pracownik AS prac ON prz.KurierID = prac.id
          JOIN klient AS nad ON zam.nadawcaID = nad.id
          JOIN klient AS odb ON zam.odbiorcaID = odb.id
          WHERE zam.status = 'Odebrana' AND prz.KurierID IS NOT NULL";

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table border="1">
                <tr>
                    <th>Przesyłka ID</th>
                    <th>Zamówienie ID</th>
                    <th>Status zamówienia</th>
                    <th>Nadawca</th>
                    <th>Odbiorca</th>
                    <th>Kurier</th>
                    <th>Akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['przesylka_id'] . '</td>
                    <td>' . $row['zamowienie_id'] . '</td>
                    <td>' . $row['status_zamowienia'] . '</td>
                    <td>' . $row['nadawca_imie'] . ' ' . $row['nadawca_nazwisko'] . '</td>
                    <td>' . $row['odbiorca_imie'] . ' ' . $row['odbiorca_nazwisko'] . '</td>
                    <td>' . $row['kurier_imie'] . ' ' . $row['kurier_nazwisko'] . '</td>
                    <td>
                        <form method="post">
							<input type="hidden" name="zamowienie_id" value="' . $row['zamowienie_id'] . '">
                            <input type="submit" name="przyjmij_paczke" value="Przyjmij">
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Brak paczek do wyświetlenia.';
    }
}

function przyjmijPaczke($zamowienie_id)
{
	global $link;
	$query_zam = "UPDATE zamowienie SET status='Na sortowni' WHERE id ='$zamowienie_id'";
	mysqli_query($link, $query_zam);
	
	$query_prze = "UPDATE przesylka SET kurierID=null WHERE zamowienieID ='$zamowienie_id'";
	mysqli_query($link, $query_prze);
}

if (isset($_POST['przyjmij_paczke'])) {
    $zamowienie_id = $_POST['zamowienie_id'];
    przyjmijPaczke($zamowienie_id);
}
echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
PokazPaczki();
?>



