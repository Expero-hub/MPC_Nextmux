<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SupprimerDocumentCorbeille extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:supprimer-document-corbeille';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'les documents sont supprimés de la corbeille au bout de 5 jours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $limite = Carbon::now()->subDays(5);
        $documents = Document::where('etat', 'corbeille')
                            ->where('archived_at', '<=', $limite)
                            ->get();

        foreach ($documents as $doc) {
            
            if ($doc->photo && file_exists(public_path($doc->photo))) {
                unlink(public_path($doc->photo));
            }

            $doc->delete(); // Suppression définitive
        }

        $this->info("{$documents->count()} documents supprimés définitivement.");
       

    }
}
