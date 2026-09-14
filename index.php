<?php

session_start();

require_once "config.php";

$nomeUsuario = $_SESSION["usuario_nome"] ?? null;

$produtosMaisVendidos = [];

try {
    // BD - SELECT / View Analítica
    $sqlMaisVendidos = "
        SELECT id, produto, categoria, preco, estoque, quantidade_vendida
        FROM vw_produtos_analitico
        ORDER BY quantidade_vendida DESC, produto ASC
        LIMIT 6
    ";

    $stmtMaisVendidos = $pdo->prepare($sqlMaisVendidos);
    $stmtMaisVendidos->execute();
    $produtosMaisVendidos = $stmtMaisVendidos->fetchAll();
} catch (PDOException $e) {
    $produtosMaisVendidos = [];
}

$categorias = [];

try {
    // BD - SELECT
    $stmtCategorias = $pdo->query("
        SELECT id, nome
        FROM categorias
        ORDER BY nome ASC
        LIMIT 6
    ");

    $categorias = $stmtCategorias->fetchAll();
} catch (PDOException $e) {
    $categorias = [];
}

$totalProdutos = 0;

try {
    // BD - SELECT / COUNT
    $stmtProdutos = $pdo->query("
        SELECT COUNT(*)
        FROM produtos
        WHERE ativo = TRUE
    ");

    $totalProdutos = (int) $stmtProdutos->fetchColumn();
} catch (PDOException $e) {
    $totalProdutos = 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Art&Co - Materiais Artísticos</title>

    <!-- DW - Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="src/css/style.css"
    >

    <style>
        .home-hero {
            padding: 75px 20px;
            background: transparent;
            text-align: center;
        }

        .home-hero h1 {
            margin-bottom: 14px;
            color: #202124;
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .home-hero h1 span {
            background: linear-gradient(90deg, #9b00ff, #00b894);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .home-hero p {
            max-width: 680px;
            margin: 0 auto 28px;
            color: #6f7785;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .home-hero .btn-artco {
            display: inline-block;
            padding: 9px 20px;
            border: 1px solid #8f00ff;
            border-radius: 7px;
            background-color: #8f00ff;
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .home-hero .btn-artco:hover {
            border-color: #7600d6;
            background-color: #7600d6;
            color: #ffffff;
        }

        .home-acessos {
            padding: 55px 0 35px;
        }

        .home-secao-titulo {
            margin-bottom: 8px;
            color: var(--artco-titulo);
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
        }

        .home-secao-subtitulo {
            margin-bottom: 35px;
            color: var(--artco-subtitulo);
            text-align: center;
        }

        .home-acesso-card {
            height: 100%;
            padding: 27px;
            border: 1px solid #e1e1e1;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            text-align: center;
            transition: all 0.2s ease;
        }

        .home-acesso-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 18px rgba(0, 0, 0, 0.10);
        }

        .home-acesso-card h3 {
            margin-bottom: 10px;
            color: #202124;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .home-acesso-card p {
            min-height: 45px;
            margin-bottom: 18px;
            color: #6f7785;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .home-acesso-link {
            display: inline-block;
            color: #555;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .home-acesso-link:hover {
            color: #8f00ff;
        }

        .home-categorias {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .home-categoria-link {
            display: inline-block;
            padding: 6px 11px;
            border: 1px solid #d8d8d8;
            border-radius: 6px;
            color: #555;
            background-color: #fafafa;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .home-categoria-link:hover {
            border-color: #8f00ff;
            background-color: #f8f1ff;
            color: #7600d6;
        }

        .home-mais-vendidos {
            padding: 45px 0 65px;
        }

        .home-carousel {
            padding: 0 45px;
        }

        .home-produto-card {
            height: 100%;
            overflow: hidden;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .home-produto-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 9px 24px rgba(0, 0, 0, 0.13);
        }

        .home-produto-imagem {
            width: 100%;
            height: 190px;
            overflow: hidden;
            background-color: #ffffff;
        }

        .home-produto-imagem img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-produto-body {
            padding: 18px;
        }

        .home-produto-categoria {
            margin-bottom: 6px;
            color: #8a45e8;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .home-produto-nome {
            min-height: 44px;
            margin-bottom: 8px;
            color: #182033;
            font-size: 1rem;
            font-weight: 700;
        }

        .home-produto-vendas {
            margin-bottom: 10px;
            color: #07945a;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .home-produto-preco {
            margin-bottom: 15px;
            color: #8b20c4;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .home-produto-botao {
            display: block;
            width: 100%;
            padding: 8px;
            border-radius: 7px;
            background: linear-gradient(90deg, #8f00ff, #00c9a7);
            color: #ffffff;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .home-produto-botao:hover {
            color: #ffffff;
            opacity: 0.9;
        }

        .home-carousel .carousel-control-prev,
        .home-carousel .carousel-control-next {
            width: 38px;
        }

        .home-carousel .carousel-control-prev-icon,
        .home-carousel .carousel-control-next-icon {
            width: 30px;
            height: 30px;
            padding: 7px;
            border-radius: 50%;
            background-color: #202124;
            background-size: 55%;
        }

        .home-final {
            padding: 50px 20px;
            background-color: #202124;
            text-align: center;
        }

        .home-final h2 {
            margin-bottom: 10px;
            color: #ffffff;
            font-size: 2rem;
            font-weight: 800;
        }

        .home-final p {
            max-width: 600px;
            margin: 0 auto 22px;
            color: #bfc0c2;
            line-height: 1.6;
        }

        .home-final-destaque {
            margin-bottom: 22px;
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .home-final a {
            display: inline-block;
            padding: 9px 20px;
            border: 1px solid #ffffff;
            border-radius: 7px;
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .home-final a:hover {
            background-color: #ffffff;
            color: #202124;
        }

        @media (max-width: 991px) {
            .home-hero h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 767px) {
            .home-hero {
                padding: 60px 20px;
            }

            .home-hero h1 {
                font-size: 2.2rem;
            }

            .home-carousel {
                padding: 0 35px;
            }
        }
    </style>
</head>

<body>

<?php
$base = "";
require_once "componentes/navbar.php";
?>

<section class="container py-5 home-hero">
    <div class="container">
        <?php if ($nomeUsuario): ?>
            <p class="mb-2">
                Olá, <?= htmlspecialchars($nomeUsuario) ?>!
            </p>
        <?php endif; ?>

        <h1>
            Crie, imagine, <span>transforme.</span>
        </h1>

        <p>
            Encontre materiais artísticos para transformar
            suas ideias em criações. Explore nosso catálogo
            e encontre o que precisa para o seu próximo projeto.
        </p>

        <a
            href="produtos/produtos.php"
            class="btn-artco"
        >
            Explorar produtos
        </a>
    </div>
</section>

<section class="home-acessos">
    <div class="container">
        <h2 class="home-secao-titulo">
            Encontre o que precisa
        </h2>

        <p class="home-secao-subtitulo">
            Acesse rapidamente as principais áreas da Art&Co.
        </p>

        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="home-acesso-card">
                    <h3>Explore nossas categorias</h3>

                    <p>
                        Encontre materiais organizados
                        por categoria para facilitar sua busca.
                    </p>

                    <?php if (count($categorias) > 0): ?>
                        <div class="home-categorias">
                            <?php foreach ($categorias as $categoria): ?>
                                <a
                                    href="produtos/produtos.php?categoria=<?= (int) $categoria["id"] ?>"
                                    class="home-categoria-link"
                                >
                                    <?= htmlspecialchars($categoria["nome"]) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <a
                            href="produtos/produtos.php"
                            class="home-acesso-link"
                        >
                            Ver categorias →
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="home-acesso-card">
                    <h3>Produtos</h3>

                    <p>
                        Confira todo o catálogo da Art&Co
                        e encontre materiais para seus projetos.
                    </p>

                    <a
                        href="produtos/produtos.php"
                        class="home-acesso-link"
                    >
                        Ver catálogo →
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="home-acesso-card">
                    <h3>Minhas compras</h3>

                    <p>
                        Consulte seus pedidos e acompanhe
                        seu histórico de compras.
                    </p>

                    <?php if ($nomeUsuario): ?>
                        <a
                            href="compras.php"
                            class="home-acesso-link"
                        >
                            Ver minhas compras →
                        </a>
                    <?php else: ?>
                        <a
                            href="login.php"
                            class="home-acesso-link"
                        >
                            Acessar minha conta →
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php if (count($produtosMaisVendidos) > 0): ?>
    <!-- TF - Produtos mais vendidos -->
    <section class="home-mais-vendidos">
        <div class="container">
            <h2 class="home-secao-titulo">
                Mais vendidos
            </h2>

            <p class="home-secao-subtitulo">
                Confira alguns dos produtos mais procurados
                pelos nossos clientes.
            </p>

            <!-- DW - Bootstrap / Carousel -->
            <div
                id="carouselMaisVendidos"
                class="carousel slide home-carousel"
                data-bs-ride="carousel"
            >
                <div class="carousel-inner">

                    <?php
                    $gruposProdutos = array_chunk(
                        $produtosMaisVendidos,
                        3
                    );
                    ?>

                    <?php foreach ($gruposProdutos as $indice => $grupo): ?>
                        <div
                            class="carousel-item <?= $indice === 0 ? "active" : "" ?>"
                        >
                            <div class="row g-4">

                                <?php foreach ($grupo as $produto): ?>
                                    <div class="col-12 col-md-4">
                                        <div class="home-produto-card">

                                            <div class="home-produto-imagem">
                                                <img
                                                    src="src/img/produto-padrao.jpg"
                                                    alt="<?= htmlspecialchars($produto["produto"]) ?>"
                                                >
                                            </div>

                                            <div class="home-produto-body">

                                                <div class="home-produto-categoria">
                                                    <?= htmlspecialchars($produto["categoria"]) ?>
                                                </div>

                                                <div class="home-produto-nome">
                                                    <?= htmlspecialchars($produto["produto"]) ?>
                                                </div>

                                                <div class="home-produto-vendas">
                                                    <?= (int) $produto["quantidade_vendida"] ?>
                                                    unidade(s) vendida(s)
                                                </div>

                                                <div class="home-produto-preco">
                                                    R$
                                                    <?= number_format(
                                                        $produto["preco"],
                                                        2,
                                                        ",",
                                                        "."
                                                    ) ?>
                                                </div>

                                                <a
                                                    href="produtos/produto.php?id=<?= (int) $produto["id"] ?>"
                                                    class="home-produto-botao"
                                                >
                                                    Ver produto
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

                <?php if (count($gruposProdutos) > 1): ?>
                    <button
                        class="carousel-control-prev"
                        type="button"
                        data-bs-target="#carouselMaisVendidos"
                        data-bs-slide="prev"
                    >
                        <span
                            class="carousel-control-prev-icon"
                            aria-hidden="true"
                        ></span>

                        <span class="visually-hidden">
                            Anterior
                        </span>
                    </button>

                    <button
                        class="carousel-control-next"
                        type="button"
                        data-bs-target="#carouselMaisVendidos"
                        data-bs-slide="next"
                    >
                        <span
                            class="carousel-control-next-icon"
                            aria-hidden="true"
                        ></span>

                        <span class="visually-hidden">
                            Próximo
                        </span>
                    </button>
                <?php endif; ?>

            </div>
        </div>
    </section>
<?php endif; ?>

<section class="home-final">
    <div class="container">
        <h2>
            Tudo o que você precisa para criar
        </h2>

        <p>
            Explore nosso catálogo e encontre materiais
            para diferentes técnicas, projetos e formas
            de expressão artística.
        </p>

        <a href="produtos/produtos.php">
            Conhecer o catálogo
        </a>
    </div>
</section>

<?php
require_once "componentes/footer.php";
?>

<!-- DW - Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>