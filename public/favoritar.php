<?php
session_start();
require 'db.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id'];
$id_tour = intval($_POST['id_tour'] ?? 0);

if($id_tour <= 0){
    header("Location: explorar.php");
    exit;
}

// Se vier param 'desfavoritar' remove o favorito
if(isset($_POST['desfavoritar'])){
    $stmt = $conn->prepare("DELETE FROM favoritos WHERE id_tour = ? AND id_turista = ?");
    $stmt->bind_param("ii", $id_tour, $id_usuario);
    $stmt->execute();
    $stmt->close();

    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Removido dos favoritos!")."&type=success");
    exit;
}

// Verifica se já existe
$stmt = $conn->prepare("SELECT id FROM favoritos WHERE id_tour = ? AND id_turista = ?");
$stmt->bind_param("ii", $id_tour, $id_usuario);
$stmt->execute();
$existe = $stmt->get_result()->num_rows > 0;
$stmt->close();

if(!$existe){
    $stmt = $conn->prepare("INSERT INTO favoritos (id_tour, id_turista, criado_em) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $id_tour, $id_usuario);
    $stmt->execute();
    $stmt->close();

    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Adicionado aos favoritos!")."&type=success");
    exit;
} else {
    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Já está nos favoritos.")."&type=success");
    exit;
}
