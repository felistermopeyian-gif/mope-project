</p<?php
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $kra_pin = trim($_POST['kra_pin']);
}

// Validate errors
$error = [];

// Check if all fields are filled
if (empty($name) || empty($email) || empty($kra_pin)) {
    $error[] = "ALL FIELDS ARE REQUIRED";
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = "Invalid email format";
}

// Validate KRA PIN using regex
if (!preg_match("/^[A-Z][0-9]{9}[A-Z]$/", $kra_pin)) {
    $error[] = "Invalid KRA PIN format";
}

// Check for duplication
$file = 'kra_data.txt';
$existing_pins = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

foreach ($existing_pins as $line) {
    $data = explode("|", $line);
    if (isset($data[2]) && $data[2] == $kra_pin) {
        $error[] = "This KRA PIN already exists.";
        break;
    }
}

// Save to file if no errors
if (empty($error)) {
    // Save name | email | pin
    $record = $name . "|" . $email . "|" . $kra_pin . "\n";
    file_put_contents($file, $record, FILE_APPEND);
    echo "<p style='color:green;'>Registration successful!</p>";
} else {
    foreach ($error as $err) {
        echo "<p style='color:red;'>$err</p>";
    }
}

// Display table of saved entries
if (file_exists($file)) {
    $records = file($file, FILE_IGNORE_NEW_LINES);

    echo "<h3>Registered KRA PINs</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Name</th><th>Email</th><th>KRA PIN</th></tr>";

    foreach ($records as $record) {
        $parts = explode("|", $record);
        if (count($parts) == 3) {
            list($name, $email, $kra_pin) = $parts;
            echo "<tr><td>$name</td><td>$email</td><td>$kra_pin</td></tr>";
        }
    }

    echo "</table>";
}

echo "<p><a href='view.php'>View All Records</a></p>";
?>
>"