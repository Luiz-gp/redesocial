
// Endereço da API
const API = "back/api.php";

// Variáveis do sistema
let usuarioLogado = null;
let publicacoes = [];
let curtidas = [];

// Elementos do HTML
const telaLogin = document.getElementById("telaLogin");
const aplicacao = document.getElementById("aplicacao");

const formLogin = document.getElementById("formLogin");
const formCadastro = document.getElementById("formCadastro");
const formPublicacao = document.getElementById("formPublicacao");

const listaPublicacoes = document.getElementById("listaPublicacoes");


// ==================================================
// REQUISIÇÕES PARA A API
// ==================================================

async function requisicao(rota, metodo = "GET", dados = null) {

    try {
        const configuracao = {
            method: metodo,
            headers: {
                "Content-Type": "application/json"
            }
        };

        if (dados !== null) {
            configuracao.body = JSON.stringify(dados);
        }

        const resposta = await fetch(`${API}?rota=${rota}`, configuracao);

        const texto = await resposta.text();

        // Verifica se o PHP retornou alguma coisa
        if (!texto.trim()) {
            throw new Error(
                `Resposta vazia na rota "${rota}", método "${metodo}". Status HTTP: ${resposta.status}`
            );
        }

        let resultado;

        try {
            resultado = JSON.parse(texto);
        } catch (erro) {
            console.error("Resposta recebida do PHP:", texto);

            throw new Error(
                `Resposta inválida do PHP na rota "${rota}". Verifique o Console.`
            );
        }

        // Verifica erros HTTP
        if (!resposta.ok) {
            throw new Error(
                resultado.detalhe
                    ? (resultado.erro || resultado.mensagem || "Erro na requisição.") + "\n" + resultado.detalhe
                    : resultado.erro || resultado.mensagem || "Erro na requisição."
            );
        }

        // Verifica erros retornados pela API
        if (resultado.status === "erro") {
            throw new Error(resultado.mensagem || "Ocorreu um erro na API.");
        }

        return resultado;

    } catch (erro) {
        console.error(`Erro na rota ${rota}:`, erro);
        throw erro;
    }
}


// ==================================================
// MENSAGENS
// ==================================================

function mostrarMensagem(mensagem) {
    alert(mensagem);
}


// ==================================================
// LOGIN
// ==================================================

formLogin.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const identificador = document.getElementById("loginUsuario").value.trim();
    const senha = document.getElementById("loginSenha").value;

    try {

        const resultado = await requisicao(
            "login",
            "POST",
            {
                identificador: identificador,
                senha: senha
            }
        );

        usuarioLogado = resultado.usuario;

        localStorage.setItem(
            "usuarioInstagram",
            JSON.stringify(usuarioLogado)
        );

        entrarAplicacao();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});


// ==================================================
// CADASTRO
// ==================================================

formCadastro.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const nome = document.getElementById("cadastroNome").value.trim();
    const username = document.getElementById("cadastroUsername").value.trim();
    const email = document.getElementById("cadastroEmail").value.trim();
    const senha = document.getElementById("cadastroSenha").value;

    try {

        // Cadastra o usuário
        await requisicao(
            "usuario",
            "POST",
            {
                nome: nome,
                username: username,
                email: email,
                senha: senha
            }
        );

        mostrarMensagem("Cadastro realizado com sucesso!");

        // Realiza o login após cadastrar
        const resultadoLogin = await requisicao(
            "login",
            "POST",
            {
                identificador: username,
                senha: senha
            }
        );

        usuarioLogado = resultadoLogin.usuario;

        localStorage.setItem(
            "usuarioInstagram",
            JSON.stringify(usuarioLogado)
        );

        entrarAplicacao();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});


// ==================================================
// TROCAR LOGIN E CADASTRO
// ==================================================

const abrirCadastro = document.getElementById("abrirCadastro");
const voltarLogin = document.getElementById("voltarLogin");

