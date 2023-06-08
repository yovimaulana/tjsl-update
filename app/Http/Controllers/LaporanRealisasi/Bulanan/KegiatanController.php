<?php

namespace App\Http\Controllers\LaporanRealisasi\Bulanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\PilarPembangunan;
use App\Models\Tpb;
use App\Models\AnggaranTpb;
use App\Models\VersiPilar;
use App\Models\CoreSubject;
use App\Models\TargetTpb;
use App\Models\LogTargetTpb;
use App\Models\Bulan;
use App\Models\JenisKegiatan;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\SatuanUkur;
use App\Models\Kegiatan;
use App\Models\KegiatanRealisasi;
use App\Models\LogKegiatan;
use Datatables;
use DB;
use Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
class KegiatanController extends Controller
{

    public function __construct()
    {

        $this->__route = 'laporan_realisasi.bulanan.kegiatan';
        $this->pagetitle = 'Laporan Realisasi Kegiatan';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->kriteria_program);
        $id_users = \Auth::user()->id;
        $users = User::where('id', $id_users)->first();

        $admin_bumn = false;
        $view_only = false;
        $perusahaan_id = $request->perusahaan_id ?? 1;
        if (!empty($users->getRoleNames())) {
            foreach ($users->getRoleNames() as $v) {
                if ($v == 'Admin BUMN' || $v == 'Verifikator BUMN') {
                    $admin_bumn = true;
                    $perusahaan_id = \Auth::user()->id_bumn;
                }
                if ($v == 'Admin Stakeholder') {
                    $view_only = true;
                }
            }
        }

      

        $tahun = $request->tahun ? $request->tahun : (int)date('Y');
        $jenis_anggaran = $request->jenis_anggaran ?? 'CID';
        $jenis_kegiatan_id = $request->jenis_kegiatan ?? '';
        $jenis_kegiatan = DB::table('jenis_kegiatans')->where('is_active', true)->get();
        // dd($jenis_anggaran);
        
