<?php
include './connexion.php.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT ID_user, username, email, password, ID_role FROM users WHERE ID_user = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
}
?>