if (abrirCadastro) {

    abrirCadastro.addEventListener("click", function (evento) {

        evento.preventDefault();

        formLogin.classList.add("oculto");
        formCadastro.classList.remove("oculto");
    });
}

if (voltarLogin) {

    voltarLogin.addEventListener("click", function (evento) {

        evento.preventDefault();

        formCadastro.classList.add("oculto");
        formLogin.classList.remove("oculto");
    });
}


// ==================================================
// ENTRAR NA APLICAÇÃO
// ==================================================

async function entrarAplicacao() {

    telaLogin.classList.add("oculto");
    aplicacao.classList.remove("oculto");

    atualizarInformacoesUsuario();

    await carregarDados();
}


// ==================================================
// INFORMAÇÕES DO USUÁRIO
// ==================================================

function atualizarInformacoesUsuario() {

    const nomeMenu = document.getElementById("nomeUsuarioMenu");
    const usernameMenu = document.getElementById("usernameMenu");

    if (nomeMenu) {
        nomeMenu.textContent = usuarioLogado.nome;
    }

    if (usernameMenu) {
        usernameMenu.textContent = "@" + usuarioLogado.username;
    }
}


// ==================================================
// CARREGAR PUBLICAÇÕES E CURTIDAS
// ==================================================

async function carregarDados() {

    try {

        publicacoes = await requisicao("publicacao");
        curtidas = await requisicao("curtida");

        renderizarPublicacoes();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}


// ==================================================
// EXIBIR PUBLICAÇÕES
// ==================================================

function renderizarPublicacoes() {

    listaPublicacoes.innerHTML = "";

    if (publicacoes.length === 0) {

        listaPublicacoes.innerHTML = `
            <p class="feed-vazio">
                Ainda não há publicações.
            </p>
        `;

        return;
    }

    publicacoes.forEach(function (publicacao) {

        const curtidaExiste = curtidas.some(function (curtida) {

            return (
                Number(curtida.id_usuario) === Number(usuarioLogado.id_usuario) &&
                Number(curtida.id_publicacao) === Number(publicacao.id_publicacao)
            );
        });

        const souDono =
            Number(publicacao.id_usuario) === Number(usuarioLogado.id_usuario);

        const elemento = document.createElement("article");

        elemento.classList.add("publicacao");

        elemento.innerHTML = `
            <div class="publicacao-cabecalho">

                <div class="publicacao-usuario">
                    <div class="avatar">
                        ${publicacao.nome.charAt(0).toUpperCase()}
                    </div>

                    <div>
                        <strong>${escaparHTML(publicacao.nome)}</strong>
                        <small>@${escaparHTML(publicacao.username)}</small>
                    </div>
                </div>

                ${
                    souDono
                    ? `
                        <div class="acoes-publicacao">
                            <button class="editar-publicacao">
                                Editar
                            </button>

                            <button class="excluir-publicacao">
                                Excluir
                            </button>
                        </div>
                    `
                    : ""
                }

            </div>

            <img
                class="imagem-post"
                src="${escaparHTML(publicacao.imagem_url)}"
                alt="Imagem da publicação"
                onerror="this.style.display='none'"
            >

            <div class="publicacao-conteudo">

                <button class="botao-curtir">
                    ${curtidaExiste ? "❤️" : "🤍"}
                    ${curtidaExiste ? "Curtido" : "Curtir"}
                </button>

                <p class="legenda">
                    <strong>${escaparHTML(publicacao.username)}</strong>
                    ${escaparHTML(publicacao.legenda)}
                </p>

                <small class="data-publicacao">
                    ${escaparHTML(publicacao.data_publicacao || "")}
                </small>

            </div>
        `;

        // Botão de curtir
        const botaoCurtir = elemento.querySelector(".botao-curtir");

        botaoCurtir.addEventListener("click", function () {
            alternarCurtida(publicacao.id_publicacao);
        });


        // Botão de editar
        const botaoEditar = elemento.querySelector(".editar-publicacao");

        if (botaoEditar) {

            botaoEditar.addEventListener("click", function () {
                editarPublicacao(publicacao);
            });
        }


        // Botão de excluir
        const botaoExcluir = elemento.querySelector(".excluir-publicacao");

        if (botaoExcluir) {

            botaoExcluir.addEventListener("click", function () {
                excluirPublicacao(publicacao.id_publicacao);
            });
        }

        listaPublicacoes.appendChild(elemento);
    });
}


// ==================================================
// CRIAR PUBLICAÇÃO
// ==================================================

formPublicacao.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const legenda = document.getElementById("legendaPublicacao").value.trim();
    const imagem = document.getElementById("imagemPublicacao").value.trim();

    if (legenda === "" || imagem === "") {
        mostrarMensagem("Preencha a legenda e o link da imagem.");
        return;
    }

    try {

        await requisicao(
            "publicacao",
            "POST",
            {
                id_usuario: usuarioLogado.id_usuario,
                legenda: legenda,
                imagem_url: imagem
            }
        );

        formPublicacao.reset();

        mostrarMensagem("Publicação criada!");

        await carregarDados();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
});


