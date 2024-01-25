<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';
	
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
function pokazZagraniczne()
{
    global $link;
    $query = "SELECT zam.id AS zamowienie_id, nadawca.imie AS nadawca_imie, nadawca.nazwisko AS nadawca_nazwisko, odbiorca.imie AS odbiorca_imie, odbiorca.nazwisko AS odbiorca_nazwisko, zam.dataNadania, zam.status, prz.opis, IFNULL(doc.pdf_file, 'Brak') AS pdf_file, doc.id AS dokument_id
              FROM zamowienie AS zam
              JOIN przesylka AS prz ON zam.id = prz.zamowienieID
              LEFT JOIN dokument AS doc ON zam.id = doc.zamowienieID
              LEFT JOIN klient AS nadawca ON zam.nadawcaID = nadawca.id
              LEFT JOIN klient AS odbiorca ON zam.odbiorcaID = odbiorca.id
              WHERE zam.CzyZagraniczna='1'";
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) 
    {
        echo '<table border="1">
                <tr>
                    <th>id</th>
                    <th>nadawca</th>
                    <th>odbiorca</th>
                    <th>data Nadania</th>
                    <th>status</th>
                    <th>opis</th>
                    <th>akcje</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) 
        {
            echo '<tr>
                    <td>' . $row['zamowienie_id'] . '</td>
                    <td>' . $row['nadawca_imie'] . ' ' . $row['nadawca_nazwisko'] . '</td>
                    <td>' . $row['odbiorca_imie'] . ' ' . $row['odbiorca_nazwisko'] . '</td>
                    <td>' . $row['dataNadania'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . $row['opis'] . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="doc_id" value="' . $row['zamowienie_id'] . '">
                            <input type="submit" name="dodaj_pdf" value="Dodaj dokument" ' . ($row['pdf_file'] != 'Brak' ? 'disabled' : '') . '>
                            <input type="submit" name="usun_pdf" value="Usun dokument" ' . ($row['pdf_file'] == 'Brak' ? 'disabled' : '') . '>
                            ' . ($row['pdf_file'] != 'Brak' ? '<input type="submit" name="pobierz_pdf" value="Pobierz dokument">' : '') . '
                   
                        </form>
                    </td>
                </tr>';
        }

        echo '</table>';
    } 
    else 
    {
        echo 'Brak zagranicznych zamówień do wyświetlenia.';
    }
}

function UsunPdf($id)
{
	global $link;
	$id = mysqli_real_escape_string($link, $id);
	$query = "DELETE FROM dokument WHERE zamowienieID='$id'";
	
	mysqli_query($link, $query);
}

if(isset($_POST['zapisz_pdf']))
{	
	global $link;
	$targetDir = "uploads/";
	$targetFile = $targetDir . basename($_FILES["pdfFile"]["name"]);
	$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
	
	if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFile))
	{
		$id = isset($_POST['doc_id']) ? $_POST['doc_id'] : '';
		$folder_path = $targetFile;
		
		$query = "INSERT INTO dokument (zamowienieID, pdf_file) VALUES ('$id', '$folder_path')";
		mysqli_query($link, $query);
	}
	else
	{
		echo 'błąd wrzucania pliku';
	}
}

if (isset($_POST['pobierz_pdf'])) 
{
	global $link;
	$id = isset($_POST['doc_id']) ? $_POST['doc_id'] : '';
	$query = "SELECT pdf_file from dokument WHERE zamowienieID='$id'";
	$result = mysqli_query($link, $query);
	$row = mysqli_fetch_assoc($result);
	$path = $row['pdf_file'];
	
	header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="nazwa_pliku.pdf"');
    readfile($path);
}

if (isset($_POST['usun_pdf']))
{
	$id = isset($_POST['doc_id']) ? $_POST['doc_id'] : '';
	UsunPdf($id);
	echo 'usunieto dokument';
}

echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';	
pokazZagraniczne();
	
if (isset($_POST['dodaj_pdf'])) 
{
	$id = isset($_POST['doc_id']) ? $_POST['doc_id'] : '';
	echo '
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="doc_id" value="' . $id . '">
		<label for="pdfFile">Wybierz plik PDF:</label>
		<input type="file" name="pdfFile" id="pdfFile" accept=".pdf">
		<input type="submit" name="zapisz_pdf" value="Dodaj">
	</form>	';
}


?>