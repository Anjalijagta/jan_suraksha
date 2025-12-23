<?php
$password = 'admin';
$hash = password_hash($password, PASSWORD_DEFAULT);
?>
<!DOCTYPE html>
<html>
<head><title>Hash Generator</title></head>
<body style="font-family:monospace;padding:50px;background:#f0f0f0">
<h2>✅ Copy this EXACT hash for password "admin":</h2>
<div style="background:#fff;padding:20px;border:2px solid green;font-size:16px">
<?= $hash ?> <span style="color:green">(Length: <?= strlen($hash) ?>)</span>
</div>
<p><strong>SQL to run:</strong></p>
<pre style="background:#eee;padding:15px">UPDATE admins SET password_hash = '<?= $hash ?>' WHERE admin_id = 'admin';</pre>
</body>
</html>
