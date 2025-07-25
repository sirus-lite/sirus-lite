<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Livewire\MasterPasien\MasterPasien;

use App\Http\Livewire\MasterPoli\MasterPoli;
use App\Http\Livewire\MasterDokter\MasterDokter;
use App\Http\Livewire\MasterKamarAplicares\MasterKamarAplicares;



use App\Http\Livewire\PendaftaranMandiriPasienPoli\PendaftaranMandiriPasienPoli;
use App\Http\Livewire\DaftarRJ\DaftarRJ;
use App\Http\Livewire\DaftarUGD\DaftarUGD;
use App\Http\Livewire\DaftarRI\DaftarRI;

use App\Http\Livewire\RJskdp\RJskdp;
use App\Http\Livewire\RIskdp\RIskdp;

use App\Http\Livewire\PelayananRJ\PelayananRJ;
use App\Http\Livewire\DisplayPelayananRJ\DisplayPelayananRJ;
use App\Http\Livewire\EmrRJ\AdministrasiRJ\AdministrasiRJ;
use App\Http\Livewire\EmrRJ\EmrRJ;

use App\Http\Livewire\EmrRJBulan\EmrRJBulan;
use App\Http\Livewire\EmrRJHari\EmrRJHari;


use App\Http\Livewire\BookingRJ\BookingRJ;

use App\Http\Livewire\EmrRJ\TelaahResepRJ\TelaahResepRJ;
use App\Http\Livewire\EmrUGD\TelaahResepUGD\TelaahResepUGD;
use App\Http\Livewire\EmrRI\TelaahResepRI\TelaahResepRI;


use App\Http\Livewire\EmrUGD\EmrUGD;

use App\Http\Livewire\SetupHfisBpjs\SetupHfisBpjs;




use App\Http\Livewire\EmrRI\EmrRI;
use App\Http\Livewire\EmrRIPulang\EmrRIPulang;

use App\Http\Livewire\EmrRIHari\EmrRIHari;
use App\Http\Livewire\EmrRIHariPulang\EmrRIHariPulang;



use App\Http\Livewire\Laporan\LaporanRI;
use App\Http\Livewire\Laporan\LaporanRJ;
use App\Http\Livewire\Laporan\LaporanUGD;


use App\Http\Livewire\GroupingBPJS\GroupingBPJSRJ;
use App\Http\Livewire\GroupingBPJS\GroupingBPJSRI;


use App\Http\Livewire\GajiDokter\GajiDokter;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Livewire\MyAdmin\Users\Users;
use App\Http\Livewire\MyAdmin\Roles\Roles;
use App\Http\Livewire\MyAdmin\Permissions\Permissions;

// Role Group
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/MyUsers', Users::class)->name('MyUsers');
    Route::get('/MyRoles', Roles::class)->name('MyRoles');
    Route::get('/MyPermissions', Permissions::class)->name('MyPermissions');
});

Route::group(['middleware' => ['role:Admin|Mr|Perawat|Dokter|Apoteker|Tu']], function () {
    Route::get('pendaftaranMandiriPasienPoli', PendaftaranMandiriPasienPoli::class)->middleware('auth')->name('pendaftaranMandiriPasienPoli');
    // Route::view('/cetak-tiket', 'livewire.pendaftaran-mandiri-pasien-poli.cetak-tiket');


    // Master Pasien
    Route::get('MasterPasien', MasterPasien::class)->middleware('auth')->name('MasterPasien');

    // Master Poli
    Route::get('MasterPoli', MasterPoli::class)->middleware('auth')->name('MasterPoli');

    // Master Dokter
    Route::get('MasterDokter', MasterDokter::class)->middleware('auth')->name('MasterDokter');

    // Master KamarAplicares
    Route::get('MasterKamarAplicares', MasterKamarAplicares::class)->middleware('auth')->name('MasterKamarAplicares');



    // RJ
    Route::get('daftarRJ', DaftarRJ::class)->middleware('auth')->name('daftarRJ');
    Route::get('pelayananRJ', PelayananRJ::class)->middleware('auth')->name('pelayananRJ');
    Route::get('displayPelayananRJ', displayPelayananRJ::class)->middleware('auth')->name('displayPelayananRJ');
    Route::get('SetupHfisBpjs', SetupHfisBpjs::class)->middleware('auth')->name('SetupHfisBpjs');

    Route::get('RJskdp', RJskdp::class)->middleware('auth')->name('RJskdp');
    Route::get('RIskdp', RIskdp::class)->middleware('auth')->name('RIskdp');

    Route::get('EmrRJ', EmrRJ::class)->middleware('auth')->name('EmrRJ');
    Route::get('EmrRJBulan', EmrRJBulan::class)->middleware('auth')->name('EmrRJBulan');
    Route::get('EmrRJHari', EmrRJHari::class)->middleware('auth')->name('EmrRJHari');


    Route::get('BookingRJ', BookingRJ::class)->middleware('auth')->name('BookingRJ');
    Route::get('TelaahResepRJ', TelaahResepRJ::class)->middleware('auth')->name('TelaahResepRJ');
    Route::get('TelaahResepRI', TelaahResepRI::class)->middleware('auth')->name('TelaahResepRI');


    Route::get('EmrRJAdministrasi', AdministrasiRJ::class)->middleware('auth')->name('EmrRJAdministrasi');



    // UGD
    Route::get('daftarUGD', DaftarUGD::class)->middleware('auth')->name('daftarUGD');

    Route::get('EmrUGD', EmrUGD::class)->middleware('auth')->name('EmrUGD');
    Route::get('TelaahResepUGD', TelaahResepUGD::class)->middleware('auth')->name('TelaahResepUGD');





    // RI
    Route::get('daftarRI', DaftarRI::class)->middleware('auth')->name('daftarRI');
    Route::get('EmrRI', EmrRI::class)->middleware('auth')->name('EmrRI');
    Route::get('EmrRIPulang', EmrRIPulang::class)->middleware('auth')->name('EmrRIPulang');

    Route::get('EmrRIHari', EmrRIHari::class)->middleware('auth')->name('EmrRIHari');
    Route::get('EmrRIHariPulang', EmrRIHariPulang::class)->middleware('auth')->name('EmrRIHariPulang');



    //Laporan
    Route::get('LaporanRI', LaporanRI::class)->middleware('auth')->name('LaporanRI');
    Route::get('LaporanRJ', LaporanRJ::class)->middleware('auth')->name('LaporanRJ');
    Route::get('LaporanUGD', LaporanUGD::class)->middleware('auth')->name('LaporanUGD');



    //Grouping
    Route::get('GroupingBPJSRJ', GroupingBPJSRJ::class)->middleware('auth')->name('GroupingBPJSRJ');
    Route::get('GroupingBPJSRI', GroupingBPJSRI::class)->middleware('auth')->name('GroupingBPJSRI');


    //Gaji Dokter
    Route::get('GajiDokter', GajiDokter::class)->middleware('auth')->name('GajiDokter');
});











require __DIR__ . '/auth.php';
