<?php

function exigirLogin()
{
    if (!in_array($tipoUsuario, $perfisPermitidos, true)) {
        header("Location: index.php");
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