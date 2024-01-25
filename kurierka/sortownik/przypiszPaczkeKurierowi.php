<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';
	
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
function PokazPaczki()
{
    global $link;

    // Pobierz listę kurierów do dynamicznej opcji select
    $optionsKurierzy = '';
    $queryKurierzy = "SELECT id, imie, nazwisko FROM pracownik WHERE typPracownika = 'Kurier'";
    $resultKurierzy = mysqli_query($link, $queryKurierzy);

    while ($rowKurier = mysqli_fetch_assoc($resultKurierzy)) {
        $optionsKurierzy .= '<option value="' . $rowKurier['id'] . '">' . $rowKurier['imie'] . ' ' . $rowKurier['nazwisko'] . '</option>';
    }

    // Pobierz informacje o przesyłkach
	$query = "SELECT prz.id, prz.zamowienieID, prz.opis, prz.koszt, prz.waga, zam.status, prz.KurierID, prac.imie AS kurier_imie, prac.nazwisko AS kurier_nazwisko
          FROM przesylka AS prz
          JOIN zamowienie AS zam ON prz.zamowienieID = zam.id
          LEFT JOIN pracownik AS prac ON prz.KurierID = prac.id
          WHERE zam.status IN ('Oczekujące na odbiór przez kuriera', 'Na sortowni') 
          ORDER BY prz.KurierID IS NULL DESC";

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table border="1">
                <tr>
                    <th>paczka ID</th>
                    <th>zamowienie ID</th>
                    <th>opis</th>
                    <th>koszt</th>
                    <th>waga</th>
                    <th>Status</th>
                    <th>Kurier</th>
                    <th>Akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['zamowienieID'] . '</td>
                    <td>' . $row['opis'] . '</td>
                    <td>' . $row['koszt'] . '</td>
                    <td>' . $row['waga'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . ($row['KurierID'] ? $row['kurier_imie'] . ' ' . $row['kurier_nazwisko'] : 'Nieprzypisany') . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="przesylka_id" value="' . $row['id'] . '">
                            <select name="kurier_id">
                                <option value="">Wybierz kuriera</option>
                                ' . $optionsKurierzy . '
                            </select>
                            <input type="submit" name="przypisz_kuriera" value="Przypisz kuriera">
							<input type="submit" name="usun_przypisane" value="Usuń przypisanie" ' . ($row['KurierID'] ? '' : 'disabled') . '>
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Brak paczek do wyświetlenia.';
    }
}

function przypiszKuriera($id_przesylka, $id_kurier)
{
	global $link;
	$query = "UPDATE przesylka SET kurierID='$id_kurier' WHERE id='$id_przesylka'";
	mysqli_query($link, $query);
}

function usunPrzypisanie($id_przesylka)
{
    global $link;
    
    // Sprawdź, czy istnieje przypisany kurier
    $query = "SELECT KurierID FROM przesylka WHERE id='$id_przesylka'";
    $result = mysqli_query($link, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Jeżeli kurier jest przypisany, usuń przypisanie
        if ($row['KurierID'] !== null) {
            $queryUpdate = "UPDATE przesylka SET KurierID=NULL WHERE id='$id_przesylka'";
            mysqli_query($link, $queryUpdate);
        }
    }
}

echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
if(isset($_POST['przypisz_kuriera']))
{
    $przesylka_id = $_POST['przesylka_id'];
    $kurier_id = $_POST['kurier_id'];

	przypiszKuriera($przesylka_id, $kurier_id);
}

if(isset($_POST['usun_przypisane']))
{
    $przesylka_id = $_POST['przesylka_id'];

	usunPrzypisanie($przesylka_id);
}


PokazPaczki();
?>
