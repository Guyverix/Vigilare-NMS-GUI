<?php
/*
  Renders RRD graphs for a given template.
  This version improves structure, time selection, and image rendering.
*/

require_once(__DIR__ . '/../../../functions/generalFunctions.php');
require_once(__DIR__ . "/../../../config/api.php");
require_once(__DIR__ . "/../functions/hostFunctions.php");

$hostname = $_POST['hostname'] ?? '';
$id = $_POST['id'] ?? '';
$templateName = $_POST['templateName'] ?? '';
$files = $_POST['files'] ?? '[]';

$startNumber = $_POST['startNumber'] ?? '1';
$startRange  = $_POST['startRange'] ?? 'd';
$startTime   = "-$startNumber$startRange";

$endNumber = $_POST['endNumber'] ?? 'now';
$endRange  = $_POST['endRange'] ?? 'h';
$endTime   = ($endNumber !== 'now') ? "-$endNumber$endRange" : 'now';

$fileArray = json_decode($files, true);
if (!is_array($fileArray)) $fileArray = [];

//debugger($fileArray);

$headers = [ 'Authorization: Bearer ' . $_COOKIE['token'] ];

?>
<div class="container">
  <div class="text-center mt-5">
    <h1>RRD Graphs for <a href="/host/index.php?page=deviceDetails.php&id=<?= htmlspecialchars($id) ?>">
      <?= htmlspecialchars($hostname) ?></a></h1>
    <br>
  </div>

  <form id="changeTimes" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <input type="hidden" name="hostname" value="<?= htmlspecialchars($hostname) ?>">
    <input type="hidden" name="templateName" value="<?= htmlspecialchars($templateName) ?>">
    <input type="hidden" name="files" value='<?= htmlspecialchars($files) ?>'>

    <table class="table table-bordered bg-primary text-white">
      <thead>
        <tr><th>From Range</th><th>Until Range</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <input type="text" name="startNumber" value="<?= htmlspecialchars($startNumber) ?>" size="3">
            <select name="startRange">
              <?php foreach (['d' => 'days', 'h' => 'hours', 'w' => 'weeks', 'm' => 'months'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($startRange === $val ? 'selected' : '') ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
            <input type="text" name="endNumber" value="<?= htmlspecialchars($endNumber) ?>" size="3">
            <select name="endRange">
              <?php foreach (['d' => 'days', 'h' => 'hours', 'w' => 'weeks', 'm' => 'months'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($endRange === $val ? 'selected' : '') ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="text-center">
      <button type="submit" class="btn btn-primary">Change Timeframe</button>
      <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
    </div>
  </form>

  <table class="table table-striped table-hover mt-4">
    <thead>
      <tr><th class="text-center">RRD Graphs for template: <?= htmlspecialchars($templateName) ?></th></tr>
    </thead>
    <tbody>
    <?php foreach ($fileArray as $file):
      $file = trim($file, '"');
      $post = [
        'hostname' => $hostname,
        'file' => $file,
        'filter' => $templateName,
        'start' => $startTime,
        'IgnoreMatch' => ["/run"]
      ];
      $rawRenderRrd = callApiPost("/render/render", $post, $headers);
      $renderRrd = json_decode($rawRenderRrd['response'], true);

      if ($renderRrd['statusCode'] !== 200) {
        echo '<tr><td class="text-warning">Error loading graph for ' . htmlspecialchars($file) . '</td></tr>';
        continue;
      }
      $images = isset($renderRrd['data'][0]) ? $renderRrd['data'] : [$renderRrd['data']];
      foreach ($images as $img):
        $imgUrl = $apiHttp . $apiHostname . ':' . $apiPort . $img['image'];
        $mime = exif_imagetype($imgUrl);
        $mimeType = image_type_to_mime_type($mime);
        $base64 = base64_encode(file_get_contents($imgUrl));
        ?>
        <tr>
          <td class="text-center">
            <img src="data:<?= $mimeType ?>;base64,<?= $base64 ?>" width="900" height="200">
          </td>
        </tr>
      <?php endforeach;
    endforeach; ?>
    </tbody>
  </table>
</div>
