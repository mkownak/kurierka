<?php
	echo '<h1>Witam w panelu Kuriera</h1>';
	echo '<table border="1">
                <tr>
                    <th>Funkcje</th>
                </tr>';
	echo '<tr>
			<td><a href="dostarczKlient.php"><button type="button">Dostarcz paczkę klientowi</button></a></td>
	</tr>';
	
	echo '<tr>
			<td><a href="odbierzKlient.php"><button type="button">Odbierz paczkę od klienta</button></a></td>
	</tr>';
	
	echo '<tr>
			<td><a href="DostarczZagraniczna.php"><button type="button">Przekaż zagranicznej firmie kurierskiej</button></a></td>
	</tr>';
	
		echo '<tr>
			<td><a href="odbierzZagraniczna.php"><button type="button">Odbierz od zagranicznej firmy kurierskiej</button></a></td>
	</tr>';
	
		echo '<tr>
			<td><a href="pobierzTrase.php"><button type="button">Pobierz trase</button></a></td>
	</tr>';
	echo '</table>';
?>	