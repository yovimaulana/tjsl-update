<form class="kt-form kt-form--label-right" method="POST" id="form-edit">
    @csrf
    <input type="hidden" name="id" id="id" readonly="readonly"
        value="{{ $actionform == 'update' ? (int) $data->id : null }}" />
    <input type="hidden" name="actionform" id="actionform" readonly="readonly" value="{{ $actionform }}" />

    <div class="form-group mb-4 row">
        <div class="col-lg-6">
            <label>Nama</label>
            <input type="text" class="form-control" name="nama" id="nama"
                value="{{ !empty(old('nama')) ? old('nama') : ($actionform == 'update' && $data->nama != '' ? $data->nama : old('nama')) }}"
                required />
        </div>
        <div class="col-lg-6">
            <label>Keterangan</label>
            <input type="text" class="form-control" name="keterangan" id="keterangan"
                value="{{ !empty(old('keterangan')) ? old('keterangan') : ($actionform == 'update' && $data->keterangan != '' ? $data->keterangan : old('keterangan')) }}" />
        </div>
    </div>
    <div class="form-group mb-4 row">
        <div class="col-lg-6">
            <label>Status</label>
            <div class="d-flex mt-2">
                <div class="form-check mr-3 me-4">
                    <input class="form-check-input" type="radio" name="is_active" id="option1" value="aktif"
                        {{ $data->is_active ? 'checked' : '' }} required>
                    <label class="form-check-label" for="option1">Aktif</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_active" id="option2" value="tidak aktif"
                        {{ !$data->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="option2">Tidak Aktif</label>
                </div>
            </div>
        </div>

    </div>
    <div class="text-center pt-15">
        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal"
            data-kt-roles-modal-action="cancel">Discard</button>
        <button id="submit" type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
            <span class="indicator-label">Submit</span>
            <span class="indicator-progress">Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        </button>
    </div>
</form>

<script type="text/javascript">
    var title = "{{ $actionform == 'update' ? 'Update' : 'Tambah' }}" + " {{ $pagetitle }}";

    $(document).ready(function() {
        $('.modal-title').html(title);
        $('.modal').on('shown.bs.modal', function() {
            setFormValidate();
        });
    });

    function setFormValidate() {
        $('#form-edit').validate({
            rules: {
                nama: {
                    required: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                nama: {
                    required: "Nama wajib diinput"
                },
                status: {
                    required: "Status wajib diinput"
                }
            },
            highlight: function(element) {
                $(element).closest('.form-control').addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).closest('.form-control').removeClass('is-invalid');
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            errorPlacement: function(error, element) {
                if (element.parent('.validated').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var typesubmit = $("input[type=submit][clicked=true]").val();

                $(form).ajaxSubmit({
                    type: 'post',
                    url: urlstore,
                    data: {
                        source: typesubmit
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $.blockUI({
                            theme: true,
                            baseZ: 2000
                        })
                    },
                    success: function(data) {
                        $.unblockUI();

                        swal.fire({
                            title: data.title,
                            html: data.msg,
                            icon: data.flag,

                            buttonsStyling: true,

                            confirmButtonText: "<i class='flaticon2-checkmark'></i> OK",
                        });

                        if (data.flag == 'success') {
                            $('#winform').modal('hide');
                            datatable.ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        $.unblockUI();
                        var msgerror = '';
                        if (jqXHR.status === 0) {
                            msgerror = 'jaringan tidak terkoneksi.';
                        } else if (jqXHR.status == 404) {
                            msgerror = 'Halaman tidak ditemukan. [404]';
                        } else if (jqXHR.status == 500) {
                            msgerror = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msgerror = 'Requested JSON parse gagal.';
                        } else if (exception === 'timeout') {
                            msgerror = 'RTO.';
                        } else if (exception === 'abort') {
                            msgerror = 'Gagal request ajax.';
                        } else {
                            msgerror = 'Error.\n' + jqXHR.responseText;
                        }
                        swal.fire({
                            title: "Error System",
                            html: msgerror + ', coba ulangi kembali !!!',
                            icon: 'error',

                            buttonsStyling: true,

                            confirmButtonText: "<i class='flaticon2-checkmark'></i> OK",
                        });
                    }
                });
                return false;
            }
        });
    }
</script>
