<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kurierex';
	
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	
function PokazZagraniczne()
{
    global $link;

    $query = "SELECT zam.id AS Numer_Zamowienia, 
                     nad.imie AS Nadawca_Imie, nad.nazwisko AS Nadawca_Nazwisko, 
                     nad.ulica AS Nadawca_Ulica, nad.nrDomu AS Nadawca_NrDomu, nad.nrMieszkania AS Nadawca_NrMieszkania, 
                     nad.miasto AS Nadawca_Miasto, nad.kodPocztowy AS Nadawca_KodPocztowy, nad.nrTelefonu AS Nadawca_Telefon, 
                     odb.imie AS Odbiorca_Imie, odb.nazwisko AS Odbiorca_Nazwisko, 
                     odb.ulica AS Odbiorca_Ulica, odb.nrDomu AS Odbiorca_NrDomu, odb.nrMieszkania AS Odbiorca_NrMieszkania, 
                     odb.miasto AS Odbiorca_Miasto, odb.kodPocztowy AS Odbiorca_KodPocztowy, odb.nrTelefonu AS Odbiorca_Telefon, 
                     zam.dataNadania AS Data_Nadania, 
                     prz.opis AS Opis_Paczki, 
                     prz.koszt AS Koszt_Paczki 
              FROM zamowienie AS zam
              JOIN klient AS nad ON zam.nadawcaID = nad.id
              JOIN klient AS odb ON zam.odbiorcaID = odb.id
              JOIN przesylka AS prz ON zam.id = prz.zamowienieID
              WHERE zam.CzyZagraniczna = '1'";

    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table border="1">
                <tr>
                    <th>Numer Zamówienia</th>
                    <th>Nadawca</th>
                    <th>Adres Nadawcy</th>
                    <th>Odbiorca</th>
                    <th>Adres Odbiorcy</th>
                    <th>Data Nadania</th>
                    <th>Opis Paczki</th>
                    <th>Koszt Paczki</th>
                </tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['Numer_Zamowienia'] . '</td>
                    <td>' . $row['Nadawca_Imie'] . ' ' . $row['Nadawca_Nazwisko'] . '</td>
                    <td>' . $row['Nadawca_Ulica'] . ' ' . $row['Nadawca_NrDomu'] . '/' . $row['Nadawca_NrMieszkania'] . ', ' . $row['Nadawca_Miasto'] . ', ' . $row['Nadawca_KodPocztowy'] . '</td>
                    <td>' . $row['Odbiorca_Imie'] . ' ' . $row['Odbiorca_Nazwisko'] . '</td>
                    <td>' . $row['Odbiorca_Ulica'] . ' ' . $row['Odbiorca_NrDomu'] . '/' . $row['Odbiorca_NrMieszkania'] . ', ' . $row['Odbiorca_Miasto'] . ', ' . $row['Odbiorca_KodPocztowy'] . '</td>
                    <td>' . $row['Data_Nadania'] . '</td>
                    <td>' . $row['Opis_Paczki'] . '</td>
                    <td>' . $row['Koszt_Paczki'] . '</td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'Brak zagranicznych przesyłek do wyświetlenia.';
    }
}
	echo '<a href="index.php"><button type="button">Powrót do panelu</button></a>';
	PokazZagraniczne();
	echo '	
	<h1>Kontakt</h1>
		<form action="mailto:164400@student.uwm.edu.pl" method="post" enctype="text/plain">	
			<label for="subject">Temat</label><br>
			<input type="text" id="subject" name="subject" size="50" value="Powiadomienie o Nadchodzącej Przesyłce"><br>
			
			<textarea id="msg" name="msg" rows="30" cols="100">
            Szanowni Państwo [Nazwa Firmy],

            Chcielibyśmy Państwa poinformować o nadchodzącej przesyłce, którą zamierzamy wysłać do Państwa firmy kurierskiej. Poniżej znajdują się szczegóły przesyłki:

            **Dane Przesyłki:**
            - Numer Zamówienia: [Numer Zamówienia]
            - Nadawca: [Imię i Nazwisko/Nazwa Firmy Nadawcy]
            - Adres Nadawcy: [Adres Nadawcy]
            - Odbiorca: [Imię i Nazwisko/Nazwa Firmy Odbiorcy]
            - Adres Odbiorcy: [Adres Odbiorcy]
            - Data Nadania: [Data Nadania]
            - Waga Paczki: [Waga Paczki]
            - Wartość Deklarowana: [Wartość Deklarowana]

            **Informacje Celne:**
            - Numer Faktury: [Numer Faktury]
            - Opis Zawartości: [Opis Zawartości]
            - Numer Śledzenia Celno-Skarbowego: [Numer Śledzenia Celno-Skarbowego]

            **Usługa Kurierska:**
            - Wybrana Usługa: [Nazwa Wybranej Usługi]
            - Metoda Dostawy: [Metoda Dostawy]

            Prosimy o potwierdzenie przyjęcia przesyłki oraz przesłanie wszelkich niezbędnych dokumentów celnych. W przypadku jakichkolwiek pytań prosimy o kontakt.

            Z poważaniem,

            [Twoje Imię i Nazwisko]
            [Twoja Pozycja w Firmie]
            [Nazwa Twojej Firmy]
            [Kontakt: Telefon lub E-mail]
        </textarea><br>
		<input type="submit" value="Wyślij">
		</form>';
?>