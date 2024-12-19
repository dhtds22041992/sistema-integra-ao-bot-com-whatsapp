<?php

// Arquivo webhook.php

// DeDesenvolvimento de solução de Bot whatsappfina a chave de verificação do WhatsApp (para validação)
$whatsapp_token = 'SUA_CHAVE_DE_VERIFICACAO';

// Verifique o token de validação (caso o webhook precise ser verificado pelo WhatsApp)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_challenge'])) {
    if ($_GET['hub_verify_token'] === $whatsapp_token) {
        echo $_GET['hub_challenge'];
        exit;
    } else {
        echo 'Token inválido!';
        exit;
    }
}

// Processando as mensagens recebidas do WhatsApp
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Verifique se há mensagens
    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];

        // Extraia o número do remetente e a mensagem
        $from = $message['from'];
        $text = $message['text']['body'];

        // Resposta simples de boas-vindas
        if ($text == 'Olá') {
            sendMessage($from, 'Olá! Como posso ajudá-lo?');
        }

        // Se a mensagem for solicitando um atendimento humano
        elseif (strpos(strtolower($text), 'atendimento') !== false) {
            handleHumanSupport($from);
        }
        
        // Caso contrário, passe para a IA para uma resposta mais inteligente
        else {
            $response = getAiResponse($text);
            sendMessage($from, $response);
        }
    }
}

// Função para enviar mensagem via API do WhatsApp
function sendMessage($to, $message) {
    $url = 'https://graph.facebook.com/v14.0/SEU_NUMERO_DE_WHATSAPP/messages';
    
    $data = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'text' => ['body' => $message]
    ];

    $headers = [
        'Authorization: Bearer SEU_TOKEN_DE_ACESSO',
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// Função para integrar com uma IA (Dialogflow por exemplo)
function getAiResponse($userInput) {
    // URL da API do Dialogflow
    $url = 'https://api.dialogflow.com/v1/query?v=20150910';
    
    $headers = [
        'Authorization: Bearer SEU_TOKEN_DIALOGFLOW',
        'Content-Type: application/json'
    ];

    $data = [
        'query' => $userInpu
