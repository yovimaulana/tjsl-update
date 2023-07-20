<?php

namespace App\Http\Controllers\Referensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Perusahaan;
use App\Models\User;
use DateTime;
use Exception;
use Auth;
class EnableInputBySuperadmin extends Controller
{
    public function __construct() {
        $this->__route = 'referensi.enable_input';
        $this->pagetitle = 'Enable/Disable Input By Superadmin';
    }

    public function isSuperAdmin() {
        $id_users = \Auth::user()->id;
        $users = User::where('id', $id_users)->first();
        $isSuperAdmin = false;
        if (!empty($users->getRoleNames())) {
            foreach ($users->getRoleNames() as $v) {
                if($v == 'Super Admin') {
                    $isSuperAdmin = true;
                }
            }
        }

        return $isSuperAdmin;
    }

    public function index(Request $request) {
    
        $perusahaan_id = $request->perusahaan_id;
        $tahun = $request->tahun ? $request->tahun : (int)date('Y');
        $referensi = $request->referensi;

        $data_enable = DB::table('enable_input_by_superadmin as ep')
            ->select('ep.*', 'rp.route_name', 'rp.deskripsi', 'pp.nama_lengkap')
            ->join('referensi_enable_input_by_superadmin as rp', 'ep.referensi_id', '=', 'rp.id')
            ->join('perusahaans as pp', 'pp.id', '=', 'ep.perusahaan_id')
            ->when($tahun, function($query) use ($tahun) {
                return $query->where('tahun', $tahun);
            })
            ->when($perusahaan_id, function($query) use ($perusahaan_id) {
                return $query->where('perusahaan_id', $perusahaan_id);
            }) 
            ->when($referensi, function($query) use ($referensi) {
                return $query->where('referensi_id', $referensi);
            })
            ->orderBy('perusahaan_id', 'asc')
            ->get();        

        return view($this->__route .'.index', [
            'master_referensi' => DB::table('referensi_enable_input_by_superadmin')->get(),
            'pagetitle' => $this->pagetitle,
            'breadcrumb' => '',
            'isSuperAdmin' => $this->isSuperAdmin(),
            'perusahaan' => Perusahaan::where('is_active', true)->where('induk', 0)->orderBy('id', 'asc')->get(),
            'tahun' => $tahun,
            'perusahaan_id' => $perusahaan_id,
            'referensi_selected' => $referensi,
            'data_enable' => $data_enable
        ]);

    }

    public function create(Request $request) {
        try {
            $perusahaan_id = $request->perusahaan_id;
            $tahun = $request->tahun ? $request->tahun : (int)date('Y');
            $referensi = $request->referensi;

            return view($this->__route . '.create', [
                'master_referensi' => DB::table('referensi_enable_input_by_superadmin')->get(),
                'pagetitle' => $this->pagetitle,
                'actionform' => 'add',
                'perusahaan' => Perusahaan::where('is_active', true)->where('induk', 0)->orderBy('id', 'asc')->get(),
                'isSuperAdmin' => $this->isSuperAdmin(),
                'tahun' => $tahun,
                'perusahaan_id' => $perusahaan_id,
                'referensi_selected' => $referensi
            ]);
        } catch (Exception $e) {
        }
    }

    public function save(Request $request) {
        DB::beginTransaction();
        try {
            $id_users = Auth::user()->id;
            $isSuperAdmin = $this->isSuperAdmin();
            if(!$isSuperAdmin) throw new Exception('Anda tidak memiliki hak akses terhadap fitur ini!');

            $data = $request->data;
            $tahun = $data['tahun'];
            $referensi_id = $data['tipe'];
            $list_perusahaan = $data['bumn'];
            $currentDate = new DateTime();

            $already_enable = DB::table('enable_input_by_superadmin')
                ->where('tahun', $tahun)
                ->where('referensi_id', $referensi_id)
                ->get();
            $id_already_enable = $already_enable->pluck('perusahaan_id')->toArray();
            
            if($list_perusahaan[0] === 'all') {
                $all_perusahaan = DB::table('perusahaans as pp')
                    ->where('pp.is_active', true) // perusahaan aktif only
                    ->get();
                $list_perusahaan = $all_perusahaan->pluck('id')->toArray();
            }

            // yang disave hanya id_perushaan yang belum ada di table enable
            $list_perusahaan = array_diff($list_perusahaan, $id_already_enable);

            $send = [];
            $log = [];
            foreach($list_perusahaan as $lp) {
                $tempData = [
                    'perusahaan_id' => $lp, 
                    'tahun' => $tahun,
                    'status' => 'enable',
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                    'referensi_id' => $referensi_id,   
                    'user_id' => $id_users
                ];

                $tempDataLog = [
                    'perusahaan_id' => $lp, 
                    'tahun' => $tahun,
                    'status' => 'enable',
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                    'tipe' => $referensi_id,                    
                ];

                array_push($log, $tempDataLog);
                array_push($send, $tempData);

                // // Unset the 'referensi_id' property
                // unset($tempData['referensi_id']);
                // unset($tempData['user_id']);
                // // Push the modified $tempData to $log
                // array_push($log, $tempDataLog);
            }
            // dd($send);
            DB::table('enable_input_by_superadmin')->insert($send);
            DB::table('log_enable_disable_input_datas')->insert($log);

            DB::commit();

            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses tambah data',
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

    public function delete(Request $request) {
        DB::beginTransaction();
        try {

            $isSuperAdmin = $this->isSuperAdmin();
            if(!$isSuperAdmin) throw new Exception('Anda tidak memiliki hak akses terhadap fitur ini!');

            $data = $request->selectedData;
            $currentDate = new DateTime();
            
            $log = [];
            foreach($data as $enable) {
                $temp = DB::table('enable_input_by_superadmin')->where('id', $enable)->first();
                array_push($log, [
                    'perusahaan_id' => $temp->perusahaan_id, 
                    'tahun' => $temp->tahun,
                    'status' => 'disable',
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                    'referensi_id' => $temp->referensi_id,                    
                ]);
            }

            DB::table('log_enable_disable_input_datas')->insert($log);
            DB::table('enable_input_by_superadmin')->whereIn('id', $data)->delete();

            DB::commit();

            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses tambah data',
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

    public function createMaster(Request $request) {
        try {

            return view($this->__route . '.create_master', [
                'master_referensi' => DB::table('referensi_enable_input_by_superadmin')->get(),
                'pagetitle' => $this->pagetitle,
                'actionform' => 'add',
                'isSuperAdmin' => $this->isSuperAdmin(),
            ]);
        } catch (Exception $e) {
        }        
    }

    public function saveMaster(Request $request) {
        try {
            DB::beginTransaction();
            $currentDate = new DateTime();
            $data = $request->data;
            DB::table('referensi_enable_input_by_superadmin')->insert([
                'route_name' => $data['route_name'],
                'deskripsi' => $data['deskripsi'],
                'created_at' => $currentDate,
                'updated_at' => $currentDate
            ]);
            DB::commit();
            $result = [
                'flag'  => 'success',
                'msg' => 'Sukses tambah data',
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

    public function deleteMaster(Request $request) {
        try {
            DB::beginTransaction();
            $id = $request->data;

            $checkForeignKey = DB::table('enable_input_by_superadmin')
                ->where('referensi_id', $id)
                ->first();
            if($checkForeignKey) throw new Exception('Master ini digunakan pada data enable/disable!');

            DB::table('referensi_enable_input_by_superadmin')
                ->where('id', $id)
                ->delete();

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
                'msg' => $e->getMessage(),
                'title' => 'Gagal'
            ];
        }
        return response()->json($result);
    }
}