        //Kegiatan
        $program = DB::table('target_tpbs')
        ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
            $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
                ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                ->where('anggaran_tpbs.tahun', $tahun);
        })
        ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        ->select(
            'target_tpbs.*',

            'anggaran_tpbs.id as anggaran_tpb_id',
            'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
            'tpbs.id as tpb_id',
            'tpbs.jenis_anggaran'
        )
        ->get();
        // $currentMonth = (int) date('n');

        $bulan = $request->bulan_id ??  (int) date('n');
        $tahun = $request->tahun ?? date('Y');
        
        $kegiatan = DB::table('kegiatans')
        ->join('kegiatan_realisasis', function($join) use ($bulan, $tahun) {
            $join->on('kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
                ->where('kegiatan_realisasis.bulan', $bulan)
                ->where('kegiatan_realisasis.tahun', $tahun);
        })
        ->join('target_tpbs', 'target_tpbs.id', 'kegiatans.target_tpb_id')
        ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
            $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
                ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                ->where('anggaran_tpbs.tahun', $tahun);
        })
        ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        ->leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'kegiatans.jenis_kegiatan_id')
        ->join('provinsis', 'provinsis.id', '=', 'kegiatans.provinsi_id')
        ->join('kotas', 'kotas.id', '=', 'kegiatans.kota_id')
        ->join('satuan_ukur', 'satuan_ukur.id', '=', 'kegiatans.satuan_ukur_id')
        ->select(
            'kegiatans.*',
            'kegiatan_realisasis.bulan as kegiatan_realisasi_bulan',
            'kegiatan_realisasis.tahun as kegiatan_realisasi_tahun',
            'kegiatan_realisasis.anggaran as kegiatan_realisasi_anggaran',
            'kegiatan_realisasis.anggaran_total as kegiatan_realisasi_anggaran_total',
            'kegiatan_realisasis.status_id as kegiatan_realisasi_status_id',
            'target_tpbs.program as target_tpb_program',
            'jenis_kegiatans.nama as jenis_kegiatan_nama',
            'provinsis.nama as provinsi_nama',
            'kotas.nama as kota_nama',
            'anggaran_tpbs.id as anggaran_tpb_id',
            'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
            'tpbs.id as tpb_id',
            'tpbs.jenis_anggaran',
            'satuan_ukur.nama as satuan_ukur_nama'
        )
        ->get();
        // dd($kegiatan);
        // $pilar_pembangunan_id = $request->pilar_pembangunan ?? '';
        //     dd($pilar_pembangunan_id);
        return view($this->__route . '.index', [
            'pagetitle' => $this->pagetitle,
            'breadcrumb' => '',
            'perusahaan' => Perusahaan::where('is_active', true)->orderBy('id', 'asc')->get(),
            // 'anggaran' => $anggaran,
            // 'anggaran_pilar' => $anggaran_pilar,
            // 'anggaran_bumn' => $anggaran_bumn,
            'pilar' => PilarPembangunan::select(DB::raw('DISTINCT ON (nama) *'))->where('is_active', true)->orderBy('nama')->orderBy('id')->get(),
            'tpb' => Tpb::select(DB::raw('DISTINCT ON (no_tpb) *'))->orderBy('no_tpb')->orderBy('id')->get(),
            'bulan' => Bulan::all(),
            'admin_bumn' => $admin_bumn,
            'perusahaan_id' => $perusahaan_id,
            'tahun' => ($request->tahun ? $request->tahun : date('Y')),
            'jenis_anggaran' => $jenis_anggaran,
            'kriteria_program' => $kriteria_program ?? [],
            'pilar_pembangunan_id' => $request->pilar_pembangunan ?? '',
            // 'tpb_id' => $request->tpb,
            // 'view_only' => $view_only,
            // 'pilar_pembangunan_id' => $request->pilar_pembangunan,
            'tpb_id' => $request->tpb ?? '',
            'view_only' => $view_only,
            'program' => $program,
            'jenis_kegiatan' => $jenis_kegiatan,
            'jenis_kegiatan_id' => $jenis_kegiatan_id,
            'bulan_id' =>  $bulan,
            'program_id' => $request->program_id ?? '',
            'jenis_kegiatan_id' => $request->jenis_kegiatan ?? ''

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($perusahaan_id, $tahun, $bulan)
    {
        $admin_bumn = false;
        $view_only = false;
        $id_users = \Auth::user()->id;
        $users = User::where('id', $id_users)->first();
        if (!empty($users->getRoleNames())) {
            foreach ($users->getRoleNames() as $v) {
                if ($v == 'Admin BUMN') {
                    $admin_bumn = true;
                    $perusahaan_id = \Auth::user()->id_bumn;
                }
                if ($v == 'Admin Stakeholder') {
                    $view_only = true;
                }
            }
        }
        $versi = VersiPilar::whereNull('tanggal_akhir')->orWhere('tanggal_akhir', '>=', date('Y-m-d'))->first();
        $versi_pilar_id = $versi->id;
        // $pilars = DB::table('relasi_pilar_tpbs')
        //     ->join('pilar_pembangunans', 'pilar_pembangunans.id', '=', 'relasi_pilar_tpbs.pilar_pembangunan_id')
        //     ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        //     ->where('versi_pilar_id', $versi->id)
        //     ->get(['relasi_pilar_tpbs.id', 'pilar_pembangunans.nama as pilar_name', 'pilar_pembangunans.jenis_anggaran as pilar_jenis_anggaran', 'tpbs.nama as tpb_name', 'tpbs.jenis_anggaran as tpb_jenis_anggaran']);


        // $current = AnggaranTpb::join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        //     ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        //     ->join('pilar_pembangunans', 'pilar_pembangunans.id', '=', 'relasi_pilar_tpbs.pilar_pembangunan_id')
        //     ->where('perusahaan_id', $perusahaan_id)
        //     ->where('tahun', $tahun)
        //     ->get();

        $pilars = DB::table('relasi_pilar_tpbs')
            ->join('pilar_pembangunans', 'pilar_pembangunans.id', '=', 'relasi_pilar_tpbs.pilar_pembangunan_id')
            ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
            ->leftJoin('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun){
                $join->on('anggaran_tpbs.relasi_pilar_tpb_id', '=', 'relasi_pilar_tpbs.id')
                    ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                    ->where('anggaran_tpbs.tahun', $tahun);
            })
            ->where('versi_pilar_id', $versi->id)            
            ->get(['relasi_pilar_tpbs.id', 'pilar_pembangunans.nama as pilar_name', 'pilar_pembangunans.jenis_anggaran as pilar_jenis_anggaran', 'tpbs.nama as tpb_name', 'tpbs.jenis_anggaran as tpb_jenis_anggaran', 'anggaran_tpbs.anggaran', 'tpbs.no_tpb as tpb_no_tpb']);


        // if (count($current) > 0) {
        //     $actionform = 'update';
        // } else {
        //     $actionform = 'insert';
        // }

        // foreach ($pilars as $key => $pilar) {
        //     foreach ($current as $key => $current2) {

        //         if ($pilar->id == $current2->relasi_pilar_tpb_id) {

        //             $pilarArray = (array) $pilar; // convert object to array
        //             $pilarArray['anggaran'] = $current2->anggaran; // add new key
        //             $pilars[$key] = (object) $pilarArray; // convert array back to object and assign it to $pilars
        //         }
        //     }
        // }
        $pilars = $pilars->groupBy([
            'pilar_name',
            function ($item) {
                return $item->tpb_name;
            }
        ])->sortByDesc(null);
        
        // dd($pilars);

        //untuk View tabelnya
        $id_users = \Auth::user()->id;
        $users = User::where('id', $id_users)->first();
        $perusahaan_id = $request->perusahaan_id ?? 1;

        $admin_bumn = false;
        $view_only = false;
        if (!empty($users->getRoleNames())) {
            foreach ($users->getRoleNames() as $v) {
                if ($v == 'Admin BUMN') {
                    $admin_bumn = true;
                    $perusahaan_id = \Auth::user()->id_bumn;
                }
                if ($v == 'Admin Stakeholder') {
                    $view_only = true;
                }
            }
        }
        // Kegiatan
        $program = DB::table('target_tpbs')
        ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
            $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
                ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                ->where('anggaran_tpbs.tahun', $tahun);
        })
        ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        ->select(
            'target_tpbs.*',

            'anggaran_tpbs.id as anggaran_tpb_id',
            'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
            'tpbs.id as tpb_id',
            'tpbs.jenis_anggaran'
        )
        ->get();
        // dd($program);

        // $targetTpbs = DB::table('target_tpbs')
        //     ->leftJoin('anggaran_tpbs', 'target_tpbs.anggaran_tpb_id', '=', 'anggaran_tpbs.id')
        //     ->leftJoin('relasi_pilar_tpbs', 'anggaran_tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        //     ->leftJoin('tpbs', 'relasi_pilar_tpbs.tpb_id', '=', 'tpbs.id')
        //     ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
        //     ->where('anggaran_tpbs.tahun', $tahun)
        //     ->select('target_tpbs.*', 'tpbs.jenis_anggaran')
        //     ->get();
        // dd($targetTpbs);
       
       
        return view(
            $this->__route . '.create',
            [
                'pagetitle' => $this->pagetitle,
                'breadcrumb' => '',
                'pilars' => $pilars,
                'perusahaan_id' => $perusahaan_id,
                'tahun' => $tahun,
                'actionform' => '-',
                'nama_perusahaan' => Perusahaan::find($perusahaan_id)->nama_lengkap,
                'bulan' => Bulan::all(),
                // 'pilar' => PilarPembangunan::get(),
                // 'versi_pilar_id' => $versi_pilar_id,
                'perusahaan' => Perusahaan::where('is_active', true)->orderBy('id', 'asc')->get(),
                'admin_bumn' => $admin_bumn,
                'jenis_kegiatan' => JenisKegiatan::all(),
                'provinsi' => Provinsi::where('is_luar_negeri', false)->get(),
                'kota_kabupaten' => Kota::where('is_luar_negeri', false)->get(),
                'satuan_ukur' => SatuanUkur::where('is_active', true)->get(),
                'program' => $program,
                'bulan_id' =>$bulan ?? 1,
                
           
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        // dd($request->data['nama_kegiatan']);
  
       
        // dd($kegiatanGroup);
        DB::beginTransaction();
        try {
            $kegiatan = new Kegiatan();
            $kegiatan->target_tpb_id = $request->data['program_id'];
            $kegiatan->kegiatan = $request->data['nama_kegiatan'];
            $kegiatan->provinsi_id = $request->data['provinsi'];
            $kegiatan->kota_id = $request->data['kota_kabupaten'];
            $kegiatan->indikator = $request->data['realisasi_indikator'];
            $kegiatan->satuan_ukur_id = $request->data['satuan_ukur'];
            $kegiatan->anggaran_alokasi = $request->data['realisasi_anggaran'];
            $kegiatan->jenis_kegiatan_id = $request->data['jenis_kegiatan'];
            $kegiatan->keterangan_kegiatan = $request->data['keterangan_kegiatan'];
            $kegiatan->save();
    
            $kegiatanGroup = Kegiatan::where('kegiatan', $request->data['nama_kegiatan'])
            ->where('target_tpb_id', $request->data['program_id'])
            ->join('kegiatan_realisasis', 'kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
            ->orderBy('kegiatan_realisasis.bulan', 'desc')
            ->first();
            $kumulatif_anggaran = $request->data['realisasi_anggaran'];
            if ($kegiatanGroup) {
                $kumulatif_anggaran = $kumulatif_anggaran + $kegiatanGroup->anggaran_total;
            }
            $kegiatanRealisasi = new KegiatanRealisasi();
            $kegiatanRealisasi->kegiatan_id = $kegiatan->id;
            $kegiatanRealisasi->bulan = $request->bulan;
            $kegiatanRealisasi->tahun = $request->tahun;
            // target,realisasi -> null
            $kegiatanRealisasi->anggaran = $request->data['realisasi_anggaran'];
            $kegiatanRealisasi->anggaran_total = $kumulatif_anggaran;
            $kegiatanRealisasi->status_id = 2;//in progress
            $kegiatanRealisasi->save();

            KegiatanController::store_log($kegiatanRealisasi->id,$kegiatanRealisasi->status_id);
            DB::commit();
            Session::flash('success', "Berhasil Menyimpan Data Kegiatan");
            $result = [
                        'flag'  => 'success',
                        'msg' => 'Sukses tambah data',
                        'title' => 'Sukses'
            ];
            echo json_encode(['result' => true]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            throw $th;
        }
       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        
        $kegiatan = DB::table('kegiatans')
        ->join('kegiatan_realisasis', 'kegiatan_realisasis.kegiatan_id', 'kegiatans.id')
        ->where('kegiatans.id', $request->id)
        ->first();

        $perusahaan_id = $request->perusahaan_id;
        $tahun = $request->tahun;
        $jenis_anggaran = $request->jenis_anggaran;
        
        $program = DB::table('target_tpbs')
        ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
            $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
                ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                ->where('anggaran_tpbs.tahun', $tahun);
        })
        ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        ->select(
            'target_tpbs.*',

            'anggaran_tpbs.id as anggaran_tpb_id',
            'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
            'tpbs.id as tpb_id',
            'tpbs.jenis_anggaran'
        )
        ->get();

        $jenis_kegiatan = DB::table('jenis_kegiatans')->where('is_active', true)->get();
      
        // $kegiatan = DB::table('kegiatans')
        // ->join('kegiatan_realisasis', function($join) use ($bulan, $tahun) {
        //     $join->on('kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
        //         ->where('kegiatan_realisasis.bulan', $bulan)
        //         ->where('kegiatan_realisasis.tahun', $tahun);
        // })
        // ->join('target_tpbs', 'target_tpbs.id', 'kegiatans.target_tpb_id')
        // ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
        //     $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
        //         ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
        //         ->where('anggaran_tpbs.tahun', $tahun);
        // })
        // ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
        // ->join('tpbs', 'tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
        // ->leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'kegiatans.jenis_kegiatan_id')
        // ->join('provinsis', 'provinsis.id', '=', 'kegiatans.provinsi_id')
        // ->join('kotas', 'kotas.id', '=', 'kegiatans.kota_id')
        // ->join('satuan_ukur', 'satuan_ukur.id', '=', 'kegiatans.satuan_ukur_id')
        // ->select(
        //     'kegiatans.*',
        //     'kegiatan_realisasis.bulan as kegiatan_realisasi_bulan',
        //     'kegiatan_realisasis.tahun as kegiatan_realisasi_tahun',
        //     'kegiatan_realisasis.anggaran as kegiatan_realisasi_anggaran',
        //     'kegiatan_realisasis.anggaran_total as kegiatan_realisasi_anggaran_total',
        //     'kegiatan_realisasis.status_id as kegiatan_realisasi_status_id',
        //     'target_tpbs.program as target_tpb_program',
        //     'jenis_kegiatans.nama as jenis_kegiatan_nama',
        //     'provinsis.nama as provinsi_nama',
        //     'kotas.nama as kota_nama',
        //     'anggaran_tpbs.id as anggaran_tpb_id',
        //     'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
        //     'tpbs.id as tpb_id',
        //     'tpbs.jenis_anggaran',
        //     'satuan_ukur.nama as satuan_ukur_nama'
        // )
        // ->get();

        // dd($kegiatan);
        try {
            // $data = TargetTpb::find((int)$request->input('program'));
            // $anggaran_tpbs = AnggaranTpb::find($data->anggaran_tpb_id);
            // $perusahaan_id = $anggaran_tpbs->perusahaan_id;
            // $tahun = $anggaran_tpbs->tahun;
            // $tpbs_temp = Tpb::find($data->tpb_id);
            return view($this->__route . '.edit', [
                'pagetitle' => $this->pagetitle,
                'actionform' => 'update',
                // 'tpb' => DB::table('tpbs')->select('*')->whereIn('id', function($query) use($perusahaan_id, $tahun) {
                //     $query->select('relasi_pilar_tpbs.tpb_id as id')
                //         ->from('anggaran_tpbs')
                //         ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id','=','anggaran_tpbs.relasi_pilar_tpb_id')
                //         ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                //         ->where('anggaran_tpbs.tahun', $tahun);
                // })->where('tpbs.jenis_anggaran', $tpbs_temp->jenis_anggaran)->get(),
                // 'core_subject' => CoreSubject::get(),
                // 'perusahaan' => Perusahaan::where('is_active', true)->orderBy('id', 'asc')->get(),
                // 'data' => $data,
                // 'id_program' => $request->input('program'),
                // 'tahun' => $tahun,
                // 'perusahaan_id' => $perusahaan_id
                'kegiatan' => $kegiatan,
                'program' => $program,
                'jenis_kegiatan' => $jenis_kegiatan,
                'jenis_anggaran' => $jenis_anggaran,
                'provinsi' => Provinsi::where('is_luar_negeri', false)->get(),
                'kota_kabupaten' => Kota::where('is_luar_negeri', false)->get(),
                'satuan_ukur' => SatuanUkur::where('is_active', true)->get(),
            ]);
        } catch (Exception $e) {
        }
    }

    public function editStore(Request $request) {

        // dd($request);
        // dd($request->data['kegiatan_data']['id']);
       
        
        
        DB::beginTransaction();
        try {
            $kegiatan = Kegiatan::where('id', $request->data['kegiatan_data']['kegiatan_id'])->first();
            $kegiatan->target_tpb_id = $request->data['program_id_edit'];
            $kegiatan->kegiatan = $request->data['nama_kegiatan_edit'];
            $kegiatan->provinsi_id = $request->data['provinsi_edit'];
            $kegiatan->kota_id = $request->data['kota_kabupaten_edit'];
            $kegiatan->indikator = $request->data['realisasi_indikator_edit'];
            $kegiatan->satuan_ukur_id = $request->data['satuan_ukur_edit'];
            $kegiatan->anggaran_alokasi = $request->data['realisasi_anggaran_edit'];
            $kegiatan->save();

            $kegiatanRealisasi = KegiatanRealisasi::where('kegiatan_id',$request->data['kegiatan_data']['kegiatan_id'] )->first();
            $kegiatanRealisasi->anggaran = $kegiatan->anggaran_alokasi;
            $kegiatanRealisasi->save();
            KegiatanController::store_log( $kegiatanRealisasi->id, 2);//in progress

            //cek ulang kumulatif anggaran versi sebelumnya
            $kegiatanGroupOld = Kegiatan::where('kegiatan', $request->data['kegiatan_data']['kegiatan'])
            ->where('target_tpb_id', $request->data['kegiatan_data']['target_tpb_id'])
            ->join('kegiatan_realisasis', 'kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
            ->orderBy('kegiatan_realisasis.bulan')
            ->get();
            
            $kumulatif_anggaran_old = 0;
            foreach ($kegiatanGroupOld as $key => $kegiatan) {
           
                $kumulatif_anggaran_old = $kumulatif_anggaran_old + $kegiatan->anggaran;
                $kegiatanRealisasi = KegiatanRealisasi::where('id', $kegiatan->id )->first();
                $kegiatanRealisasi->anggaran = $kegiatan->anggaran_alokasi;
                $kegiatanRealisasi->anggaran_total = $kumulatif_anggaran_old;
                $kegiatanRealisasi->save();
            }

            //cek ulang kumulatif anggaran versi baru
            $kegiatanGroupNew = Kegiatan::where('kegiatan',  $kegiatan->kegiatan)
            ->where('target_tpb_id', $kegiatan->target_tpb_id)
            ->join('kegiatan_realisasis', 'kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
            ->orderBy('kegiatan_realisasis.bulan')
            ->get();

            $kumulatif_anggaran_new = 0;
            foreach ($kegiatanGroupNew as $key => $kegiatan) {
           
                $kumulatif_anggaran_new = $kumulatif_anggaran_new + $kegiatan->anggaran;
                $kegiatanRealisasi = KegiatanRealisasi::where('id', $kegiatan->id )->first();
                $kegiatanRealisasi->anggaran = $kegiatan->anggaran_alokasi;
                $kegiatanRealisasi->anggaran_total = $kumulatif_anggaran_new;
                $kegiatanRealisasi->save();
            }
            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Berhasil memperbarui data!',
                'title' => 'Sukses'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => $e->getMessage(),
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function datatable(Request $request)
    {
        // dd($request);
        
        // $periode_rka_id = DB::table('periode_laporans')->where('nama', 'RKA')->first()->id;
        // $laporan_manajemen = DB::table('laporan_manajemens')->selectRaw('laporan_manajemens.*, perusahaans.id as perusahaan_id, perusahaans.nama_lengkap as nama_lengkap')
        // ->leftJoin('perusahaans', 'perusahaans.id', '=', 'laporan_manajemens.perusahaan_id')->where('periode_laporan_id', $periode_rka_id)->where('perusahaans.induk', 0);

        $perusahaan_id = $request->perusahaan_id ?? 1;
        $bulan = $request->bulan ?? 1;
        $tahun = $request->tahun ?? date('Y');
        $jenis_anggaran = $request->jenis_anggaran ?? 'CID';
        $kegiatan = DB::table('kegiatans')
        ->join('kegiatan_realisasis', function($join) use ($bulan, $tahun) {
            $join->on('kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
                ->where('kegiatan_realisasis.bulan', $bulan)
                ->where('kegiatan_realisasis.tahun', $tahun);
        })
        ->join('target_tpbs', 'target_tpbs.id', 'kegiatans.target_tpb_id')
        ->join('anggaran_tpbs', function($join) use ($perusahaan_id, $tahun) {
            $join->on('anggaran_tpbs.id', '=', 'target_tpbs.anggaran_tpb_id')
                ->where('anggaran_tpbs.perusahaan_id', $perusahaan_id)
                ->where('anggaran_tpbs.tahun', $tahun);
        })
        ->join('relasi_pilar_tpbs', 'relasi_pilar_tpbs.id', '=', 'anggaran_tpbs.relasi_pilar_tpb_id')
    
        ->join('tpbs', function($join) use ($jenis_anggaran) {
            $join->on('tpbs.id', '=', 'relasi_pilar_tpbs.tpb_id')
                ->where('tpbs.jenis_anggaran', $jenis_anggaran);
        })
        ->leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'kegiatans.jenis_kegiatan_id')
        ->join('provinsis', 'provinsis.id', '=', 'kegiatans.provinsi_id')
        ->join('kotas', 'kotas.id', '=', 'kegiatans.kota_id')
        ->join('satuan_ukur', 'satuan_ukur.id', '=', 'kegiatans.satuan_ukur_id')
        ->select(
            'kegiatans.*',
            'kegiatan_realisasis.bulan as kegiatan_realisasi_bulan',
            'kegiatan_realisasis.tahun as kegiatan_realisasi_tahun',
            'kegiatan_realisasis.anggaran as kegiatan_realisasi_anggaran',
            'kegiatan_realisasis.anggaran_total as kegiatan_realisasi_anggaran_total',
            'kegiatan_realisasis.status_id as kegiatan_realisasi_status_id',
            'target_tpbs.program as target_tpb_program',
            'jenis_kegiatans.nama as jenis_kegiatan_nama',
            'provinsis.nama as provinsi_nama',
            'kotas.nama as kota_nama',
            'anggaran_tpbs.id as anggaran_tpb_id',
            'relasi_pilar_tpbs.id as relasi_pilar_tpb_id',
            'tpbs.id as tpb_id',
            'tpbs.jenis_anggaran',
            'satuan_ukur.nama as satuan_ukur_nama'
        );

        if ($request->pilar_pembangunan_id) {

            $kegiatan = $kegiatan->where('relasi_pilar_tpbs.pilar_pembangunan_id', $request->pilar_pembangunan_id);
        }

        if ($request->tpb_id) {

            $kegiatan = $kegiatan->where('tpbs.id', $request->tpb_id);
        }

        if ($request->program_id) {

            $kegiatan = $kegiatan->where('target_tpbs.id', $request->program_id);
        }

        if ($request->jenis_kegiatan) {

            $kegiatan = $kegiatan->where('jenis_kegiatans.id', $request->jenis_kegiatan);
        }

        $kegiatan = $kegiatan->get();
       
        try {
            return datatables()->of($kegiatan)
                ->addColumn('action', function ($row) {
                    $id = (int)$row->id;
                    $button = '<div align="center">';

                    // $button .= '<button type="button" class="btn btn-sm btn-light btn-icon btn-primary cls-button-edit" data-id="' . $id . '" data-toggle="tooltip" title="Ubah data ' . $row->nama . '"><i class="bi bi-pencil fs-3"></i></button>';
                    $button .= '<button type="button" class="btn btn-sm btn-light btn-icon btn-primary cls-button-edit" data-id="' . $id . '" data-toggle="tooltip" title="Ubah data '  . '"><i class="bi bi-pencil fs-3"></i></button>';

                    $button .= '&nbsp;';

                    // $button .= '<button type="button" class="btn btn-sm btn-danger btn-icon cls-button-delete" data-id="' . $id . '" data-nama="' . $row->nama . '" data-toggle="tooltip" title="Hapus data ' . $row->nama . '"><i class="bi bi-trash fs-3"></i></button>';

                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['id', 'target_tpb_program', 'kegiatan', 'jenis_kegiatan_nama', 'provinsi_nama','kota_nama', 'anggaran_alokasi', 'indikator', 'kegiatan_realisasi_status_id', 'action'])
                ->toJson();
        } catch (Exception $e) {
            return response([
                'draw'            => 0,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => []
            ]);
        }
    }

    public static function store_log($kegiatan_id, $status_id)
    {  
        $param['kegiatan_id'] = $kegiatan_id;
        $param['status_id'] = $status_id;
        $param['user_id'] = \Auth::user()->id;
        LogKegiatan::create((array)$param);
    }

    public function log_status(Request $request)
    {
        $kegiatanRealisasi = KegiatanRealisasi::where('kegiatan_id', (int)$request->input('id'))->first();

        $log = LogKegiatan::select('log_kegiatans.*', 'users.name AS user', 'statuses.nama AS status')
            ->leftjoin('users', 'users.id', '=', 'log_kegiatans.user_id')
            ->leftjoin('statuses', 'statuses.id', '=', 'log_kegiatans.status_id')
            ->where('kegiatan_id', $kegiatanRealisasi->id)
            ->orderBy('created_at')
            ->get();

        return view($this->__route . '.log_status', [
            'pagetitle' => 'Log Status',
            'log' => $log
        ]);
    }

    public function delete(Request $request) {
        
      
         
         
       
        DB::beginTransaction();
        try {
            foreach ($request->kegiatan_deleted as $key => $kegiatan_id) {
                //kegiatan
                $kegiatan = Kegiatan::where('id', $kegiatan_id)->first();
                
                //save old kegiatan nama dan target_tpb_id
                $kegiatan_nama = $kegiatan->kegiatan;
                $target_tpb_id = $kegiatan->target_tpb_id;
    
                //delete kegiatan dan kegiatan realisasis
                $kegiatan_realisasis = KegiatanRealisasi::where('kegiatan_id', $kegiatan_id)->first();
                $kegiatan_realisasis->delete();
                $kegiatan->delete();
                //cek ulang kumulatif anggaran versi sebelumnya
                $kegiatanGroupOld = Kegiatan::where('kegiatan', $kegiatan_nama)
                ->where('target_tpb_id', $target_tpb_id)
                ->join('kegiatan_realisasis', 'kegiatan_realisasis.kegiatan_id', '=', 'kegiatans.id')
                ->orderBy('kegiatan_realisasis.bulan')
                ->get();
    
                $kumulatif_anggaran_old = 0;
                foreach ($kegiatanGroupOld as $key => $kegiatanOld) {
               
                    $kumulatif_anggaran_old = $kumulatif_anggaran_old + $kegiatanOld->anggaran;
                    $kegiatanRealisasi = KegiatanRealisasi::where('id', $kegiatanOld->id )->first();
                    $kegiatanRealisasi->anggaran = $kegiatanOld->anggaran_alokasi;
                    $kegiatanRealisasi->anggaran_total = $kumulatif_anggaran_old;
                    $kegiatanRealisasi->save();
                }
                
            }
            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses hapus data',
                'title' => 'Sukses'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => 'Gagal hapus data',
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);
    }

    public function verifikasiData(Request $request) {

       
        DB::beginTransaction();
        try {
            foreach ($request->kegiatan_verifikasi as $key => $kegiatan_id) {
                $kegiatan = Kegiatan::where('id', $kegiatan_id)->first();
                
                //save old kegiatan nama dan target_tpb_id
                $kegiatan_nama = $kegiatan->kegiatan;
                $target_tpb_id = $kegiatan->target_tpb_id;
    
                //delete kegiatan dan kegiatan realisasis
                $kegiatan_realisasi = KegiatanRealisasi::where('kegiatan_id', $kegiatan_id)->first();
                if($kegiatan_realisasi && $kegiatan_realisasi->status_id !== 1) {
                    $kegiatan_realisasi->status_id = 1;
                    $kegiatan_realisasi->save();
                    KegiatanController::store_log($kegiatan_realisasi->id, 1);//finish


                }
                
        }
            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses verifikasi data',
                'title' => 'Sukses'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            $result = [
                'flag'  => 'warning',
                'msg' => 'Gagal verifikasi data',
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);
    }
}