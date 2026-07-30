<?php

require_once __DIR__ . '/config/database.php';

$projectName = 'Machine Issue Tracker';

$statement = $pdo->query(
    'SELECT id, machine_code, machine_name, location
     FROM machines
     ORDER BY machine_code ASC'
);

$machines = $statement->fetchAll();

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

    <p>Registered machines in the system:</p>

    <?php if (count($machines) === 0): ?>
        <p>No machines have been registered.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Machine Code</th>
                    <th>Machine Name</th>
                    <th>Location</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($machines as $machine): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($machine['id']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($machine['machine_code']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($machine['machine_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($machine['location']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>