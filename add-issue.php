<?php

require_once __DIR__ . '/config/database.php';

$errors = [];

$machineId = '';
$issueTitle = '';
$issueDescription = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $machineId = $_POST['machine_id'] ?? '';
    $issueTitle = trim($_POST['issue_title'] ?? '');
    $issueDescription = trim($_POST['issue_description'] ?? '');

    if ($machineId === '' || !ctype_digit($machineId)) {
        $errors[] = 'Please select a valid machine.';
    }

    if ($issueTitle === '') {
        $errors[] = 'Issue title is required.';
    }

    if ($issueDescription === '') {
        $errors[] = 'Issue description is required.';
    }

    if (strlen($issueTitle) > 150) {
        $errors[] = 'Issue title cannot exceed 150 characters.';
    }

    if (count($errors) === 0) {
        $checkMachine = $pdo->prepare(
            'SELECT COUNT(*)
             FROM machines
             WHERE id = :machine_id'
        );

        $checkMachine->execute([
            'machine_id' => $machineId
        ]);

        $machineExists = $checkMachine->fetchColumn();

        if (!$machineExists) {
            $errors[] = 'The selected machine does not exist.';
        }
    }

    if (count($errors) === 0) {
        $statement = $pdo->prepare(
            'INSERT INTO issues (
                machine_id,
                issue_title,
                issue_description
            )
            VALUES (
                :machine_id,
                :issue_title,
                :issue_description
            )'
        );

        $statement->execute([
            'machine_id' => $machineId,
            'issue_title' => $issueTitle,
            'issue_description' => $issueDescription
        ]);

        header('Location: index.php?created=1');
        exit;
    }
}

$machineStatement = $pdo->query(
    'SELECT id, machine_code, machine_name
     FROM machines
     ORDER BY machine_code ASC'
);

$machines = $machineStatement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Report Machine Issue</title>
</head>

<body>
    <h1>Report Machine Issue</h1>

    <p>
        <a href="index.php">Back to issue list</a>
    </p>

    <?php if (count($errors) > 0): ?>
        <div>
            <strong>Please correct the following:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="machine_id">Machine</label>

            <select
                id="machine_id"
                name="machine_id"
                required
            >
                <option value="">Select a machine</option>

                <?php foreach ($machines as $machine): ?>
                    <option
                        value="<?php echo htmlspecialchars($machine['id']); ?>"
                        <?php
                        echo (string) $machine['id'] === $machineId
                            ? 'selected'
                            : '';
                        ?>
                    >
                        <?php
                        echo htmlspecialchars(
                            $machine['machine_code']
                            . ' — '
                            . $machine['machine_name']
                        );
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="issue_title">Issue Title</label>

            <input
                type="text"
                id="issue_title"
                name="issue_title"
                maxlength="150"
                value="<?php echo htmlspecialchars($issueTitle); ?>"
                required
            >
        </div>

        <br>

        <div>
            <label for="issue_description">Issue Description</label>

            <textarea
                id="issue_description"
                name="issue_description"
                rows="6"
                cols="50"
                required
            ><?php echo htmlspecialchars($issueDescription); ?></textarea>
        </div>

        <br>

        <button type="submit">Submit Issue</button>
    </form>
</body>
</html>