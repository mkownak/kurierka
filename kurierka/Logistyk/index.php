<!DOCTYPE html>
<html lang="pl">

<head>
    <!-- ... (Pozostała część nagłówka HTML) ... -->
</head>

<body class="boddy">
    
    <h1>Panel Logistyka</h1>

    <h2>Wybierz akcję:</h2>
    <ul> 
        <a href="wyświetlTrasy.php">
        <button type="button">Wyświetl trasy</button>
        </a>
        <a href="edytujTrase.php">
        <button type="button">Edytuj trasę</button>
        </a>
        <a href="usunTrase.php">
        <button type="button">Usuń trasę</button>
        </a>
        <a href="dodajTrase.php">
        <button type="button">Dodaj trasę</button>
        </a>
    </ul>

    <?php
    // Obsługa formularza - edycja trasy
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["edytujTrase"])) {
            // Przekierowanie do pliku edytujTrase.php
            header("Location: edytujTrase.php");
            exit();
        }

        // Obsługa formularza - usunięcie trasy
        if (isset($_POST["usunTrase"])) {
            // Przekierowanie do pliku usunTrase.php
            header("Location: usunTrase.php");
            exit();
        }
    }
    ?>

</body>

</html>
