<?php

require_once __DIR__ . '/config/database.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmation = $_POST['confirm_delete'] ?? '';

    if ($confirmation !== 'yes') {
        $errors[] = 'The deletion was not confirmed.';
    }

    if (count($errors) === 0) {
        $deleteStatement = $pdo->prepare(
            'DELETE FROM issues
             WHERE id = :issue_id'
        );

        $deleteStatement->execute([
            'issue_id' => $issueId
        ]);

        header('Location: index.php?deleted=1');
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

    <title>Delete Machine Issue</title>
</head>

<body>
    <h1>Delete Machine Issue</h1>

    <?php if (count($errors) > 0): ?>
        <div>
            <strong>Unable to delete the issue:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <p>
        Are you sure you want to permanently delete this issue?
    </p>

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
        <strong>Status:</strong>

        <?php echo htmlspecialchars($issue['status']); ?>
    </p>

    <p>
        <strong>Reported at:</strong>

        <?php echo htmlspecialchars($issue['reported_at']); ?>
    </p>

    <form method="POST" action="">
        <input
            type="hidden"
            name="issue_id"
            value="<?php echo htmlspecialchars($issue['id']); ?>"
        >

        <button
            type="submit"
            name="confirm_delete"
            value="yes"
        >
            Yes, Delete Issue
        </button>
    </form>

    <p>
        <a href="index.php">Cancel and return to issue list</a>
    </p>
</body>
</html>