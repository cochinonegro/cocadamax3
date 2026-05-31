<?php

namespace App\Console\Commands;

use App\Filament\Support\ProgramaImageUpload;
use App\Models\Programas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RepairProgramaGalleryImages extends Command
{
    protected $signature = 'programas:repair-gallery-images {--dry-run : Solo mostrar cambios, sin guardar}';

    protected $description = 'Normaliza rutas de galería en BD y elimina referencias a archivos inexistentes';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $updated = 0;

        Programas::query()
            ->whereNotNull('gallery_images')
            ->orderBy('id')
            ->each(function (Programas $programa) use ($dryRun, &$updated): void {
                $original = $programa->gallery_images ?? [];

                if (! is_array($original) || $original === []) {
                    return;
                }

                $repaired = ProgramaImageUpload::existingStoredPaths($original, 'programas/gallery');

                if ($original === $repaired) {
                    return;
                }

                $this->line(sprintf(
                    'Programa #%d (%s)',
                    $programa->id,
                    $programa->progname,
                ));
                $this->line('  antes: '.json_encode($original));
                $this->line('  después: '.json_encode($repaired));

                if (! $dryRun) {
                    $programa->update(['gallery_images' => $repaired !== [] ? $repaired : null]);
                }

                $updated++;
            });

        $directories = [
            'programas/gallery',
            'programas/instalacion',
            'programas/instalador',
        ];

        foreach ($directories as $directory) {
            if (! Storage::disk('public')->exists($directory)) {
                $this->warn("Creando directorio storage/app/public/{$directory}");

                if (! $dryRun) {
                    Storage::disk('public')->makeDirectory($directory);
                }
            }
        }

        $this->info($dryRun
            ? "Dry run: {$updated} registro(s) pendientes de reparar."
            : "Reparados {$updated} registro(s).");

        return self::SUCCESS;
    }
}
