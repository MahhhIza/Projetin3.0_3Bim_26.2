async function buscarProdutos(): Promise<void> {

    try {

        const resposta = await fetch("api/produtos.php");

        if (!resposta.ok) {
            throw new Error("Erro ao carregar os produtos.");
        }

        const produtos = await resposta.json();

        console.log(produtos);

    } catch (erro) {

        console.error(
            "Não foi possível carregar os produtos.",
            erro
        );

    }

}

buscarProdutos();

export {};