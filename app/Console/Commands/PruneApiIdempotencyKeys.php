<?php

namespace App\Console\Commands;

use App\Models\ApiIdempotencyKey;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('api:prune-idempotency')]
#[Description('Hapus kunci idempotensi API yang sudah kedaluwarsa.')]
class PruneApiIdempotencyKeys extends Command
{
    public function handle(): int
    {
        $deleted = ApiIdempotencyKey::query()
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("{$deleted} kunci idempotensi API dihapus.");

        return self::SUCCESS;
    }
}
