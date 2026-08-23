<?php

function exigirLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["usuario_id"])) {
        header("Location: login.php");
        exit;
    }
}

function exigirPerfil(array $perfisPermitidos)
{
    exigirLogin();

    $tipoUsuario = $_SESSION["usuario_tipo"] ?? "usuario";

    if (!in_array($tipoUsuario, $perfisPermitidos, true)) {

        // Usuário comum volta para a página inicial
        if ($tipoUsuario === "usuario") {
            header("Location: index.php");
            exit;
        }

        // Vendedor e administrador sem permissão
        header("Location: painel.php");
        exit;
    }
}