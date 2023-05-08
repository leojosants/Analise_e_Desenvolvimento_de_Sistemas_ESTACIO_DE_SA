<?php
//  Conectar ao BD (com o PHP)
require_once 'conectaBD.php';

//  Definir o BD (e a tabela)

session_start();

if (empty($_SESSION)) {
    // Significa que as variáveis de SESSÂO não foram definidas, este bloco não poderá ser acessado
    header("Location: index.php?msgErro=Você precisa se autenticar no sistema.");
    die();
}

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// die();


if (!empty($_POST)) {
    //  Está chegando dados por POST e então posso tentar inserir no BD
    //  Obter as informações do formulário ($_POST)

    //  Verificar se estou tentando Inserir (CAD) / Alterar (ALT) / Excluir (DEL)
    if ($_POST["enviarDados"] == "CAD") {   //  Inserir
        try {
            //  Preparar as informações //
            //  - Montar a SQL (pgsql)
            $sql = "INSERT INTO anuncio (fase, tipo, porte, sexo, pelagem_cor, raca, observacao, email_usuario)
                VALUES (:fase, :tipo, :porte, :sexo, :pelagem_cor, :raca, :observacao, :email_usuario)";

            //  - Preparar a SQL (pdo)
            $stmt = $pdo->prepare($sql);

            //  - Definir/organizar os dados para SQL
            $dados = array(
                ':fase' => $_POST['fase'],
                ':tipo' => $_POST['tipo'],
                ':porte' => $_POST['porte'],
                ':sexo' => $_POST['sexo'],
                ':pelagem_cor' => $_POST['pelagemCor'],
                ':raca' => $_POST['raca'],
                ':observacao' => $_POST['observacao'],
                ':email_usuario' => $_SESSION['email']
            );

            //  - Tentar executar a SQL (INSERT)
            //  Realizar a inserção das informações no BD (com o PHP)
            if ($stmt->execute($dados)) {
                header("Location: index_logado.php?msgSucesso=Anúncio cadastrado com sucesso!");
            }
        } catch (PDOException $e) {
            die($e->getMessage());
            header("Location: index_logado.php?msgErro=Falha ao cadastrar anúncio...");
        }
    } elseif ($_POST["enviarDados"] == "ALT") { //  Alterar
        try {
            //  Construir SQL para Update
            $sql = "UPDATE anuncio 
                    SET 
                        fase = :fase, 
                        tipo = :tipo, 
                        porte = :porte, 
                        pelagem_cor = :pelagem_cor, 
                        raca = :raca, 
                        sexo = :sexo, 
                        observacao = :observacao
                    WHERE id = :id_anuncio
                    AND email_usuario = :email";

            //  Definir dados para SQL
            $dados = array(
                ":id_anuncio" => $_POST["id_anuncio"],
                ":fase" => $_POST["fase"],
                ":tipo" => $_POST["tipo"],
                ":porte" => $_POST["porte"],
                ":pelagem_cor" => $_POST["pelagemCor"],
                ":raca" => $_POST["raca"],
                ":sexo" => $_POST["sexo"],
                ":observacao" => $_POST["observacao"],
                ":email" => $_SESSION["email"]
            );

            $stmt = $pdo->prepare($sql);

            //  Executar SQL
            if ($stmt->execute($dados)) {
                header("Location: index_logado.php?msgSucesso=Alteração realizada com sucesso!");
            } else {
                header("Location: index_logado.php?msgErro=Falha ao ALTERAR anúncio...");
            }
        } catch (PDOException $e) {
            // die($e->getMessage());
            header("Location: index_logado.php?msgErro=Falha ao ALTERAR anúncio...");
        }
    } elseif ($_POST["enviarDados"] == "DEL") { //  Excluir
        //  id_anuncio ok
        //  e-mail usuário logado
        try {
            $sql = "DELETE FROM anuncio
                    WHERE id = :id_anuncio
                    AND email_usuario = :email";

            $stmt = $pdo->prepare($sql);

            $dados = array(
                ":id_anuncio" => $_POST["id_anuncio"],
                ":email" => $_SESSION["email"]
            );

            if ($stmt->execute($dados)) {
                header("Location: index_logado.php?msgSucesso=Anúncio excluído com sucesso!");
            } else {
                header("Location: index_logado.php?msgSucesso=Falha ao EXCLUIR anúncio!");
                // header("Location: index_logado.php?msgErro=Falha ao EXCLUIR anúncio!");
            }
        } catch (PDOException $e) {
            // die($e->getMessage());
            header("Location: index_logado.php?msgSucesso=Falha ao EXCLUIR anúncio!");
            // header("Location: index_logado.php?msgErro=Falha ao EXCLUIR anúncio!");
        }
    } else {
        header("Location: index_logado.php?msgErro=Erro de acesso (Operação não definida).");
    }
} else {
    header("Location: index_logado.php?msgErro=Erro de acesso.");
}

die();

//  Redirecionar para a página Inicial (index_logado) com mensagem de erro/sucesso