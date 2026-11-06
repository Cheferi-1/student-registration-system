<?php
session_start();

// Escape output
function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Form values
$values = [
    'full_name' => '',
    'email' => '',
    'telephone' => '',
    'date_of_birth' => '',
    'programme' => '',
    'level' => '',
    'gender' => ''
];

$errors = [];
$success = '';
$databaseError = '';

$programmes = [
    'Software Engineering',
    'Computer Science',
    'Information Technology',
    'Computer Engineering'
];

$levels = [
    'HND',
    'Degree Year 1',
    'Degree Year 2',
    'Degree Year 3',
    'Degree Year 4'
];

$genders = ['Male', 'Female'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get submitted values
    foreach ($values as $field => $unused) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    $policy = isset($_POST['policy']) &&
              $_POST['policy'] === 'yes';

    // Validate name
    $nameLength = preg_match_all('/./us', $values['full_name']);

    if ($values['full_name'] === '') {
        $errors['full_name'] = 'Please enter your full name.';
    } elseif (
        $nameLength < 3 ||
        $nameLength > 80 ||
        !preg_match("/^[\p{L}][\p{L}\p{M} .'-]*[\p{L}]$/u", $values['full_name'])
    ) {
        $errors['full_name'] = 'Name must contain 3–80 valid characters.';
    }

    // Validate email
    if ($values['email'] === '') {
        $errors['email'] = 'Please enter your email.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    // Validate telephone
    if ($values['telephone'] === '') {
        $errors['telephone'] = 'Please enter your telephone number.';
    } elseif (!preg_match('/^6[0-9]{8}$/', $values['telephone'])) {
        $errors['telephone'] = 'Enter a 9-digit Cameroon mobile number beginning with 6.';
    }

    // Validate date of birth
    if ($values['date_of_birth'] === '') {
        $errors['date_of_birth'] = 'Select your date of birth.';
    } else {
        $date = DateTime::createFromFormat('!Y-m-d', $values['date_of_birth']);
        $dateErrors = DateTime::getLastErrors();

        if (
            !$date ||
            ($dateErrors !== false &&
            ($dateErrors['warning_count'] > 0 ||
             $dateErrors['error_count'] > 0)) ||
            $date->format('Y-m-d') !== $values['date_of_birth']
        ) {
            $errors['date_of_birth'] = 'Enter a valid date.';
        } elseif ($values['date_of_birth'] >= date('Y-m-d')) {
            $errors['date_of_birth'] = 'Date of birth must be in the past.';
        }
    }

    // Validate programme
    if (!in_array($values['programme'], $programmes, true)) {
        $errors['programme'] = 'Choose a programme.';
    }

    // Validate level
    if (!in_array($values['level'], $levels, true)) {
        $errors['level'] = 'Choose a level.';
    }

    // Validate gender
    if (!in_array($values['gender'], $genders, true)) {
        $errors['gender'] = 'Choose your gender.';
    }

    // Validate policy
    if (!$policy) {
        $errors['policy'] = 'You must accept the registration policy.';
    }

    // Save valid registration
    if (empty($errors)) {
        try {

            $host = 'localhost';
            $port = 3307;
            $dbname = 'lab01_student_registration';
            $username = 'root';
            $password = '';

            $conn = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password
            );

            $conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            // Insert student
            $sql = "INSERT INTO students
            (full_name, email, telephone, date_of_birth,
             programme, level, gender)
            VALUES
            (:full_name, :email, :telephone, :date_of_birth,
             :programme, :level, :gender)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':full_name' => $values['full_name'],
                ':email' => $values['email'],
                ':telephone' => $values['telephone'],
                ':date_of_birth' => $values['date_of_birth'],
                ':programme' => $values['programme'],
                ':level' => $values['level'],
                ':gender' => $values['gender']
            ]);

            if ($stmt->rowCount() === 1) {
                $success = 'Registration completed successfully!';

                foreach ($values as $field => $unused) {
                    $values[$field] = '';
                }
            }

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $errors['email'] = 'This email is already registered.';
            } else {
                $databaseError = 'Unable to save registration. Check your database connection.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="form-card">

        <header>
            <p class="tag">LAB 01</p>
            <h1>Student Registration</h1>
            <p>Complete the form to register for a degree programme.</p>
        </header>

        <?php if ($success): ?>
            <div class="success">
                <?= clean($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($databaseError): ?>
            <div class="error-box">
                <?= clean($databaseError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">

            <div class="form-group">
                <label>Full name</label>
                <input type="text" name="full_name"
                    value="<?= clean($values['full_name']) ?>"
                    maxlength="80">

                <?php if (isset($errors['full_name'])): ?>
                    <small><?= clean($errors['full_name']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email address</label>
                <input type="email" name="email"
                    value="<?= clean($values['email']) ?>">

                <?php if (isset($errors['email'])): ?>
                    <small><?= clean($errors['email']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Telephone</label>
                <input type="tel" name="telephone"
                    maxlength="9"
                    placeholder="6XXXXXXXX"
                    value="<?= clean($values['telephone']) ?>">

                <?php if (isset($errors['telephone'])): ?>
                    <small><?= clean($errors['telephone']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Date of birth</label>
                <input type="date" name="date_of_birth"
                    value="<?= clean($values['date_of_birth']) ?>">

                <?php if (isset($errors['date_of_birth'])): ?>
                    <small><?= clean($errors['date_of_birth']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Programme</label>
                <select name="programme">
                    <option value="">Choose a programme</option>

                    <?php foreach ($programmes as $programme): ?>
                        <option value="<?= clean($programme) ?>"
                            <?= $values['programme'] === $programme ? 'selected' : '' ?>>
                            <?= clean($programme) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <?php if (isset($errors['programme'])): ?>
                    <small><?= clean($errors['programme']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Level</label>
                <select name="level">
                    <option value="">Choose a level</option>

                    <?php foreach ($levels as $level): ?>
                        <option value="<?= clean($level) ?>"
                            <?= $values['level'] === $level ? 'selected' : '' ?>>
                            <?= clean($level) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <?php if (isset($errors['level'])): ?>
                    <small><?= clean($errors['level']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Gender</label>

                <?php foreach ($genders as $gender): ?>
                    <label class="radio-label">
                        <input type="radio" name="gender"
                            value="<?= clean($gender) ?>"
                            <?= $values['gender'] === $gender ? 'checked' : '' ?>>
                        <?= clean($gender) ?>
                    </label>
                <?php endforeach; ?>

                <?php if (isset($errors['gender'])): ?>
                    <small><?= clean($errors['gender']) ?></small>
                <?php endif; ?>
            </div>

            <div class="policy-row">
                <label>
                    <input type="checkbox" name="policy" value="yes"
                        <?= isset($_POST['policy']) && $_POST['policy'] === 'yes' ? 'checked' : '' ?>>
                    I agree to the registration policy.
                </label>

                <?php if (isset($errors['policy'])): ?>
                    <small><?= clean($errors['policy']) ?></small>
                <?php endif; ?>
            </div>

            <div class="button-row">
                <button type="submit">Submit Form</button>
                <button type="reset" class="reset-btn">Reset</button>
            </div>

        </form>

    </div>

    <footer>Student Registration System</footer>

</div>

</body>
</html>