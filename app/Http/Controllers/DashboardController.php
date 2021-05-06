<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Charts\DashCharts;
use App\Entities\MkOs;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    protected $inicio;
    protected $fim;
    protected $tipoOs;

    public function __construct()
    {
        $this->inicio = Carbon::now()->format('Y-m-d 00:00:00');
        $this->fim= Carbon::now()->format('Y-m-d 23:59:59');  
        $this->tipoOs = [13,86,88,97,109,110,137];
    }

    public function subprocessos(){
        $result = DB::connection('pgsql')->table('mk_ate_subprocessos')
        ->where('nome_subprocesso', 'LIKE' ,'SUP. NV2')
        ->select('codsubprocesso', 'nome_subprocesso')
        ->pluck('codsubprocesso');
        return $result;
    }

    public function compromissos(){

        $result = DB::connection('pgsql')->table('mk_compromissos as compromisso')
        ->join('mk_compromisso_pessoa as compessoa', 'compromisso.codcompromisso', '=', 'compessoa.codcompromisso')
        ->join('mk_pessoas as pessoa', 'compessoa.cdpessoa', '=', 'pessoa.codpessoa')
        ->join('mk_os', 'compromisso.cd_integracao', '=', 'mk_os.codos')
        ->join('mk_os_tipo', 'mk_os.tipo_os', '=', 'mk_os_tipo.codostipo')
        ->whereIn('mk_os.tipo_os', $this->tipoOs)
        ->whereBetween('com_inicio',[$this->inicio, $this->fim])
        ->select(
            'compessoa.cdpessoa',
            'pessoa.nome_razaosocial',
            'compromisso.com_inicio','compromisso.com_titulo', 'compromisso.codcompromisso', 
            'compromisso.cd_funcionario','compromisso.cd_integracao',
            'mk_os.tipo_os', 'mk_os_tipo.descricao' ,'mk_os.status as status', 'mk_os.fechamento_tecnico'
            )
        ->get();
        return $result;
    }

    public function atendimentos(){
        $restult = DB::connection('pgsql')->table('mk_os as os')
        ->rightJoin('mk_ate_os as ate_os', 'os.codos','=', 'ate_os.cd_os' )
        ->rightJoin('mk_atendimento as atendimento', 'ate_os.cd_atendimento', '=', 'atendimento.codatendimento')
        ->where('atendimento.finalizado','=' , 'N')
        ->select
            (
              'atendimento.cd_subprocesso','atendimento.codatendimento', 
              'os.codos', 'os.dt_hr_fechamento_tec', 'os.status'
            )
        ->get();
        return $restult;
    }

    public function os(){
        $restult = MkOs::where('status','<>', '3')
        ->whereIn('tipo_os',  $this->tipoOs)
        ->get();
        return $restult;
    }

    public function chartTipos( ){
        $os = $this->compromissos()->countBy('descricao');
        $chart = new DashCharts;
        $chart->labels($os->keys());
        $chart->dataset('Tipos', 'bar' , $os->values());
        $chart->height(300);
        return $chart;
    }

    public function dash_suporte(){

        $chartTipo =  $this->chartTipos();
        $ordens = $this->os();
        $por_tecnico = $this->compromissos()
            ->groupBy('nome_razaosocial');
        $os_total = $this->compromissos()->count();

        $arr_nv2 = $this->subprocessos();

        $nv2_total   = $this->atendimentos()->whereNull('dt_hr_fechamento_tec')->whereIn('cd_subprocesso', $arr_nv2  )->count();
        $total       = $this->atendimentos();
        $inexistente = $total->whereNull('codos' )->count();
        $atend_total = $total->whereNull('dt_hr_fechamento_tec')->count();
        $sem_agenda = $total->where('status', 1)->count();
  
        return view('dashboard.suporte.index', compact('ordens' ,'nv2_total' ,'atend_total', 'por_tecnico', 'os_total', 'chartTipo', 'inexistente', 'sem_agenda'));
    }

}
