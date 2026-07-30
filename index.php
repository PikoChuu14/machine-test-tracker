<?php

require_once __DIR__ . '/config/database.php';

$projectName = 'Machine Issue Tracker';

$issueCreated = isset($_GET['created'])
    && $_GET['created'] === '1';

$issueUpdated = isset($_GET['updated'])
    && $_GET['updated'] === '1';

$issueDeleted = isset($_GET['deleted'])
    && $_GET['deleted'] === '1';

$statement = $pdo->query(
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
     ORDER BY issues.reported_at DESC'
);

$issues = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?php echo htmlspecialchars($projectName); ?></title>
</head>

<body>
    <h1><?php echo htmlspecialchars($projectName); ?></h1>

    <p>
        <a href="add-issue.php">Report New Issue</a>
    </p>

    <?php if ($issueCreated): ?>
        <p>
            <strong>Issue reported successfully.</strong>
        </p>
    <?php endif; ?>

    <?php if ($issueUpdated): ?>
    <p>
        <strong>Issue status updated successfully.</strong>
    </p>
    <?php endif; ?>

    <?php if ($issueDeleted): ?>
        <p>
            <strong>Issue deleted successfully.</strong>
        </p>
    <?php endif; ?>

    <?php if (count($issues) === 0): ?>
        <p>No machine issues have been reported.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Machine</th>
                    <th>Issue</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Reported At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($issues as $issue): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($issue['id']); ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $issue['machine_code']
                                . ' — '
                                . $issue['machine_name']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $issue['issue_title']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $issue['issue_description']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $issue['status']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $issue['reported_at']
                            );
                            ?>
                        </td>

                        <td>
                            <a
                                href="edit-issue.php?id=<?php
                                echo urlencode($issue['id']);
                                ?>"
                            >
                                Update Status
                            </a>
                        </td>

                        <td>
                            <a
                                href="delete-issue.php?id=<?php
                                echo urlencode($issue['id']);
                                ?>"
                            >
                                Delete
                            </a>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>