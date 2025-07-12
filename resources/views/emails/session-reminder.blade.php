<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de séance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .session-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .session-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .session-details {
            margin: 15px 0;
        }
        .detail-item {
            display: flex;
            align-items: center;
            margin: 8px 0;
            color: #555;
        }
        .detail-item i {
            width: 20px;
            margin-right: 10px;
            color: #667eea;
        }
        .meet-link {
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s;
        }
        .meet-link:hover {
            background-color: #218838;
        }
        .location-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .urgent {
            color: #dc3545;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Rappel de séance</h1>
            <p>Votre séance commence dans <span class="urgent">10 minutes</span></p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $participantName }}</strong>,</p>
            
            <p>Ceci est un rappel que votre séance avec <strong>{{ $otherParticipantName }}</strong> commence dans 10 minutes.</p>

            <div class="session-info">
                <div class="session-title">{{ $session->title }}</div>
                
                <div class="session-details">
                    <div class="detail-item">
                        <i>📅</i>
                        <span><strong>Date :</strong> {{ $session->scheduled_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <i>🕐</i>
                        <span><strong>Heure :</strong> {{ $session->scheduled_at->format('H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <i>⏱️</i>
                        <span><strong>Durée :</strong> {{ $session->duration_minutes }} minutes</span>
                    </div>
                    <div class="detail-item">
                        <i>📚</i>
                        <span><strong>Matière :</strong> {{ ucfirst($session->subject) }}</span>
                    </div>
                    <div class="detail-item">
                        <i>📝</i>
                        <span><strong>Niveau :</strong> {{ ucfirst($session->level) }}</span>
                    </div>
                </div>

                @if($session->type === 'online')
                    <div style="margin-top: 20px;">
                        <p><strong>🔗 Lien de la visioconférence :</strong></p>
                        <a href="{{ $meetLink }}" class="meet-link" target="_blank">
                            🎥 Rejoindre la séance en ligne
                        </a>
                        <p style="font-size: 14px; color: #666; margin-top: 10px;">
                            <strong>Code de la réunion :</strong> {{ str_replace('https://meet.google.com/', '', $meetLink) }}
                        </p>
                    </div>
                @else
                    <div class="location-info">
                        <p><strong>📍 Lieu de rendez-vous :</strong></p>
                        <p>{{ $session->location }}</p>
                        <p style="margin-top: 10px; font-size: 14px;">
                            <strong>⚠️ Important :</strong> Assurez-vous d'arriver à l'heure au lieu indiqué.
                        </p>
                    </div>
                @endif
            </div>

            <div style="margin: 30px 0; text-align: center;">
                <a href="{{ route('sessions.show', $session) }}" class="btn">
                    📋 Voir les détails de la séance
                </a>
            </div>

            <p style="color: #666; font-size: 14px;">
                <strong>Description :</strong><br>
                {{ $session->description }}
            </p>

            <div style="margin-top: 30px; padding: 20px; background-color: #e3f2fd; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #1976d2;">💡 Conseils pour une séance réussie</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Préparez vos questions et documents à l'avance</li>
                    <li>Assurez-vous d'avoir une connexion stable (séances en ligne)</li>
                    <li>Arrivez quelques minutes en avance</li>
                    <li>Préparez un environnement calme pour travailler</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Ce message a été envoyé automatiquement par Soutiens-moi!</p>
            <p>Si vous avez des questions, contactez-nous à support@soutiens-moi.fr</p>
        </div>
    </div>
</body>
</html> 