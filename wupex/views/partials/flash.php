<?php
$types = ['success','error','warning','info'];
foreach($types as $type) {
    $msg = Session::flash($type);
    if($msg) echo "<div class='flash $type'>$msg</div>";
}
