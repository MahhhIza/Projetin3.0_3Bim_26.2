async function buscarProdutos(): Promise<void> {

    const faturamentoElemento =
        document.getElementById("faturamentoTotal");

    const quantidadeElemento =
        document.getElementById("quantidadeTotal");

    const produtosElemento =
        document.getElementById("totalProdutos");

    const estoqueElemento =
        document.getElementById("estoqueTotal");

    const mensagemElemento =
        document.getElementById("mensagemDashboard");

    try {

        const resposta = await fetch("api/produtos.php");

        if (!resposta.ok) {
            throw new Error("Erro ao carregar os produtos.");
        }

        const produtos = await resposta.json();

        if (!Array.isArray(produtos) || produtos.length === 0) {

            if (mensagemElemento) {
                mensagemElemento.textContent =
                    "Nenhum dado registrado.";
                mensagemElemento.classList.remove("d-none");
            }

            if (faturamentoElemento) {
                faturamentoElemento.textContent = "R$ 0,00";
            }

            if (quantidadeElemento) {
                quantidadeElemento.textContent = "0";
            }

            if (produtosElemento) {
                produtosElemento.textContent = "0";
            }

            if (estoqueElemento) {
                estoqueElemento.textContent = "0";
            }

            return;
        }

        const faturamentoTotal = produtos.reduce(
            (total: number, produto: any) => {
                return total + Number(produto.faturamento || 0);
            },
            0
        );

        const quantidadeTotal = produtos.reduce(
            (total: number, produto: any) => {
                return total + Number(
                    produto.quantidade_vendida || 0
                );
            },
            0
        );

        const estoqueTotal = produtos.reduce(
            (total: number, produto: any) => {
                return total + Number(produto.estoque || 0);
            },
            0
        );

        if (faturamentoElemento) {
            faturamentoElemento.textContent =
                "R$ " +
                faturamentoTotal.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
        }

        if (quantidadeElemento) {
            quantidadeElemento.textContent =
                quantidadeTotal.toString();
        }

        if (produtosElemento) {
            produtosElemento.textContent =
                produtos.length.toString();
        }

        if (estoqueElemento) {
            estoqueElemento.textContent =
                estoqueTotal.toString();
        }

    } catch (erro) {

        console.error(
            "Não foi possível carregar os produtos.",
            erro
        );

        if (mensagemElemento) {
            mensagemElemento.textContent =
                "Não foi possível carregar os dados da dashboard.";
            mensagemElemento.classList.remove("d-none");
            mensagemElemento.classList.remove("alert-info");
            mensagemElemento.classList.add("alert-danger");
        }
    }
}

buscarProdutos();

export {};