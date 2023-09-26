<?php
session_start();

include_once './conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
$data_start = str_replace('/', '-', $dados['start']);
$data_start_conv = date("Y-m-d H:i:s", strtotime($data_start));

$data_end = str_replace('/', '-', $dados['end']);
$data_end_conv = date("Y-m-d H:i:s", strtotime($data_end));

$arquivo = $_FILES['foto'];

$query_event = "INSERT INTO events (title, color, start, end, foto, atracao, texto, preco) VALUES (:title, :color, :start, :end, :foto, :atracao, :texto, :preco)";

$insert_event = $conn->prepare($query_event);
$insert_event->bindParam(':title', $dados['title']);
$insert_event->bindParam(':color', $dados['color']);
$insert_event->bindParam(':start', $data_start_conv);
$insert_event->bindParam(':end', $data_end_conv);
$insert_event->bindParam(':foto', $arquivo['name'], PDO::PARAM_STR);
$insert_event->bindParam(':atracao', $dados['atracao']);
$insert_event->bindParam(':texto', $dados['texto']);
$insert_event->bindParam(':preco', $dados['preco']);

if ($insert_event->execute()) {
	
	if ((isset($arquivo['name'])) and (!empty($arquivo['name']))) {
                // Recuperar ultimo ID inserido no banco de dados
                $ultimo_id = $conn->lastInsertId();

                // Diretorio onde o arquivo sera salvo
                $diretorio = "imagens/$ultimo_id/";

                // Criar o diretorio
                mkdir($diretorio, 0755);

                // Upload do arquivo
                $nome_arquivo = $arquivo['name'];
                move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo);
	}
	
    $retorna = ['sit' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
    $_SESSION['msg'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
} else {
    $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi cadastrado com sucesso!</div>'];
}


header('Content-Type: application/json');
echo json_encode($retorna);
