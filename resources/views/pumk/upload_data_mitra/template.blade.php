


<html>

    <head>
    
    <style type="text/css">
        body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:x-small }
        a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
        a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
        comment { display:none;  } 
    </style>
    
    </head>
    
    <body>
    <table cellspacing="0" border="0">
    <tr>
        <td colspan="27" style="background-color : #e3e3e3 ;width:5px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">LAPORAN PENDANAAN UMK</font></b></td>
    </tr>
    <tr>
        <td colspan="27" style="background-color : #e3e3e3 ;width:5px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">{{ $perusahaan }}</font></b></td>
    </tr>
    <tr>
        <td colspan="27" style="background-color : #e3e3e3 ;width:5px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">{{ $periode }}</font></b></td>
    </tr>
    <tr></tr>
    <tr>
        <td style="background-color : #e3e3e3 ;width:5px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">No</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Nama Mitra Binaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Provinsi</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Kota</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Sektor Usaha</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">No Identitas</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Nilai Aset*</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Nilai Omset*</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">No Pinjaman</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Cara Penyaluran</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Sumber Dana</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Tgl Awal Pendanaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Tgl Jatuh Tempo</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Nominal Pendanaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Saldo Pokok Pendanaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Saldo Jasa Admin Pendanaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:35px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Penerimaan Pokok Bulan Berjalan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:40px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Penerimaan Jasa Admin Bulan Berjalan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Tgl Penerimaan Terakhir</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Kolektibilitas Pendanaan</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Kondisi Pinjaman</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Jenis Pembayaran</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">ID Bank Account</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">SDM di MB*</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Kelebihan Angsuran*</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">SubSektor*</font></b></td>
        <td style="background-color : #e3e3e3 ;width:30px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="center" valign=middle ><b><font face="Arial" size=4 color="#000000">Produk/Jasa yang dihasilkan*</font></b></td>
    </tr>

    @if(!empty($data))
        @php
            $i=1;
        @endphp
        @foreach($data as $val)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$val->nama_mitra?$val->nama_mitra:""}}</td>
            <td>{{$val->provinsi_id?$val->provinsi_id:""}}</td>
            <td>{{$val->kota_id?$val->kota_id:""}}</td>
            <td>{{$val->sektor_usaha_id?$val->sektor_usaha_id:""}}</td>
            <td>{{$val->no_identitas?$val->no_identitas:""}}</td>
            <td>{{$val->nilai_aset?$val->nilai_aset:""}}</td>
            <td>{{$val->nilai_omset?$val->nilai_omset:""}}</td>
            <td>{{$val->no_pinjaman?$val->no_pinjaman:""}}</td>
            <td>{{$val->cara_penyaluran_id?$val->cara_penyaluran_id:""}}</td>
            <td>{{$val->sumber_dana?$val->sumber_dana:""}}</td>
            <td>{{$val->tgl_awal?$val->tgl_awal:""}}</td>
            <td>{{$val->tgl_jatuh_tempo?$val->tgl_jatuh_tempo:""}}</td>
            <td>{{$val->nominal_pendanaan?$val->nominal_pendanaan:""}}</td>
            <td>{{$val->saldo_pokok_pendanaan?$val->saldo_pokok_pendanaan:""}}</td>
            <td>{{$val->saldo_jasa_adm_pendanaan?$val->saldo_jasa_adm_pendanaan:""}}</td>
            <td>{{$val->penerimaan_pokok_bulan_berjalan?$val->penerimaan_pokok_bulan_berjalan:""}}</td>
            <td>{{$val->penerimaan_jasa_adm_bulan_berjalan?$val->penerimaan_jasa_adm_bulan_berjalan:""}}</td>
            <td>{{$val->tgl_penerimaan_terakhir?$val->tgl_penerimaan_terakhir:""}}</td>
            <td>{{$val->kolektibilitas_id?$val->kolektibilitas_id:""}}</td>
            <td>{{$val->kondisi_pinjaman_id?$val->kondisi_pinjaman_id:""}}</td>
            <td>{{$val->jenis_pembayaran_id?$val->jenis_pembayaran_id:""}}</td>
            <td>{{$val->bank_account_id?$val->bank_account_id:""}}</td>
            <td>{{$val->jumlah_sdm?$val->jumlah_sdm:""}}</td>
            <td>{{$val->kelebihan_angsuran?$val->kelebihan_angsuran:""}}</td>
            <td>{{$val->subsektor?$val->subsektor:""}}</td>
            <td>{{$val->hasil_produk_jasa?$val->hasil_produk_jasa:""}}</td>
        </tr>
        @endforeach
    @endif
    
    </table>
    <!-- ************************************************************************** -->
    </body>
    
    </html>
    