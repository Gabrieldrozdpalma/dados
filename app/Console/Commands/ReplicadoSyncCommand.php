<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Uspdev\Replicado\Lattes;
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\Posgraduacao;
use App\Models\Lattes as LattesModel;
use App\Utils\ReplicadoTemp;

class ReplicadoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replicadosync';

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
    public function handle()
    {
       
        #$credenciados = ReplicadoTemp::credenciados($codare);
       
        foreach(ReplicadoTemp::credenciados() as $docente) {
            $lattes = LattesModel::where('codpes',$docente['codpes'])->first();
            if(!$lattes) {
                $lattes = new LattesModel;
            }

            $info_lattes = [];
            $info_lattes['nome'] = Pessoa::dump($docente['codpes'])['nompes'];
            $info_lattes['id_lattes'] = Lattes::id($docente['codpes']);
            $data_atualizacao = Lattes::getUltimaAtualizacao($docente['codpes'], null) ; 
            $info_lattes['data_atualizacao'] = $data_atualizacao ? substr($data_atualizacao, 0,2) . '/' . substr($data_atualizacao,2,2) . '/' . substr($data_atualizacao,4,4) : '-';
            $info_lattes['resumo'] = Lattes::getResumoCV($docente['codpes'], 'pt', null);
            $info_lattes['livros'] = Lattes::getLivrosPublicados($docente['codpes'], null, 'anual', -1, null);
            $info_lattes['linhas_pesquisa'] = Lattes::getLinhasPesquisa($docente['codpes'], null);
            $info_lattes['artigos'] = Lattes::getArtigos($docente['codpes'], null, 'anual', -1, null);
            $info_lattes['capitulos'] = Lattes::getCapitulosLivros($docente['codpes'], null, 'anual', -1, null);
            $info_lattes['jornal_revista'] = Lattes::getTextosJornaisRevistas($docente['codpes'], null, 'anual', -1, null);
            //$info_lattes['orientandos'] = Posgraduacao::obterOrientandosAtivos($docente['codpes']);
            //$info_lattes['orientandos_concluidos'] = Posgraduacao::obterOrientandosConcluidos($docente['codpes']);

            $lattes->codpes = $docente['codpes'];
            $lattes->json = json_encode($info_lattes);
           
            
            $lattes->save();
        }
        return 0;
    }
}
