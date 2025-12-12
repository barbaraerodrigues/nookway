<?php
session_start();
require 'db.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$id_tour = intval($_POST['id_tour']);
$dia_escolhido = $_POST['dia_escolhido'] ?? null;
$horario = $_POST['horario'] ?? null;
$quantidade = intval($_POST['quantidade_pessoas'] ?? 1);
$id_usuario = $_SESSION['id'];

$msg = "Preencha todos os campos corretamente.";
$type = "error";

$stmt = $conn->prepare("SELECT * FROM tours WHERE id=?");
$stmt->bind_param("i", $id_tour);
$stmt->execute(); 
$tour = $stmt->get_result()->fetch_assoc();
$stmt->close();
 
if(!$tour) exit("Tour inválida.");

if(!$dia_escolhido || !$horario || $quantidade < 1){
    header("Location: tour.php?id=$id_tour&msg=".urlencode($msg)."&type=$type");
    exit;
}

// Combina a data escolhida com o horário selecionado
$hora_inicio = explode('-', $horario)[0];
$data_reservada = $dia_escolhido . ' ' . $hora_inicio . ':00';

// Gera código de confirmação aleatório (6 dígitos)
$codigo_confirmacao = strtoupper(bin2hex(random_bytes(3))); // Ex: A1B2C3

// Inserção no banco com bind_param correto
$stmt = $conn->prepare("
INSERT INTO reservas 
(id_tour, id_turista, data_reserva, status, quantidade_pessoas, data_reservada, codigo_confirmacao)
VALUES (?, ?, NOW(), 'pendente', ?, ?, ?)
"); 
$stmt->bind_param("iiiss", $id_tour, $id_usuario, $quantidade, $data_reservada, $codigo_confirmacao);
$stmt->execute();
$stmt->close();

// Redireciona mostrando o código de confirmação
header("Location: minhas_reservas.php?msg=Reserva registrada com sucesso!&codigo=$codigo_confirmacao");
exit;
