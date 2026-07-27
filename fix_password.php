<?php
// One-time: copy password to password_hash for all existing users
// Delete this file after running once.
require_once 'config/db_connect.php';
$res = $conn->query("SELECT user_id, password FROM users WHERE (password_hash IS NULL OR password_hash = '')");
$updated = 0;
while ($row = $res->fetch_assoc()) {
    $hash = password_hash('Citizen@123', PASSWORD_BCRYPT);
    $upd = $conn->prepare("UPDATE users SET password_hash=?, login_id=IFNULL(NULLIF(login_id,''), CONCAT('user', user_id)) WHERE user_id=?");
    $upd->bind_param("si", $hash, $row['user_id']);
    $upd->execute();
    $upd->close();
    $updated++;
}
echo "Updated $updated user(s) with contract columns.";
$conn->close();
