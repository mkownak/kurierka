<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kurierex";

$conn = new mysqli($servername, $username, $password, $dbname);

function wyswietlTrasy($conn) {
    $sqlGetTrasy = "SELECT trasaID, kurierID, dlugoscTrasy, czasDostawy FROM trasa";
    $result = $conn->query($sqlGetTrasy);

    if ($result->num_rows > 0) {
        echo "<h2>Lista tras:</h2>";
        echo "<table border='1'>
                <tr>
                    <th>TrasaID</th>
                    <th>KurierID</th>
                    <th>Długość trasy</th>
                    <th>Czas dostawy</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['trasaID']}</td>
                    <td>{$row['kurierID']}</td>
                    <td>{$row['dlugoscTrasy']}</td>
                    <td>{$row['czasDostawy']}</td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "Brak tras w bazie danych.";
    }
}

?>

<!DOCTYPE html>
<html lang="pl">

<head>
<meta charset="UTF-8">
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        h2 {
            margin-top: 20px;
            color: #555;
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #fff;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
</head>

<body class="createbody">
    
    <h1>Panel Logistyka</h1>
    
    <h2>Wyświetl trasy</h2>
    <div class="createformularz">
        <?php
            // Wywołanie funkcji do wyświetlania tras
            wyswietlTrasy($conn);
        ?>
    </div>
    
    <br><br>

    <a href="index.php">
        <button type="button">Powrót do strony głównej</button>
    </a>
</body>

</html>

<?php
$conn->close();
?>
