<?php
// deviceGeneral.php
// expects $sharedDevice passed in from parent

//debugger($sharedDevice);
//exit();

if (!isset($sharedDevice) || !is_array($sharedDevice)) {
    echo "No device data found.";
    exit;
}
?>

<div class="container my-4">

    <h2 class="mb-4">Device General Information</h2>

    <!-- Performance -->
    <div class="card mb-3">
        <div class="card-header">
            Performance
        </div>
        <div class="card-body">
            <?php if (!empty($sharedDevice['performance'])): ?>
                <pre><?php print_r($sharedDevice['performance']); ?></pre>
            <?php else: ?>
                <p>No performance data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Properties -->
    <div class="card mb-3">
        <div class="card-header">
            Properties
        </div>
        <div class="card-body">
            <?php if (!empty($sharedDevice['properties'])): ?>
                <table class="table table-striped">
                    <tbody>
                    <?php foreach ($sharedDevice['properties'] as $key => $value): ?>
                        <tr>
                            <th><?= htmlspecialchars($key) ?></th>
                            <td><?= htmlspecialchars($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No properties found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Events -->
    <div class="card mb-3">
        <div class="card-header">
            Active Events
        </div>
        <div class="card-body">
            <?php if (!empty($sharedDevice['activeEvents'])): ?>
                <pre><?php print_r($sharedDevice['activeEvents']); ?></pre>
            <?php else: ?>
                <p>No active events reported.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- History Events -->
    <div class="card mb-3">
        <div class="card-header">
            History Events
        </div>
        <div class="card-body">
            <?php if (!empty($sharedDevice['historyEvents'])): ?>
                <pre><?php print_r($sharedDevice['historyEvents']); ?></pre>
            <?php else: ?>
                <p>No history events found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Availability -->
    <div class="card mb-3">
        <div class="card-header">
            Availability
        </div>
        <div class="card-body">
            <?= htmlspecialchars($sharedDevice['availibility'] ?? 'Unknown') ?>
        </div>
    </div>

    <!-- History Time -->
    <div class="card mb-3">
        <div class="card-header">
            History Time
        </div>
        <div class="card-body">
            <?= htmlspecialchars($sharedDevice['historyTime'] ?? 'Not recorded') ?>
        </div>
    </div>

    <!-- Event Time -->
    <div class="card mb-3">
        <div class="card-header">
            Event Time
        </div>
        <div class="card-body">
            <?= htmlspecialchars($sharedDevice['eventTime'] ?? 'Not recorded') ?>
        </div>
    </div>

    <!-- Storage -->
    <div class="card mb-3">
        <div class="card-header">
            Storage
        </div>
        <div class="card-body">
            <?php if (!empty($sharedDevice['storage'])): ?>
                <pre><?php print_r($sharedDevice['storage']); ?></pre>
            <?php else: ?>
                <p>No storage data available.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
