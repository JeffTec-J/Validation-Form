<?php
require_once "db.php";

$errors = [];
$success = "";

$name = $email = $phone = $matric = $dob = "";
$department = $level = $gender = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $matric = strtoupper(trim($_POST["matric"] ?? ""));
    $dob = $_POST["dob"] ?? "";
    $department = $_POST["department"] ?? "";
    $level = $_POST["level"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";

    if ($name == "") $errors["name"] = "Enter your full name.";

    if ($email == "") {
        $errors["email"] = "Enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Enter a valid email address.";
    }

    // Example Cameroon phone format: 9 digits starting with 6
    if (!preg_match("/^6[0-9]{8}$/", $phone)) {
        $errors["phone"] = "Enter 9 digits starting with 6.";
    }

    // Example matriculation format: SE2026001
    if (!preg_match("/^SE[0-9]{7}$/", $matric)) {
        $errors["matric"] = "Use this format: SE2026001.";
    }

    if ($dob == "") {
        $errors["dob"] = "Choose your date of birth.";
    } elseif ($dob > date("Y-m-d")) {
        $errors["dob"] = "Date of birth cannot be in the future.";
    }

    if ($department == "") $errors["department"] = "Select your department.";
    if ($level == "") $errors["level"] = "Select your level.";
    if ($gender == "") $errors["gender"] = "Select your gender.";

    if (strlen($password) < 8) {
        $errors["password"] = "Password must have at least 8 characters.";
    }
    if ($password != $confirm) {
        $errors["confirm"] = "Passwords do not match.";
    }
    if (!isset($_POST["accuracy"])) {
        $errors["accuracy"] = "Please confirm the information is accurate.";
    }

    if (empty($errors)) {
        // Check whether the email or matriculation number already exists
        $check = $conn->prepare("SELECT id FROM students WHERE email = ? OR matric_no = ?");
        $check->bind_param("ss", $email, $matric);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $errors["general"] = "This email or matriculation number is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO students
                (full_name, email, phone, matric_no, date_of_birth, department, level, gender, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssssss",
                $name, $email, $phone, $matric, $dob,
                $department, $level, $gender, $hashedPassword
            );

            if ($stmt->execute()) {
                $success = "Student registered successfully!";
                $name = $email = $phone = $matric = $dob = "";
                $department = $level = $gender = "";
            } else {
                $errors["general"] = "Registration could not be saved. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

function showError($errors, $field) {
    if (isset($errors[$field])) {
        echo '<small class="error">' . htmlspecialchars($errors[$field]) . '</small>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <form class="form-box" method="POST" action="">
        <h1>Student Registration</h1>
        <p class="intro">Complete all required fields marked *</p>

        <?php if ($success != ""): ?>
            <p class="success" role="status"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if (isset($errors["general"])): ?>
            <p class="error" role="alert"><?= htmlspecialchars($errors["general"]) ?></p>
        <?php endif; ?>

        <label for="name">Full name *</label>
        <input type="text" id="name" name="name" placeholder="e.g. Ada Nfor" value="<?= htmlspecialchars($name) ?>">
        <?php showError($errors, "name"); ?>

        <label for="email">Email address *</label>
        <input type="email" id="email" name="email" placeholder="student@example.com" value="<?= htmlspecialchars($email) ?>">
        <?php showError($errors, "email"); ?>

        <label for="phone">Phone number *</label>
        <input type="tel" id="phone" name="phone" placeholder="677123456" value="<?= htmlspecialchars($phone) ?>">
        <?php showError($errors, "phone"); ?>

        <label for="matric">Matriculation number *</label>
        <input type="text" id="matric" name="matric" placeholder="SE2026001" value="<?= htmlspecialchars($matric) ?>">
        <?php showError($errors, "matric"); ?>

        <label for="dob">Date of birth *</label>
        <input type="date" id="dob" name="dob" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($dob) ?>">
        <?php showError($errors, "dob"); ?>

        <label for="department">Department *</label>
        <select id="department" name="department">
            <option value="">Select department</option>
            <?php
            $departments = ["Software Engineering", "Computer Engineering", "Network and Security", "Business Information Technology"];
            foreach ($departments as $item) {
                $selected = ($department == $item) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($item) . "' $selected>" . htmlspecialchars($item) . "</option>";
            }
            ?>
        </select>
        <?php showError($errors, "department"); ?>

        <label for="level">Level *</label>
        <select id="level" name="level">
            <option value="">Select level</option>
            <?php
            $levels = ["Level 1", "Level 2", "Level 3", "HND 1", "HND 2"];
            foreach ($levels as $item) {
                $selected = ($level == $item) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($item) . "' $selected>" . htmlspecialchars($item) . "</option>";
            }
            ?>
        </select>
        <?php showError($errors, "level"); ?>

        <fieldset>
            <legend>Gender *</legend>
            <?php foreach (["Female", "Male", "Prefer not to say"] as $item): ?>
                <label class="radio-label">
                    <input type="radio" name="gender" value="<?= htmlspecialchars($item) ?>" <?= ($gender == $item) ? "checked" : "" ?>>
                    <?= htmlspecialchars($item) ?>
                </label>
            <?php endforeach; ?>
            <?php showError($errors, "gender"); ?>
        </fieldset>

        <label for="password">Password *</label>
        <input type="password" id="password" name="password">
        <?php showError($errors, "password"); ?>

        <label for="confirm">Confirm password *</label>
        <input type="password" id="confirm" name="confirm">
        <?php showError($errors, "confirm"); ?>

        <label class="check-label">
            <input type="checkbox" name="accuracy" value="yes">
            I confirm that the information provided is accurate. *
        </label>
        <?php showError($errors, "accuracy"); ?>

        <button type="submit">Submit Registration</button>
    </form>
</main>
</body>
</html>
