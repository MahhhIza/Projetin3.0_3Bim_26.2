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

// DW - Interfaces TypeScript para tipagem dos dados do backend
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

function ehListaProdutos(
    valor: unknown
): valor is ProdutoAnalitico[] {
    return (
        Array.isArray(valor) &&
        valor.every(ehProdutoAnalitico)
    );
}

// TF - Manipulação segura do DOM
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

// LA - Ranking e transformação de dados com filter, reduce, map, sort e slice
function exibirRanking(
    produtos: ProdutoAnalitico[]
): void {
    const elemento =
        document.getElementById("rankingProdutos");

    if (!elemento) {
        return;
    }

    const contagemProdutos: Record<string, number> =
        produtos
            .filter(
                (produto) =>
                    produto.quantidade_vendida > 0
            )
            .reduce<Record<string, number>>(
                (
                    acumulador,
                    produto
                ): Record<string, number> => {
                    const nomeProduto =
                        produto.produto;

                    if (!acumulador[nomeProduto]) {
                        acumulador[nomeProduto] = 0;
                    }

                    acumulador[nomeProduto] +=
                        produto.quantidade_vendida;

                    return acumulador;
                },
                {}
            );

    const ranking: ProdutoRanking[] =
        Object.entries(contagemProdutos)
            .map(
                ([nome, quantidade]): ProdutoRanking => ({
                    nome,
                    quantidade
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

// LA - Segmentação de produtos com filter
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

// TF - Consumo de API e fluxo assíncrono com fetch, async/await e try/catch
async function buscarProdutos(): Promise<void> {
    try {
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

        if (mensagem) {
            mensagem.classList.add(
                "d-none"
            );
        }

        // LA - Agregação de dados com reduce
        const faturamentoTotal =
            dados.reduce(
                (
                    total: number,
                    produto: ProdutoAnalitico
                ): number => {
                    return total +
                        (
                            produto.quantidade_vendida *
                            produto.preco
                        );
                },
                0
            );

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

        const totalProdutos =
            dados.reduce(
                (
                    total: number
                ): number => {
                    return total + 1;
                },
                0
            );

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

        exibirRanking(dados);
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

void buscarProdutos();

export {};