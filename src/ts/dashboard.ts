interface ProdutoAnalitico {
    id: number;
    produto: string;
    categoria: string;
    preco: number;
    estoque: number;
    quantidade_vendida: number;
    faturamento: number;
}

interface ProdutoRanking {
    nome: string;
    quantidade: number;
}

/*
 * VALIDAÇÃO DO PRODUTO
 */
function ehProdutoAnalitico(
    valor: unknown
): valor is ProdutoAnalitico {

    if (
        typeof valor !== "object" ||
        valor === null
    ) {
        return false;
    }

    const produto =
        valor as Record<string, unknown>;

    return (
        typeof produto.id === "number" &&
        typeof produto.produto === "string" &&
        typeof produto.categoria === "string" &&
        typeof produto.preco === "number" &&
        typeof produto.estoque === "number" &&
        typeof produto.quantidade_vendida === "number" &&
        typeof produto.faturamento === "number"
    );
}

/*
 * VALIDAÇÃO DA LISTA DE PRODUTOS
 */
function ehListaProdutos(
    valor: unknown
): valor is ProdutoAnalitico[] {

    return (
        Array.isArray(valor) &&
        valor.every(ehProdutoAnalitico)
    );
}

/*
 * ATUALIZA TEXTO DE UM ELEMENTO
 */
function atualizarTexto(
    id: string,
    texto: string
): void {

    const elemento =
        document.getElementById(id);

    if (elemento) {
        elemento.textContent = texto;
    }
}

/*
 * FORMATA VALORES EM REAL
 */
function formatarMoeda(
    valor: number
): string {

    return valor.toLocaleString(
        "pt-BR",
        {
            style: "currency",
            currency: "BRL"
        }
    );
}

/*
 * EXIBE RANKING DOS PRODUTOS
 *
 * FILTER + MAP + SORT + SLICE
 */
function exibirRanking(
    produtos: ProdutoAnalitico[]
): void {

    const elemento =
        document.getElementById("rankingProdutos");

    if (!elemento) {
        return;
    }

    const ranking: ProdutoRanking[] =
        produtos
            .filter(
                (produto) =>
                    produto.quantidade_vendida > 0
            )
            .map(
                (produto) => ({
                    nome: produto.produto,
                    quantidade:
                        produto.quantidade_vendida
                })
            )
            .sort(
                (a, b) =>
                    b.quantidade - a.quantidade
            )
            .slice(0, 3);

    elemento.replaceChildren();

    if (ranking.length === 0) {

        const mensagem =
            document.createElement("p");

        mensagem.className =
            "text-muted mb-0";

        mensagem.textContent =
            "Nenhuma venda registrada.";

        elemento.appendChild(mensagem);

        return;
    }

    ranking.forEach(
        (produto, indice) => {

            const linha =
                document.createElement("div");

            linha.className =
                "d-flex justify-content-between " +
                "align-items-center mb-2";

            const nome =
                document.createElement("span");

            const destaque =
                document.createElement("strong");

            destaque.textContent =
                `${indice + 1}º`;

            nome.appendChild(destaque);

            nome.appendChild(
                document.createTextNode(
                    ` ${produto.nome}`
                )
            );

            const badge =
                document.createElement("span");

            badge.className =
                "badge bg-primary";

            badge.textContent =
                `${produto.quantidade} vendido(s)`;

            linha.appendChild(nome);
            linha.appendChild(badge);

            elemento.appendChild(linha);
        }
    );
}

/*
 * EXIBE PRODUTOS COM ESTOQUE CRÍTICO
 *
 * FILTER
 */
function exibirEstoqueCritico(
    produtos: ProdutoAnalitico[]
): void {

    const elemento =
        document.getElementById("estoqueCritico");

    if (!elemento) {
        return;
    }

    const produtosCriticos =
        produtos.filter(
            (produto) =>
                produto.estoque <= 10
        );

    elemento.replaceChildren();

    if (produtosCriticos.length === 0) {

        const mensagem =
            document.createElement("p");

        mensagem.className =
            "text-muted mb-0";

        mensagem.textContent =
            "Nenhum produto com estoque crítico.";

        elemento.appendChild(mensagem);

        return;
    }

    produtosCriticos.forEach(
        (produto) => {

            const linha =
                document.createElement("div");

            linha.className =
                "d-flex justify-content-between " +
                "align-items-center mb-2";

            const nome =
                document.createElement("span");

            nome.textContent =
                produto.produto;

            const badge =
                document.createElement("span");

            badge.className =
                "badge bg-danger";

            badge.textContent =
                `${produto.estoque} unidade(s)`;

            linha.appendChild(nome);
            linha.appendChild(badge);

            elemento.appendChild(linha);
        }
    );
}

