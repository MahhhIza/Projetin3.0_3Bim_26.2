function ehProdutoAnalitico(valor) {
    if (typeof valor !== "object" ||
        valor === null) {
        return false;
    }
    const produto = valor;
    return (typeof produto.id === "number" &&
        typeof produto.produto === "string" &&
        typeof produto.categoria === "string" &&
        typeof produto.preco === "number" &&
        typeof produto.estoque === "number" &&
        typeof produto.quantidade_vendida === "number" &&
        typeof produto.faturamento === "number");
}
function ehListaProdutos(valor) {
    return (Array.isArray(valor) &&
        valor.every(ehProdutoAnalitico));
}
function atualizarTexto(id, texto) {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.textContent = texto;
    }
}
function formatarMoeda(valor) {
    return valor.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}
function exibirRanking(produtos) {
    const elemento = document.getElementById("rankingProdutos");
    if (!elemento) {
        return;
    }
    const ranking = produtos
        .filter((produto) => produto.quantidade_vendida > 0)
        .map((produto) => ({
        nome: produto.produto,
        quantidade: produto.quantidade_vendida
    }))
        .sort((a, b) => b.quantidade - a.quantidade)
        .slice(0, 3);
    if (ranking.length === 0) {
        elemento.innerHTML =
            "<p class='text-muted mb-0'>" +
                "Nenhuma venda registrada." +
                "</p>";
        return;
    }
    elemento.innerHTML =
        ranking
            .map((produto, indice) => `
                    <div class="d-flex justify-content-between
                                align-items-center mb-2">

                        <span>
                            <strong>${indice + 1}º</strong>
                            ${produto.nome}
                        </span>

                        <span class="badge bg-primary">
                            ${produto.quantidade} vendido(s)
                        </span>

                    </div>
                `)
            .join("");
}
function exibirEstoqueCritico(produtos) {
    const elemento = document.getElementById("estoqueCritico");
    if (!elemento) {
        return;
    }
    const produtosCriticos = produtos.filter((produto) => produto.estoque <= 10);
    if (produtosCriticos.length === 0) {
        elemento.innerHTML =
            "<p class='text-muted mb-0'>" +
                "Nenhum produto com estoque crítico." +
                "</p>";
        return;
    }
    elemento.innerHTML =
        produtosCriticos
            .map((produto) => `
                    <div class="d-flex justify-content-between
                                align-items-center mb-2">

                        <span>
                            ${produto.produto}
                        </span>

                        <span class="badge bg-danger">
                            ${produto.estoque} unidade(s)
                        </span>

                    </div>
                `)
            .join("");
}
async function buscarProdutos() {
    try {
        const resposta = await fetch("api/produtos.php");
        if (!resposta.ok) {
            throw new Error("Erro ao carregar os produtos.");
        }
        const dados = await resposta.json();
        if (!ehListaProdutos(dados)) {
            throw new Error("Formato de dados inválido.");
        }
        const produtos = dados;
        const mensagem = document.getElementById("mensagemDashboard");
        /*
         * BANCO VAZIO
         */
        if (produtos.length === 0) {
            if (mensagem) {
                mensagem.textContent =
                    "Nenhum dado registrado.";
                mensagem.classList.remove("d-none");
            }
            atualizarTexto("faturamentoTotal", "R$ 0,00");
            atualizarTexto("quantidadeTotal", "0");
            atualizarTexto("totalProdutos", "0");
            atualizarTexto("estoqueTotal", "0");
            function exibirRanking(produtos) {
                const elemento = document.getElementById("rankingProdutos");
                if (!elemento) {
                    return;
                }
                const ranking = produtos
                    .filter((produto) => produto.quantidade_vendida > 0)
                    .map((produto) => ({
                    nome: produto.produto,
                    quantidade: produto.quantidade_vendida
                }))
                    .sort((a, b) => b.quantidade - a.quantidade)
                    .slice(0, 3);
                elemento.replaceChildren();
                if (ranking.length === 0) {
                    const mensagem = document.createElement("p");
                    mensagem.className =
                        "text-muted mb-0";
                    mensagem.textContent =
                        "Nenhuma venda registrada.";
                    elemento.appendChild(mensagem);
                    return;
                }
                ranking.forEach((produto, indice) => {
                    const linha = document.createElement("div");
                    linha.className =
                        "d-flex justify-content-between " +
                            "align-items-center mb-2";
                    const nome = document.createElement("span");
                    const destaque = document.createElement("strong");
                    destaque.textContent =
                        `${indice + 1}º`;
                    nome.appendChild(destaque);
                    nome.appendChild(document.createTextNode(` ${produto.nome}`));
                    const badge = document.createElement("span");
                    badge.className =
                        "badge bg-primary";
                    badge.textContent =
                        `${produto.quantidade} vendido(s)`;
                    linha.appendChild(nome);
                    linha.appendChild(badge);
                    elemento.appendChild(linha);
                });
            }
            function exibirEstoqueCritico(produtos) {
                const elemento = document.getElementById("estoqueCritico");
                if (!elemento) {
                    return;
                }
                const produtosCriticos = produtos.filter((produto) => produto.estoque <= 10);
                elemento.replaceChildren();
                if (produtosCriticos.length === 0) {
                    const mensagem = document.createElement("p");
                    mensagem.className =
                        "text-muted mb-0";
                    mensagem.textContent =
                        "Nenhum produto com estoque crítico.";
                    elemento.appendChild(mensagem);
                    return;
                }
                produtosCriticos.forEach((produto) => {
                    const linha = document.createElement("div");
                    linha.className =
                        "d-flex justify-content-between " +
                            "align-items-center mb-2";
                    const nome = document.createElement("span");
                    nome.textContent =
                        produto.produto;
                    const badge = document.createElement("span");
                    badge.className =
                        "badge bg-danger";
                    badge.textContent =
                        `${produto.estoque} unidade(s)`;
                    linha.appendChild(nome);
                    linha.appendChild(badge);
                    elemento.appendChild(linha);
                });
            }
            return;
        }
        if (mensagem) {
            mensagem.classList.add("d-none");
        }
        /*
         * REDUCE
         *
         * Faturamento total
         */
        const faturamentoTotal = produtos.reduce((total, produto) => {
            return total +
                produto.faturamento;
        }, 0);
        /*
         * REDUCE
         *
         * Quantidade vendida
         */
        const quantidadeTotal = produtos.reduce((total, produto) => {
            return total +
                produto.quantidade_vendida;
        }, 0);
        /*
         * REDUCE
         *
         * Estoque total
         */
        const estoqueTotal = produtos.reduce((total, produto) => {
            return total +
                produto.estoque;
        }, 0);
        /*
         * REDUCE
         *
         * Quantidade de produtos
         */
        const totalProdutos = produtos.reduce((total) => {
            return total + 1;
        }, 0);
        /*
         * ATUALIZAÇÃO DOS CARDS
         */
        atualizarTexto("faturamentoTotal", formatarMoeda(faturamentoTotal));
        atualizarTexto("quantidadeTotal", quantidadeTotal.toString());
        atualizarTexto("totalProdutos", totalProdutos.toString());
        atualizarTexto("estoqueTotal", estoqueTotal.toString());
        /*
         * FILTER + MAP + SORT
         *
         * Ranking de produtos
         */
        exibirRanking(produtos);
        /*
         * FILTER
         *
         * Estoque crítico
         */
        exibirEstoqueCritico(produtos);
    }
    catch (erro) {
        console.error("Não foi possível carregar os produtos.", erro);
        const mensagem = document.getElementById("mensagemDashboard");
        if (mensagem) {
            mensagem.textContent =
                "Não foi possível carregar " +
                    "os dados da dashboard.";
            mensagem.classList.remove("d-none");
            mensagem.classList.remove("alert-info");
            mensagem.classList.add("alert-danger");
        }
    }
}
buscarProdutos();
export {};
