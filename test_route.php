<?php

// Test simple de la route du chatbot
$url = 'http://127.0.0.1:8000/faqs/chatbot/ask';

$data = [
    'question' => 'Comment fonctionne le système de paiement ?'
];

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);

echo "🧪 Test de la route chatbot\n";
echo "URL: $url\n";
echo "Data: " . json_encode($data) . "\n\n";

try {
    $result = file_get_contents($url, false, $context);
    echo "✅ Réponse reçue:\n";
    echo $result . "\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
} 