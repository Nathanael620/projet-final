<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AvatarService
{
    /**
     * Upload and process user avatar
     */
    public function uploadAvatar(User $user, UploadedFile $file): string
    {
        // Supprimer l'ancien avatar s'il existe
        $this->deleteAvatar($user);

        // Générer un nom unique pour le fichier
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = 'avatars/' . $filename;

        // Redimensionner et optimiser l'image
        $image = Image::make($file);
        
        // Redimensionner à 400x400 pixels maximum
        $image->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Convertir en JPEG pour une meilleure compression
        $image->encode('jpg', 85);

        // Sauvegarder l'image
        Storage::disk('public')->put($path, $image);

        return $path;
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(User $user): bool
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            return Storage::disk('public')->delete($user->avatar);
        }

        return false;
    }

    /**
     * Get avatar URL with fallback
     */
    public function getAvatarUrl(User $user, string $size = 'default'): string
    {
        if (!$user->avatar) {
            return $this->getDefaultAvatarUrl($user);
        }

        $url = Storage::url($user->avatar);
        
        // Vérifier si l'image existe
        if (!Storage::disk('public')->exists($user->avatar)) {
            return $this->getDefaultAvatarUrl($user);
        }

        return $url;
    }

    /**
     * Get default avatar URL based on user initials
     */
    public function getDefaultAvatarUrl(User $user): string
    {
        $initials = strtoupper(substr($user->name, 0, 2));
        $color = $this->getUserColor($user->id);
        
        // Utiliser un service d'avatar par défaut
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=200&bold=true";
    }

    /**
     * Generate consistent color for user
     */
    private function getUserColor(int $userId): string
    {
        $colors = [
            '3B82F6', // blue
            '10B981', // green
            'F59E0B', // yellow
            'EF4444', // red
            '8B5CF6', // purple
            '06B6D4', // cyan
            'F97316', // orange
            'EC4899', // pink
        ];

        return $colors[$userId % count($colors)];
    }

    /**
     * Validate avatar file
     */
    public function validateAvatar(UploadedFile $file): array
    {
        $errors = [];

        // Vérifier le type MIME
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = 'Le fichier doit être une image (JPG, PNG ou GIF).';
        }

        // Vérifier la taille (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            $errors[] = 'L\'image ne peut pas dépasser 2MB.';
        }

        // Vérifier les dimensions
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo && ($imageInfo[0] < 100 || $imageInfo[1] < 100)) {
            $errors[] = 'L\'image doit faire au moins 100x100 pixels.';
        }

        return $errors;
    }

    /**
     * Clean up orphaned avatars
     */
    public function cleanupOrphanedAvatars(): int
    {
        $deletedCount = 0;
        $avatarFiles = Storage::disk('public')->files('avatars');

        foreach ($avatarFiles as $file) {
            $userId = $this->extractUserIdFromFilename($file);
            
            if ($userId && !User::where('id', $userId)->exists()) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Extract user ID from avatar filename
     */
    private function extractUserIdFromFilename(string $filename): ?int
    {
        $pathInfo = pathinfo($filename);
        $parts = explode('_', $pathInfo['filename']);
        
        return is_numeric($parts[0]) ? (int) $parts[0] : null;
    }

    /**
     * Get avatar statistics
     */
    public function getAvatarStats(): array
    {
        $totalAvatars = Storage::disk('public')->files('avatars');
        $totalSize = 0;

        foreach ($totalAvatars as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }

        return [
            'total_avatars' => count($totalAvatars),
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'average_size_kb' => count($totalAvatars) > 0 ? round($totalSize / 1024 / count($totalAvatars), 2) : 0,
        ];
    }
} 