/*
 * ATUALIZA CARDS COM BANCO VAZIO
 */
function limparDashboard(): void {

    atualizarTexto(
        "faturamentoTotal",
        "R$ 0,00"
    );

    atualizarTexto(
        "quantidadeTotal",
        "0"
    );

    atualizarTexto(
        "totalProdutos",
        "0"
    );

    atualizarTexto(
        "estoqueTotal",
        "0"
    );

    exibirRanking([]);

    exibirEstoqueCritico([]);
}

/*
 * BUSCA OS PRODUTOS NA API
 *
 * FETCH + ASYNC/AWAIT + TRY/CATCH
 */
async function buscarProdutos(): Promise<void> {

    try {

        /*
         * O painel é um dashboard geral.
         *
         * Por isso não usamos paginação aqui.
         * Buscamos até 100 produtos para que os
         * indicadores representem o conjunto do catálogo.
         */
        const resposta = await fetch(
            "api/produtos.php?limite=100"
        );

        if (!resposta.ok) {

            throw new Error(
                "Erro ao carregar os produtos."
            );
        }

        const dados: unknown =
            await resposta.json();

        if (!ehListaProdutos(dados)) {

            throw new Error(
                "Formato de dados inválido."
            );
        }

        const mensagem =
            document.getElementById(
                "mensagemDashboard"
            );

        /*
         * BANCO VAZIO
         */
        if (dados.length === 0) {

            if (mensagem) {

                mensagem.textContent =
                    "Nenhum dado registrado.";

                mensagem.classList.remove(
                    "d-none"
                );

                mensagem.classList.remove(
                    "alert-danger"
                );

                mensagem.classList.add(
                    "alert-info"
                );
            }

            limparDashboard();

            return;
        }

        /*
         * ESCONDE MENSAGEM DE BANCO VAZIO
         */
        if (mensagem) {

            mensagem.classList.add(
                "d-none"
            );
        }

        /*
         * REDUCE
         *
         * Faturamento total
         */
        const faturamentoTotal =
            dados.reduce(
                (
                    total: number,
                    produto: ProdutoAnalitico
                ): number => {

                    return total +
                        produto.faturamento;
                },
                0
            );

        /*
         * REDUCE
         *
         * Quantidade vendida
         */
        const quantidadeTotal =
            dados.reduce(
                (
                    total: number,
                    produto: ProdutoAnalitico
                ): number => {

                    return total +
                        produto.quantidade_vendida;
                },
                0
            );

        /*
         * REDUCE
         *
         * Estoque total
         */
        const estoqueTotal =
            dados.reduce(
                (
                    total: number,
                    produto: ProdutoAnalitico
                ): number => {

                    return total +
                        produto.estoque;
                },
                0
            );

        /*
         * REDUCE
         *
         * Quantidade de produtos
         */
        const totalProdutos =
            dados.reduce(
                (
                    total: number
                ): number => {

                    return total + 1;
                },
                0
            );

        /*
         * ATUALIZAÇÃO DOS CARDS
         */
        atualizarTexto(
            "faturamentoTotal",
            formatarMoeda(
                faturamentoTotal
            )
        );

        atualizarTexto(
            "quantidadeTotal",
            quantidadeTotal.toString()
        );

        atualizarTexto(
            "totalProdutos",
            totalProdutos.toString()
        );

        atualizarTexto(
            "estoqueTotal",
            estoqueTotal.toString()
        );

        /*
         * FILTER + MAP + SORT
         *
         * Ranking dos produtos
         */
        exibirRanking(dados);

        /*
         * FILTER
         *
         * Estoque crítico
         */
        exibirEstoqueCritico(dados);

    } catch (erro: unknown) {

        console.error(
            "Não foi possível carregar os produtos.",
            erro
        );

        const mensagem =
            document.getElementById(
                "mensagemDashboard"
            );

        if (mensagem) {

            mensagem.textContent =
                "Não foi possível carregar " +
                "os dados da dashboard.";

            mensagem.classList.remove(
                "d-none"
            );

            mensagem.classList.remove(
                "alert-info"
            );

            mensagem.classList.add(
                "alert-danger"
            );
        }
    }
}

/*
 * INICIALIZAÇÃO DA DASHBOARD
 */
void buscarProdutos();

export {};