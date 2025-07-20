@php
    $autre = $recipientRole === 'student' ? $session->tutor : $session->student;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle séance créée</title>
</head>
<body>
    <h2>Nouvelle séance de tutorat créée</h2>
    <p>Bonjour {{ $recipientRole === 'student' ? $session->student->name : $session->tutor->name }},</p>
    <p>Une nouvelle séance de tutorat a été créée avec les informations suivantes :</p>
    <ul>
        <li><strong>Titre :</strong> {{ $session->title }}</li>
        <li><strong>Description :</strong> {{ $session->description }}</li>
        <li><strong>Matière :</strong> {{ $session->getSubjectText() }}</li>
        <li><strong>Niveau :</strong> {{ $session->level }}</li>
        <li><strong>Date et heure :</strong> {{ $session->scheduled_at->format('d/m/Y H:i') }}</li>
        <li><strong>Durée :</strong> {{ $session->getFormattedDuration() }}</li>
        <li><strong>Type :</strong> {{ $session->getTypeText() }}</li>
        @if($session->location)
            <li><strong>Lieu :</strong> {{ $session->location }}</li>
        @endif
        <li><strong>Tarif :</strong> {{ number_format($session->price, 2) }} €</li>
        <li><strong>Avec :</strong> {{ $autre->name }} ({{ $autre->email }})</li>
    </ul>
    <p>Vous pouvez consulter les détails de la séance sur la plateforme.</p>
    <p>Cordialement,<br>L'équipe Soutiens-moi!</p>
</body>
</html> 