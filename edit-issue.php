<?php

require_once __DIR__ . '/config/database.php';

$allowedStatuses = [
    'Open',
    'In Progress',
    'Resolved'
];

$errors = [];

$issueId = $_POST['issue_id']
    ?? $_GET['id']
    ?? '';

if ($issueId === '' || !ctype_digit($issueId)) {
    http_response_code(400);
    exit('Invalid issue ID.');
}

$statement = $pdo->prepare(
    'SELECT
        issues.id,
        issues.issue_title,
        issues.issue_description,
        issues.status,
        issues.reported_at,
        machines.machine_code,
        machines.machine_name
     FROM issues
     INNER JOIN machines
        ON issues.machine_id = machines.id
     WHERE issues.id = :issue_id'
);

$statement->execute([
    'issue_id' => $issueId
]);

$issue = $statement->fetch();

if (!$issue) {
    http_response_code(404);
    exit('Issue not found.');
}

$selectedStatus = $issue['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedStatus = $_POST['status'] ?? '';

    if (!in_array($selectedStatus, $allowedStatuses, true)) {
        $errors[] = 'Please select a valid status.';
    }

    if (count($errors) === 0) {
        $updateStatement = $pdo->prepare(
            'UPDATE issues
             SET status = :status
             WHERE id = :issue_id'
        );

        $updateStatement->execute([
            'status' => $selectedStatus,
            'issue_id' => $issueId
        ]);

        header('Location: index.php?updated=1');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Update Issue Status</title>
</head>

<body>
    <h1>Update Issue Status</h1>

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

    <h2>
        <?php echo htmlspecialchars($issue['issue_title']); ?>
    </h2>

    <p>
        <strong>Machine:</strong>

        <?php
        echo htmlspecialchars(
            $issue['machine_code']
            . ' — '
            . $issue['machine_name']
        );
        ?>
    </p>

    <p>
        <strong>Description:</strong>

        <?php
        echo htmlspecialchars(
            $issue['issue_description']
        );
        ?>
    </p>

    <p>
        <strong>Reported at:</strong>

        <?php
        echo htmlspecialchars(
            $issue['reported_at']
        );
        ?>
    </p>

    <form method="POST" action="">
        <input
            type="hidden"
            name="issue_id"
            value="<?php echo htmlspecialchars($issue['id']); ?>"
        >

        <div>
            <label for="status">Status</label>

            <select
                id="status"
                name="status"
                required
            >
                <?php foreach ($allowedStatuses as $status): ?>
                    <option
                        value="<?php echo htmlspecialchars($status); ?>"
                        <?php
                        echo $selectedStatus === $status
                            ? 'selected'
                            : '';
                        ?>
                    >
                        <?php echo htmlspecialchars($status); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <button type="submit">Update Status</button>
    </form>
</body>
</html>