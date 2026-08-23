async function buscarProdutos(): Promise<void> {

    try {

        const resposta = await fetch("api/produtos.php");

        if (!resposta.ok) {
            throw new Error("Erro ao carregar os produtos.");
        }

        const produtos = await resposta.json();

        const mensagem = document.getElementById("mensagemDashboard");

        if (!Array.isArray(produtos) || produtos.length === 0) {

            if (mensagem) {
                mensagem.textContent = "Nenhum dado registrado.";
                mensagem.classList.remove("d-none");
            }

            document.getElementById("faturamentoTotal")!.textContent = "R$ 0,00";
            document.getElementById("quantidadeTotal")!.textContent = "0";
            document.getElementById("totalProdutos")!.textContent = "0";
            document.getElementById("estoqueTotal")!.textContent = "0";

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
                return total + Number(produto.quantidade_vendida || 0);
            },
            0
        );

        const estoqueTotal = produtos.reduce(
            (total: number, produto: any) => {
                return total + Number(produto.estoque || 0);
            },
            0
        );

        const totalProdutos = produtos.reduce(
            (total: number) => {
                return total + 1;
            },
            0
        );

        document.getElementById("faturamentoTotal")!.textContent =
            faturamentoTotal.toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });

        document.getElementById("quantidadeTotal")!.textContent =
            quantidadeTotal.toString();

        document.getElementById("totalProdutos")!.textContent =
            totalProdutos.toString();

        document.getElementById("estoqueTotal")!.textContent =
            estoqueTotal.toString();

    } catch (erro) {

        console.error(
            "Não foi possível carregar os produtos.",
            erro
        );

        const mensagem = document.getElementById("mensagemDashboard");

        if (mensagem) {
            mensagem.textContent =
                "Não foi possível carregar os dados da dashboard.";
            mensagem.classList.remove("d-none");
            mensagem.classList.remove("alert-info");
            mensagem.classList.add("alert-danger");
        }
    }
}

buscarProdutos();

export {};