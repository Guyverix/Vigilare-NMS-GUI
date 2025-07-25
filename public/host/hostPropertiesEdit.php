<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once(__DIR__ . '/../../config/api.php');
require_once(__DIR__ . '/functions/hostFunctions.php');

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$quitEarly = 0;
$properties = [];

$id = $_POST['id'] ?? null;
$hostname = $_POST['hostname'] ?? null;

if (!$id || !$hostname) {
    loadUnknown("Page was called without correct parameters.");
    $quitEarly = 1;
}

if (isset($_POST['deviceProperties'])) {
    $properties = json_decode($_POST['deviceProperties'], true);
}

// Handle rediscovery
if (isset($_POST['rediscover'])) {
    $post = ['id' => $id];
    $response = callApiPost("/discovery/discover", $post, $headers);
    $decoded = json_decode($response['response'], true);
    $status = $decoded['statusCode'] ?? 500;

    if ($status === 200) {
        successMessage('Device discovery was successful.');
        $properties = $decoded['data'];
    } elseif ($status === 403) {
        load403Warn("Expired access credentials"); $quitEarly = 1;
    } else {
        decideResponse($status); $quitEarly = 1;
    }
}

// Handle add/remove key-value updates
if (isset($_POST['new_key']) || isset($_POST['remove_key'])) {
    $newKey = $_POST['new_key'] ?? '';
    $newValue = $_POST['new_value'] ?? '';
    $removeKey = $_POST['remove_key'] ?? '';
    $updatedProps = [];

    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['new_key', 'new_value', 'remove_key', 'remove_value', 'id', 'hostname'])) {
            $updatedProps[$key] = $value;
        }
    }

    if (!empty($removeKey)) {
        unset($updatedProps[$removeKey]);
    }

    if (!empty($newKey) && !empty($newValue)) {
        $updatedProps[$newKey] = $newValue;
    }

    $post = [
        'id' => $id,
        'component' => 'properties',
        'properties' => json_encode($updatedProps)
    ];

    $response = callApiPost("/device/update", $post, $headers);
    $decoded = json_decode($response['response'], true);
    $status = $decoded['statusCode'] ?? 500;

    if ($status === 200) {
        successMessage('Device Property changes have been saved.');
        $properties = $updatedProps;
    } elseif ($status === 403) {
        load403Warn("Expired access credentials"); $quitEarly = 1;
    } else {
        decideResponse($status); $quitEarly = 1;
    }
}

// Sanitize properties array
foreach (['Id', 'id', 'hostname'] as $reserved) {
    if (!empty($properties[$reserved])) {
        unset($properties[$reserved]);
    }
}
?>

<?php if ($quitEarly === 0): ?>
<div class="container mt-5">
  <h1 class="text-center mb-4">
    Change Properties for 
    <a href="/host/index.php?&page=deviceDetails.php&id=<?= htmlspecialchars($id) ?>">
      <?= htmlspecialchars($hostname) ?>
    </a>
  </h1>

  <?php include 'displayComponents/devicePropertiesForm.php'; ?>
</div>
<?php else: ?>
  <div class="alert alert-danger m-4">Failed to load or update device properties.</div>
<?php endif; ?>
