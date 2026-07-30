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

/*count the total issues to display on the page*/ 
$totalIssues = count($issues);
$openIssues = 0;
$inProgressIssues = 0;
$resolvedIssues = 0;

foreach ($issues as $issue) {
    if ($issue['status'] === 'Open') {
        $openIssues++;
    }

    if ($issue['status'] === 'In Progress') {
        $inProgressIssues++;
    }

    if ($issue['status'] === 'Resolved') {
        $resolvedIssues++;
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

    <title><?php echo htmlspecialchars($projectName); ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="assets/style.css"
        rel="stylesheet"
    >
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">
                Machine Issue Tracker
            </a>

            <a class="btn btn-primary" href="add-issue.php">
                Report New Issue
            </a>
        </div>
    </nav>

    <main class="container-fluid page-container py-4">
        <div class="mb-4">
            <h1 class="h3 mb-1">Issue Dashboard</h1>

            <p class="text-muted mb-0">
                Monitor and manage reported machine problems.
            </p>
        </div>

        <?php if ($issueCreated): ?>
            <div class="alert alert-success">
                Issue reported successfully.
            </div>
        <?php endif; ?>

        <?php if ($issueUpdated): ?>
            <div class="alert alert-success">
                Issue status updated successfully.
            </div>
        <?php endif; ?>

        <?php if ($issueDeleted): ?>
            <div class="alert alert-success">
                Issue deleted successfully.
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Issues</p>

                        <h2 class="mb-0">
                            <?php echo $totalIssues; ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Open</p>

                        <h2 class="mb-0">
                            <?php echo $openIssues; ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">In Progress</p>

                        <h2 class="mb-0">
                            <?php echo $inProgressIssues; ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Resolved</p>

                        <h2 class="mb-0">
                            <?php echo $resolvedIssues; ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h2 class="h5 mb-0">Reported Issues</h2>
            </div>

            <div class="card-body p-0">
                <?php if (count($issues) === 0): ?>
                    <div class="p-4 text-center">
                        <p class="text-muted mb-3">
                            No machine issues have been reported.
                        </p>

                        <a class="btn btn-primary" href="add-issue.php">
                            Report First Issue
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover issue-table mb-0">
                            <thead class="table-light">
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
                                            <?php
                                            echo htmlspecialchars(
                                                $issue['id']
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?php
                                                echo htmlspecialchars(
                                                    $issue['machine_code']
                                                );
                                                ?>
                                            </strong>

                                            <br>

                                            <small class="text-muted">
                                                <?php
                                                echo htmlspecialchars(
                                                    $issue['machine_name']
                                                );
                                                ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $issue['issue_title']
                                            );
                                            ?>
                                        </td>

                                        <td class="issue-description">
                                            <?php
                                            echo htmlspecialchars(
                                                $issue['issue_description']
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            $badgeClass = match (
                                                $issue['status']
                                            ) {
                                                'Open' => 'text-bg-danger',
                                                'In Progress' => 'text-bg-warning',
                                                'Resolved' => 'text-bg-success',
                                                default => 'text-bg-secondary'
                                            };
                                            ?>

                                            <span
                                                class="badge <?php
                                                echo $badgeClass;
                                                ?>"
                                            >
                                                <?php
                                                echo htmlspecialchars(
                                                    $issue['status']
                                                );
                                                ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $issue['reported_at']
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                <a
                                                    class="btn btn-sm btn-outline-primary"
                                                    href="edit-issue.php?id=<?php
                                                    echo urlencode(
                                                        $issue['id']
                                                    );
                                                    ?>"
                                                >
                                                    Update
                                                </a>

                                                <a
                                                    class="btn btn-sm btn-outline-danger"
                                                    href="delete-issue.php?id=<?php
                                                    echo urlencode(
                                                        $issue['id']
                                                    );
                                                    ?>"
                                                >
                                                    Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>
</body>

</html>