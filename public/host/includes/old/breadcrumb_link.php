<?php
echo '<div class="container mt-2">
  <ul id="bc1" class="breadcrumb"><span id="ellipses" style="display: inline;"></span>
  <li><a href="/index.php">Home</a> <span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
  </li>
  <li><a href="/infrastructure/index.php">Infrastructure</a><span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
  </li>
  <li><a href="/host/hostList.php">Host</a><span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
  </li>
  <li><span tabindex="0"><a href="/host/hostnameDetails.php?id=';
  echo $hostname;
  echo '">' . $hostname . '</a>&nbsp</span></li>';
  echo '<span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>';
  echo "<li><span tabindex='0'>&nbsp Component  &nbsp</span></li>";
  echo '</ul></div>';
?>
