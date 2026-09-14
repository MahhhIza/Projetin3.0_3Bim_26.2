<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Página não encontrada - Art&Co</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="src/css/style.css"
    >

    <style>
        .erro-404 {
            min-height: 65vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .erro-404-numero {
            font-size: 7rem;
            font-weight: 800;
            color: var(--artco-titulo);
            line-height: 1;
            margin-bottom: 15px;
        }

        .erro-404-titulo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .erro-404-texto {
            color: var(--artco-subtitulo);
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <?php
    $base = "";
    require_once "componentes/navbar.php";
    ?>

    <main class="container">
        <section class="erro-404">

            <div>
                <div class="erro-404-numero">
                    404
                </div>

                <h1 class="erro-404-titulo">
                    Página não encontrada
                </h1>

                <p class="erro-404-texto">
                    A página que você está procurando não existe ou foi movida.
                </p>

                <a href="index.php" class="btn btn-artco">
                    Voltar para a página inicial
                </a>
            </div>

        </section>
    </main>

    <?php
    require_once "componentes/footer.php";
    ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>