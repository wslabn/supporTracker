<?php
// Portal controller - bypass authentication and include portal directly
// This controller handles /SupporTracker/portal requests from main app routing

// Don't use the main config.php or authentication - go directly to portal
include 'portal/index.php';
exit;
?>