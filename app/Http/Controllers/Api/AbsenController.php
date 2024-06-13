<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\ProfilPerusahaan;
use Illuminate\Http\Request;
use Validator;

class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Absen::all();
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data Absen tersedia',
            ];

            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => $th,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required',
                'lokasi_user' => 'required',
                'waktu_absen_masuk' => 'required',
                'tanggal_hari_ini' => 'required',
            ]);
            // cek apakah sudah absen masuk/belum
            $data = Absen::where('tanggal_hari_ini', $request->tanggal_hari_ini)
                ->where('users_id', $request->users_id)
                ->first();
            if ($data != null) {
                $response = [
                    'success' => false,
                    'message' => 'Anda Sudah Absen Masuk',
                ];
                return response()->json($response, 500);
            }

            $profil = ProfilPerusahaan::find(1);
            $profile = strtotime($profil->jam_masuk);
            $profilee = strtotime($request->waktu_absen_masuk);
            $peraturan = date("H:i:s", $profile);
            $pegawai_absen = date("H:i:s", $profilee);
            if ($peraturan >= $pegawai_absen) {
                $status = "Tepat Waktu";
            } else {
                $status = "Terlambat";
            }

            //kalau ya maka akan membuat data absen baru
            $data = Absen::create([
                'users_id' => $request->users_id,
                'lokasi_user' => $request->lokasi_user,
                'waktu_absen_masuk' => $request->waktu_absen_masuk,
                'waktu_absen_pulang' => $request->waktu_absen_pulang,
                'tanggal_hari_ini' => $request->tanggal_hari_ini,
                'status' => $status,
            ]);

            //data akan di kirimkan dalam bentuk response list
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Absen Masuk Berhasil',
            ];
            //jika berhasil maka akan mengirimkan status code 200
            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => 'Gagal Absen Masuk',
            ];
            //jika error maka akan mengirimkan status code 500
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            //mengambil data berdasarkan id absen yang pertama kali ketemu
            $data = Absen::where('id', $id)->first();
            if ($data == null) {
                $response = [
                    'success' => false,
                    'message' => 'Data Absen Tidak Ditemukan',
                ];
                return response()->json($response, 500);
            }
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Selamat Datang, Nama User',
            ];

            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => 'Data Absen Tidak Ditemukan',
            ];
            return response()->json($response, 500);
        }

    }

    public function cek_absen_hari_ini($users_id, $tanggal_hari_ini)
    {
        try {
            $data = Absen::where(['users_id' => $users_id, 'tanggal_hari_ini' => $tanggal_hari_ini])->first();
            if ($data == null) {
                $response = [
                    'success' => false,
                    'message' => 'Data Absen Tidak Ditemukan',
                ];
                return response()->json($response, 500);
            }
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data tersedia',
            ];

            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => 'Data Absen Tidak Ditemukan',
            ];
            return response()->json($response, 500);
        }

    }

    public function absen_history($users_id)
    {
        try {
            $data = Absen::where(['users_id' => $users_id])->limit(30)->get();
            if ($data == null) {
                $response = [
                    'success' => false,
                    'message' => 'Data Absen Tidak Ditemukan',
                ];
                return response()->json($response, 500);
            }
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data tersedia',
            ];

            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => 'Data Absen Tidak Ditemukan',
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal_hari_ini' => 'required',
                'waktu_absen_pulang' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            //cek apakah tanggal dari $data = database itu sama dengan tanggal yang dikirimkan oleh user
            $data = Absen::find($id);
            if ($data['tanggal_hari_ini'] != $request->tanggal_hari_ini) {
                $response = [
                    'success' => false,
                    'message' => 'Tanggal Absen Pulang Tidak Sesuai Dengan Tanggal Absen Masuk',
                ];
                return response()->json($response, 500);
            }
            //cek apakah user sudah absen pulang atau belum, jikas sudah maka muncul notif di bawah
            if ($data['waktu_absen_pulang'] != null) {
                $response = [
                    'success' => false,
                    'message' => 'Absen Pulang Sudah Dilakukan',
                ];
                return response()->json($response, 500);
            }

            $data->waktu_absen_pulang = $request->waktu_absen_pulang;
            $data->save();

            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Berhasil Absen Pulang',
            ];

            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => 'Gagal Absen Pulang',
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $save = Absen::find($id);
            if ($save == null) {
                return response()->json(['success' => false, 'message' => 'Periksa kembali data yang akan di hapus'], 404);
            }
            $save->delete();
            $response = [
                'success' => true,
                'message' => 'Data Absen berhasil dihapus',
            ];
            return response()->json($response, 200);
        } catch (Exception $th) {
            $response = [
                'success' => false,
                'message' => $th,
            ];
            return response()->json($response, 500);
        }

    }
}
