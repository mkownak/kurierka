<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kurierex";

$conn = new mysqli($servername, $username, $password, $dbname);

function usunTrase($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["usunTraseSubmit"])) {
            $trasaIDdoUsuniecia = $_POST["trasaIDdoUsuniecia"];

            // Pobranie szczegółów wybranej trasy
            $sqlGetTrasaDetails = "SELECT * FROM trasa WHERE trasaID='$trasaIDdoUsuniecia'";
            $resultDetails = $conn->query($sqlGetTrasaDetails);

            if ($resultDetails->num_rows > 0) {
                echo "<h2>Szczegóły trasy do usunięcia:</h2>";
                echo "<table border='1'>
                        <tr>
                            <th>TrasaID</th>
                            <th>KurierID</th>
                            <th>Długość trasy</th>
                            <th>Czas dostawy</th>
                        </tr>";

                while($rowDetails = $resultDetails->fetch_assoc()) {
                    echo "<tr>
                            <td>{$rowDetails['trasaID']}</td>
                            <td>{$rowDetails['kurierID']}</td>
                            <td>{$rowDetails['dlugoscTrasy']}</td>
                            <td>{$rowDetails['czasDostawy']}</td>
                        </tr>";
                }

                echo "</table>";

                // Usunięcie trasy z tabeli 'Trasa'
                $sqlDeleteTrasa = "DELETE FROM trasa WHERE trasaID='$trasaIDdoUsuniecia'";
                if ($conn->query($sqlDeleteTrasa) === TRUE) {
                    echo "Trasa usunięta pomyślnie!";
                } else {
                    echo "Błąd podczas usuwania trasy: " . $conn->error;
                }
            } else {
                echo "Nie znaleziono szczegółów trasy.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href="style.css">
</head>

<body>
    
    <h1>Panel Logistyka - Usuń trasę</h1>

    <h2>Wybierz trasę do usunięcia:</h2>
    <?php
    // Wyświetlanie listy tras do usunięcia
    $sqlGetTrasy = "SELECT trasaID, kurierID, dlugoscTrasy, czasDostawy FROM trasa";
    $result = $conn->query($sqlGetTrasy);

    if ($result->num_rows > 0) {
        echo "<form action='usunTrase.php' method='post'>";
        echo "<label for='trasaIDdoUsuniecia'>Wybierz trasę do usunięcia:</label>";
        echo "<select id='trasaIDdoUsuniecia' name='trasaIDdoUsuniecia' required>";

        while($row = $result->fetch_assoc()) {
            echo "<option value='{$row['trasaID']}'>Trasa {$row['trasaID']} - Kurier {$row['kurierID']}, Długość trasy: {$row['dlugoscTrasy']}, Czas dostawy: {$row['czasDostawy']}</option>";
        }

        echo "</select>";
        echo "<input type='submit' name='usunTraseSubmit' value='Usuń trasę'>";
        echo "</form>";
    } else {
        echo "Brak tras do usunięcia.";
    }

    // Wywołanie funkcji usuwającej trasę
    usunTrase($conn);
    ?>

    <br><br>

    <a href="index.php">
        <button type="button">Powrót do strony głównej</button>
    </a>

</body>

</html>

<?php
$conn->close();
?>