// ==================================================
// EDITAR PUBLICAÇÃO
// ==================================================

async function editarPublicacao(publicacao) {

    const novaLegenda = prompt(
        "Digite a nova legenda:",
        publicacao.legenda
    );

    if (novaLegenda === null) {
        return;
    }

    const novaImagem = prompt(
        "Digite o novo link da imagem:",
        publicacao.imagem_url
    );

    if (novaImagem === null) {
        return;
    }

    try {

        await requisicao(
            `publicacao&id=${publicacao.id_publicacao}`,
            "PUT",
            {
                id_usuario: usuarioLogado.id_usuario,
                legenda: novaLegenda,
                imagem_url: novaImagem
            }
        );

        mostrarMensagem("Publicação atualizada!");

        await carregarDados();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}


// ==================================================
// EXCLUIR PUBLICAÇÃO
// ==================================================

async function excluirPublicacao(id) {

    const confirmar = confirm(
        "Deseja realmente excluir esta publicação?"
    );

    if (!confirmar) {
        return;
    }

    try {

        await requisicao(
            `publicacao&id=${id}&id_usuario=${usuarioLogado.id_usuario}`,
            "DELETE"
        );

        mostrarMensagem("Publicação excluída!");

        await carregarDados();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}


// ==================================================
// CURTIR / DESCURTIR
// ==================================================

async function alternarCurtida(idPublicacao) {

    try {

        await requisicao(
            "curtida",
            "POST",
            {
                id_usuario: usuarioLogado.id_usuario,
                id_publicacao: idPublicacao
            }
        );

        await carregarDados();

    } catch (erro) {
        mostrarMensagem(erro.message);
    }
}


// ==================================================
// SAIR
// ==================================================

const botaoSair = document.getElementById("botaoSair");

if (botaoSair) {

    botaoSair.addEventListener("click", function () {

        localStorage.removeItem("usuarioInstagram");

        usuarioLogado = null;

        aplicacao.classList.add("oculto");
        telaLogin.classList.remove("oculto");

        formLogin.reset();
        formCadastro.reset();
    });
}


// ==================================================
// MANTER LOGIN
// ==================================================

const usuarioSalvo = localStorage.getItem("usuarioInstagram");

if (usuarioSalvo) {

    try {

        usuarioLogado = JSON.parse(usuarioSalvo);

        entrarAplicacao();

    } catch (erro) {

        localStorage.removeItem("usuarioInstagram");
    }
}


// ==================================================
// PROTEÇÃO SIMPLES CONTRA HTML NA LEGENDA
// ==================================================

function escaparHTML(texto) {

    return String(texto)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
