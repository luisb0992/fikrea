<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanLockedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locked-files:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->cleanLockedFiles();
    }

    /**
     * Eliminar los ficheros subidos en estado bloqueado que llevan más de 24 horas en ese estado
     *
     * @return int
     */
    protected function cleanLockedFiles(): int
    {
        $deadline = Carbon::now()->subHours(24);

        // Obtener los ficheros bloqueados por más de 24 horas
        $locked = DB::table('files')->where('locked', true)->whereDate('created_at', '<=', $deadline)->get(
            ['id', 'path']
        );

        // Eliminarlos físicamente
        Storage::disk(env('APP_STORAGE'))->delete($locked->pluck('path')->toArray());

        // Eliminarlos de la base de datos
        return DB::table('files')->whereIn('id', $locked->pluck('id')->toArray())->delete();
    }
}
