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

const PRODUTOS_POR_PAGINA = 10;
const LIMITE_BUSCA = PRODUTOS_POR_PAGINA + 1;

let paginaAtual = 1;

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

* ATUALIZA INDICADOR DA PÁGINAÇÃO
  */
  function atualizarPaginacao(): void {

  const elemento =
  document.getElementById("paginaAtual");

  if (!elemento) {
  return;
  }

  elemento.textContent =
  `Página ${paginaAtual}`;
  }

/*

* ATUALIZA ESTADO DOS BOTÕES
  */
  function atualizarEstadoPaginacao(
  existeProximaPagina: boolean
  ): void {

  const botaoAnterior =
  document.getElementById(
  "btnPaginaAnterior"
  ) as HTMLButtonElement | null;

  const botaoProxima =
  document.getElementById(
  "btnProximaPagina"
  ) as HTMLButtonElement | null;

  if (!botaoAnterior || !botaoProxima) {
  return;
  }

  botaoAnterior.disabled =
  paginaAtual === 1;

  botaoProxima.disabled =
  !existeProximaPagina;
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

   const offset =
       (paginaAtual - 1) *
       PRODUTOS_POR_PAGINA;

   /*
    * Buscamos 11 registros.
    *
    * Os primeiros 10 aparecem na página.
    * O 11º serve apenas para saber
    * se existe uma próxima página.
    */
   const resposta = await fetch(
       `api/produtos.php?limite=${LIMITE_BUSCA}&offset=${offset}`
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

   /*
    * Se o usuário avançar para uma página
    * que não existe, volta automaticamente.
    */
   if (
       dados.length === 0 &&
       paginaAtual > 1
   ) {

       paginaAtual--;

       atualizarPaginacao();

       await buscarProdutos();

       return;
   }

   /*
    * Os dados exibidos são somente
    * os 10 produtos da página.
    */
   const produtos: ProdutoAnalitico[] =
       dados.slice(
           0,
           PRODUTOS_POR_PAGINA
       );

   /*
    * Existe próxima página se a API
    * retornou o 11º registro.
    */
   const existeProximaPagina =
       dados.length >
       PRODUTOS_POR_PAGINA;

   atualizarEstadoPaginacao(
       existeProximaPagina
   );

   const mensagem =
       document.getElementById(
           "mensagemDashboard"
       );


   /*
    * BANCO VAZIO
    */
   if (produtos.length === 0) {

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
       produtos.reduce(
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
       produtos.reduce(
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
       produtos.reduce(
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
       produtos.reduce(
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
   exibirRanking(produtos);


   /*
    * FILTER
    *
    * Estoque crítico
    */
   exibirEstoqueCritico(produtos);

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

* CONFIGURA OS BOTÕES DE PAGINAÇÃO
  */
  function configurarPaginacao(): void {

  const botaoAnterior =
  document.getElementById(
  "btnPaginaAnterior"
  );

  const botaoProxima =
  document.getElementById(
  "btnProximaPagina"
  );

  if (!botaoAnterior || !botaoProxima) {
  return;
  }

  /*

  * BOTÃO ANTERIOR
    */
    botaoAnterior.addEventListener(
    "click",
    () => {

     if (paginaAtual > 1) {

         paginaAtual--;

         atualizarPaginacao();

         void buscarProdutos();
     }

    }
    );

  /*

  * BOTÃO PRÓXIMA
    */
    botaoProxima.addEventListener(
    "click",
    () => {

     const botao =
         botaoProxima as HTMLButtonElement;

     if (botao.disabled) {
         return;
     }

     paginaAtual++;

     atualizarPaginacao();

     void buscarProdutos();

    }
    );
    }

/*

* INICIALIZAÇÃO DA DASHBOARD
  */
  configurarPaginacao();

atualizarPaginacao();

void buscarProdutos();

export {};
