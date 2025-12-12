<?php
session_start();
require 'db.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$id_tour = intval($_POST['id_tour'] ?? 0);
$id_usuario = $_SESSION['id'];
$nota = intval($_POST['nota'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if($id_tour <= 0){
    header("Location: explorar.php");
    exit;
}

if($nota < 1 || $nota > 5 || $comentario === ''){
    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Preencha todos os campos da avaliação.")."&type=error");
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO avaliacoes (id_tour, id_turista, nota, comentario, criado_em)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("iiis", $id_tour, $id_usuario, $nota, $comentario);
$ok = $stmt->execute();
$stmt->close();

if($ok){
    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Avaliação enviada!")."&type=success");
    exit;
} else {
    header("Location: tour.php?id=".$id_tour."&msg=".urlencode("Erro ao enviar avaliação.")."&type=error");
    exit;
}
