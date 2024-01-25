<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Odczytaj informacje</title>
</head>

<body class="readbody">

    <h1>Odczytaj informacje o kliencie i przesyłkach</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="readform">
        <label for="email">Adres e-mail:</label>
        <input type="email" id="email" name="email" required>

        <br><br>

        <input type="submit" name="submitOdbior" value="Pokaż przesyłki nadanie do Ciebie">

        <br><br>

        <input type="submit" name="submitNadanie" value="Pokaż przesyłki nadanie przez Ciebie">
    </form>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kurierex";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["submitNadanie"])) {
            $email = $_POST["email"];

                // Pobieranie informacji o kliencie i przesyłkach na podstawie adresu e-mail
                $sqlSelect = "SELECT klient.*, zamowienie.* FROM klient
                            LEFT JOIN zamowienie ON klient.id = zamowienie.nadawcaID
                            WHERE klient.email = '$email'";
                $result = $conn->query($sqlSelect);

                $row1=$result->fetch_assoc();

                if ($result->num_rows > 0) {
                    if(!empty($row1['id'])) {
                    echo "<h2>Informacje o przesyłkach dla klienta: $email</h2>";
                    echo "<table border='1' class='readtable'>
                            <tr>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Email</th>
                                <th>Ulica</th>
                                <th>Nr Domu</th>
                                <th>Nr Mieszkania</th>
                                <th>Miasto</th>
                                <th>Kod Pocztowy</th>
                                <th>Nr Telefonu</th>
                                <th>Zamówienie ID</th>
                                <th>Data Nadania</th>
                                <th>Status</th>
                            </tr>";
                            
                            echo "<tr>
                            <td>{$row1['imie']}</td>
                            <td>{$row1['nazwisko']}</td>
                            <td>{$row1['email']}</td>
                            <td>{$row1['ulica']}</td>
                            <td>{$row1['nrDomu']}</td>
                            <td>{$row1['nrMieszkania']}</td>
                            <td>{$row1['miasto']}</td>
                            <td>{$row1['kodPocztowy']}</td>
                            <td>{$row1['nrTelefonu']}</td>
                            <td>{$row1['id']}</td>
                            <td>{$row1['dataNadania']}</td>
                            <td>{$row1['status']}</td>
                        </tr>"; 
                    while ($row2 = $result->fetch_assoc()) {
                        
                            echo "<tr>
                                <td>{$row2['imie']}</td>
                                <td>{$row2['nazwisko']}</td>
                                <td>{$row2['email']}</td>
                                <td>{$row2['ulica']}</td>
                                <td>{$row2['nrDomu']}</td>
                                <td>{$row2['nrMieszkania']}</td>
                                <td>{$row2['miasto']}</td>
                                <td>{$row2['kodPocztowy']}</td>
                                <td>{$row2['nrTelefonu']}</td>
                                <td>{$row2['id']}</td>
                                <td>{$row2['dataNadania']}</td>
                                <td>{$row2['status']}</td>
                            </tr>";
                        }} else {
                            echo "<p>Brak danych dla podanego adresu e-mail.</p>";
                        }
                    }

                    echo "</table>";
                }
            }
        

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["submitOdbior"])) {
            $email = $_POST["email"];

            // Pobieranie informacji o kliencie i przesyłkach na podstawie adresu e-mail
            $sqlSelect = "SELECT klient.*, zamowienie.* FROM klient
                            LEFT JOIN zamowienie ON klient.id = zamowienie.odbiorcaID
                            WHERE klient.email = '$email'";
            $result = $conn->query($sqlSelect);

            $row3=$result->fetch_assoc();

            if ($result->num_rows > 0) {
                if(!empty($row3['id'])) {
                echo "<h2>Informacje o przesyłkach dla klienta: $email</h2>";
                echo "<table border='1' class='readtable'>
                            <tr>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Email</th>
                                <th>Ulica</th>
                                <th>Nr Domu</th>
                                <th>Nr Mieszkania</th>
                                <th>Miasto</th>
                                <th>Kod Pocztowy</th>
                                <th>Nr Telefonu</th>
                                <th>Zamówienie ID</th>
                                <th>Data Nadania</th>
                                <th>Status</th>
                            </tr>";
                        echo "<tr>
                                <td>{$row3['imie']}</td>
                                <td>{$row3['nazwisko']}</td>
                                <td>{$row3['email']}</td>
                                <td>{$row3['ulica']}</td>
                                <td>{$row3['nrDomu']}</td>
                                <td>{$row3['nrMieszkania']}</td>
                                <td>{$row3['miasto']}</td>
                                <td>{$row3['kodPocztowy']}</td>
                                <td>{$row3['nrTelefonu']}</td>
                                <td>{$row3['id']}</td>
                                <td>{$row3['dataNadania']}</td>
                                <td>{$row3['status']}</td>
                        </tr>"; 

                while ($row4 = $result->fetch_assoc()) {
                    
                        echo "<tr>
                                <td>{$row4['imie']}</td>
                                <td>{$row4['nazwisko']}</td>
                                <td>{$row4['email']}</td>
                                <td>{$row4['ulica']}</td>
                                <td>{$row4['nrDomu']}</td>
                                <td>{$row4['nrMieszkania']}</td>
                                <td>{$row4['miasto']}</td>
                                <td>{$row4['kodPocztowy']}</td>
                                <td>{$row4['nrTelefonu']}</td>
                                <td>{$row4['id']}</td>
                                <td>{$row4['dataNadania']}</td>
                                <td>{$row4['status']}</td>
                            </tr>";
                    }} else {
                        echo "<p>Brak danych dla podanego adresu e-mail.</p>";
                    }
                }

                echo "</table>";
            }
        }
    
    $conn->close();
    ?>

    <br><br><br>

    <a href="index.php">
        <button type="button">Powrót do strony głównej</button>
    </a>

</body>

</html>
