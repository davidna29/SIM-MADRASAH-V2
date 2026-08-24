<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    protected array $editorRoles = ['editor_berita', 'wakamad_humas', 'kepala_madrasah', 'super_admin'];

    protected array $contributorRoles = ['guru', 'editor_berita', 'wakamad_humas', 'kepala_madrasah', 'super_admin'];

    public function viewAny(User $user): bool
    {
        return in_array($user->role, $this->contributorRoles, true);
    }

    public function view(User $user, Article $article): bool
    {
        return in_array($user->role, $this->contributorRoles, true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, $this->contributorRoles, true);
    }

    public function update(User $user, Article $article): bool
    {
        if (in_array($user->role, $this->editorRoles, true)) {
            return true;
        }

        return $article->author_id === $user->id;
    }

    public function delete(User $user, Article $article): bool
    {
        if (in_array($user->role, $this->editorRoles, true)) {
            return true;
        }

        return $article->author_id === $user->id && $article->status === Article::DRAFT;
    }
}
