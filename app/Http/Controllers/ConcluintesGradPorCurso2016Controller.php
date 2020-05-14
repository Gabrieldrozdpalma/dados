<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\GenericChart;
use Uspdev\Cache\Cache;
use Maatwebsite\Excel\Excel;
use App\Exports\DadosExport;

class ConcluintesGradPorCurso2016Controller extends Controller
{
    private $data;
    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
        $cache = new Cache();
        $data = [];

        /* Contabiliza concluintes da graduação em ciências sociais - 2016 */
        $query = file_get_contents(__DIR__ . '/../../../Queries/conta_concluintes_grad_sociais_2016.sql');
        $result = $cache->getCached('\Uspdev\Replicado\DB::fetch', $query);
        $data['Ciências Sociais'] = $result['computed'];

        /* Contabiliza concluintes da graduação em filosofia - 2016 */
        $query = file_get_contents(__DIR__ . '/../../../Queries/conta_concluintes_grad_filosofia_2016.sql');
        $result = $cache->getCached('\Uspdev\Replicado\DB::fetch', $query);
        $data['Filosofia'] = $result['computed'];

        /* Contabiliza concluintes da graduação em geografia - 2016 */
        $query = file_get_contents(__DIR__ . '/../../../Queries/conta_concluintes_grad_geografia_2016.sql');
        $result = $cache->getCached('\Uspdev\Replicado\DB::fetch', $query);
        $data['Geografia'] = $result['computed'];

        /* Contabiliza concluintes da graduação em história - 2016 */
        $query = file_get_contents(__DIR__ . '/../../../Queries/conta_concluintes_grad_historia_2016.sql');
        $result = $cache->getCached('\Uspdev\Replicado\DB::fetch', $query);
        $data['História'] = $result['computed'];

        /* Contabiliza concluintes da graduação em letras - 2016 */
        $query = file_get_contents(__DIR__ . '/../../../Queries/conta_concluintes_grad_letras_2016.sql');
        $result = $cache->getCached('\Uspdev\Replicado\DB::fetch', $query);
        $data['Letras'] = $result['computed'];


        $this->data = $data;
    }

    public function grafico()
    {
        /* Tipos de gráficos:
         * https://www.highcharts.com/docs/chart-and-series-types/chart-types
         */
        $chart = new GenericChart;

        $chart->labels(array_keys($this->data));
        $chart->dataset('Concluintes da Graduação por curso em 2016', 'bar', array_values($this->data));

        return view('concluintesGrad2016PorCurso', compact('chart'));
    }

    public function export($format)
    {
        if($format == 'excel') {
            $export = new DadosExport([$this->data],array_keys($this->data));
            return $this->excel->download($export, 'concluintes_grad_2016.xlsx'); 
        }
    }
}