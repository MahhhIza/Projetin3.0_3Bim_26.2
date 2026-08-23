<?php

function exigirLogin()
{
    if (!isset($_SESSION["usuario_id"])) {
        header("Location: login.php");
        exit;
    }
}

function exigirPerfil($perfisPermitidos)
{
    exigirLogin();

    $tipoUsuario = $_SESSION["usuario_tipo"] ?? "";

    if (!in_array($tipoUsuario, $perfisPermitidos, true)) {
        header("Location: painel.php");
        exit;
    }
}