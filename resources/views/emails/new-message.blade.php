<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .message-content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .sender-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .sender-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }
        .sender-details h3 {
            margin: 0;
            color: #007bff;
        }
        .sender-details p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .message-text {
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .cta-button:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .stats {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stats h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .stats p {
            margin: 5px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Nouveau message reçu</h1>
            <p>Vous avez reçu un nouveau message sur la plateforme de tutorat</p>
        </div>

        <div class="sender-info">
            <div class="sender-avatar">
                {{ strtoupper(substr($sender->name, 0, 1)) }}
            </div>
            <div class="sender-details">
                <h3>{{ $sender->name }}</h3>
                <p>
                    @if($sender->isTutor())
                        <strong>Tuteur</strong> - {{ $sender->getSkillsString() }}
                    @else
                        <strong>Étudiant</strong> - Niveau {{ $sender->level }}
                    @endif
                </p>
            </div>
        </div>

        <div class="message-content">
            <div class="message-text">
                @if($message->type === 'text')
                    {!! nl2br(e($message->content)) !!}
                @elseif($message->type === 'image')
                    <p><strong>📷 Image partagée</strong></p>
                    @if($message->content)
                        <p>{!! nl2br(e($message->content)) !!}</p>
                    @endif
                @elseif($message->type === 'file')
                    <p><strong>📎 Fichier partagé</strong></p>
                    <p>Fichier : {{ basename($message->file_path) }}</p>
                    @if($message->content)
                        <p>{!! nl2br(e($message->content)) !!}</p>
                    @endif
                @endif
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/messages/' . $sender->id) }}" class="cta-button">
                📱 Répondre au message
            </a>
        </div>

        <div class="stats">
            <h4>📊 Vos statistiques de messagerie</h4>
            <p>Messages non lus : <strong>{{ $receiver->getUnreadMessagesCount() }}</strong></p>
            <p>Conversations actives : <strong>{{ $receiver->sentMessages->merge($receiver->receivedMessages)->groupBy(function($m) use ($receiver) { return $m->sender_id === $receiver->id ? $m->receiver_id : $m->sender_id; })->count() }}</strong></p>
        </div>

        <div class="footer">
            <p>
                <strong>Plateforme de Tutorat</strong><br>
                Cet email a été envoyé automatiquement suite à la réception d'un nouveau message.<br>
                Vous pouvez désactiver les notifications email dans vos paramètres de profil.
            </p>
            <p>
                <small>
                    Message reçu le {{ $message->created_at->format('d/m/Y à H:i') }}<br>
                    Si vous ne souhaitez plus recevoir ces notifications, 
                    <a href="{{ url('/profile') }}">modifiez vos préférences</a>.
                </small>
            </p>
        </div>
    </div>
</body>
</html> 