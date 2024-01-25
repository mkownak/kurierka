<?php
	echo '<style>';
		//include 'style.css';
	echo '</style>';
	
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';
	$dbname = 'kurierex';
	
	$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
	function PokazZamowienia()
	{
		global $link;
		$query = "SELECT zam.id, nad.id AS nadawcaID, nad.imie AS nadawcaImie, nad.nazwisko AS nadawcaNazwisko, 
                odb.id AS odbiorcaID, odb.imie AS odbiorcaImie, odb.nazwisko AS odbiorcaNazwisko,
                zam.dataNadania, zam.status, zam.CzyZagraniczna 
                FROM zamowienie AS zam
                JOIN klient AS nad ON zam.nadawcaID = nad.id
                JOIN klient AS odb ON zam.odbiorcaID = odb.id";
		$result = mysqli_query($link, $query);

		$statusy = array(
        'Oczekujące na zatwierdzenie',
        'Oczekujące na odbiór przez kuriera',
        'Odebrana',
        'Na sortowni',
        'Realizowanie dostarczenia',
        'Dostarczona',
        'Anulowana',
		'Za granicą',
		'Oczekujące na odbiór od zagranicznej firmy'
		);

		if (mysqli_num_rows($result) > 0) {
			echo '<table border="1">
				<tr>
					<th>id</th>
					<th>nadawca</th>
					<th>odbiorca</th>
					<th>data Nadania</th>
					<th>status</th>
					<th>zagraniczna</th>
					<th>akcje</th>
				</tr>';

			while ($row = mysqli_fetch_assoc($result)) {
				echo '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nadawcaImie'] . ' ' . $row['nadawcaNazwisko'] . '</td>
                    <td>' . $row['odbiorcaImie'] . ' ' . $row['odbiorcaNazwisko'] . '</td>
                    <td>' . $row['dataNadania'] . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="zamowienie_id" value="' . $row['id'] . '">
                            <select name="nowy_status">';
				foreach ($statusy as $status) {
					$selected = ($status == $row['status']) ? 'selected' : '';
					echo "<option value=\"$status\" $selected>$status</option>";
				}
				echo '</select>
					</td>
					<td>' . ($row['CzyZagraniczna'] ? 'Tak' : 'Nie') . '</td>
						<td>
                            <input type="submit" name="EdytujZamowienie" value="Zapisz status">
                            <input type="submit" name="UsunZamowienie" value="Usuń">
                        </form>
                    </td>
                </tr>';
        }
		echo '</table>';
    } 	else {
			echo 'Brak zamowień do wyświetlenia.';
		}
	}
	
	function ZapiszEdycje($id, $nowy_status)
	{
		global $link;

		$id = mysqli_real_escape_string($link, $id);
		$nowy_status = mysqli_real_escape_string($link, $nowy_status);

		$query = "UPDATE zamowienie SET status = '$nowy_status' WHERE id = '$id'";
		mysqli_query($link, $query);
	}
	
	function UsunZamowienie($id)
	{
		global $link;

		$id = mysqli_real_escape_string($link, $id);
		
		$query = "DELETE FROM zamowienie WHERE id = '$id'";
		mysqli_query($link, $query);
	}
	
	
	if (isset($_POST['UsunZamowienie'])) 
	{
		$zamowienie_id = isset($_POST['zamowienie_id']) ? $_POST['zamowienie_id'] : '';
		UsunZamowienie($zamowienie_id);
    }
	
	if (isset($_POST['EdytujZamowienie'])) 
	{
		$zamowienie_id = isset($_POST['zamowienie_id']) ? $_POST['zamowienie_id'] : '';
		$nowy_status = isset($_POST['nowy_status']) ? $_POST['nowy_status'] : '';
		ZapiszEdycje($zamowienie_id, $nowy_status);
    }
	echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
	PokazZamowienia();

?>