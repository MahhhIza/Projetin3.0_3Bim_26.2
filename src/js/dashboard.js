async function buscarProdutos() {
    try {
        const resposta = await fetch("api/produtos.php");
        if (!resposta.ok) {
            throw new Error("Erro ao carregar os produtos.");
        }
        const produtos = await resposta.json();
        const faturamentoTotal = produtos.reduce((total, produto) => {
            return total + Number(produto.faturamento);
        }, 0);
        const quantidadeTotal = produtos.reduce((total, produto) => {
            return total + Number(produto.quantidade_vendida);
        }, 0);
        console.log("Faturamento total:", faturamentoTotal);
        console.log("Quantidade total vendida:", quantidadeTotal);
    }
    catch (erro) {
        console.error("Não foi possível carregar os produtos.", erro);
    }
}
buscarProdutos();
export {};
