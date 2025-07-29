<script src="../js/console_choferes.js?rev=<?php echo time(); ?>"></script>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><b>MANTENIMIENTO DE CONDUCTORES</b></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">MENU</a></li>
                    <li class="breadcrumb-item active">CONDUCTORES</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- /.col-md-6 -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;<b>Listado de Conductores</b></h3>
                        <button class="btn btn-success float-right" onclick="AbrirRegistro()"><i class="fas fa-plus"></i> Nuevo Registro</button>
                    </div>
                    <div class="table-responsive" style="text-align:center">
                        <div class="card-body">
                            <table id="tabla_choferes" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#023D77;color:#FFFFFF; ">
                                    <tr>
                                        <th style="text-align:center">Nro.</th>
                                        <th style="text-align:center">Documento de Identidad</th>
                                        <th style="text-align:center">Foto</th>
                                        <th style="text-align:center">Nombre y Apellidos</th>
                                        <th style="text-align:center">Celular</th>
                                        <th style="text-align:center">Procedencia</th>
                                        <th style="text-align:center">Dirección</th>
                                        <th style="text-align:center">Vehículo</th>
                                        <th style="text-align:center">Placa</th>
                                        <th style="text-align:center">Fecha de registro</th>
                                        <th style="text-align:center">Estado</th>
                                        <th style="text-align:center">Acción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    <div class="modal fade" id="modal_registro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>REGISTRO DE CONDUCTOR</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>
                        <div class="col-6 form-group">
                            <label for="" style="font-size:small;">Tipo de documento<b style="color:red">(*)</b>:</label>
                            <select class="form-control" id="select_tipo_documento" style="width:100%">
                                <option value="" disabled>Seleccione</option>
                                <option value="DNI" selected>DNI</option>
                                <option value="CARNET DE EXTRANJERIA">CARNET DE EXTRANJERIA</option>
                                <option value="PASAPORTE">PASAPORTE</option>
                            </select>
                        </div>

                        <div id="dni_section" class="col-6 form-group">
                            <label for="" style="font-size:small;">N° Documento<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni" maxlength="8" onkeypress="return soloNumeros(event)">
                                <div class="input-group-append">

                                    <button onclick="" class="btn btn-primary" id="prueba"><i class="fa fa-search"></i><b> Reniec</b></button>
                                </div>
                            </div>
                        </div>
                        <div id="otros_documentos_section" class="col-6 form-group" style="display: none;">
                            <label for="" style="font-size:small;">N° Documento<b style="color:red">(*)</b>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="txt_dni2">
                                <div class="input-group-append">
                         
                                </div>
                            </div>
                        </div>
                        <div class="col-12 form-group">
                            <label for="">Nombres y apellidos<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_nomb" placeholder="Ingrese los nombres y apellidos" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Celular 1<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_apelli" placeholder="Ingrese el primer celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Celular 2(Opcional):</label>
                            <input type="email" class="form-control" id="txt_correo" placeholder="Ingrese el segundo celular" onkeypress="return soloNumeros(event)" maxlenght="9">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Procedencia(Opcional):</label>
                            <input type="text" class="form-control" id="txt_procedencia" placeholder="Ingrese la procedencia del chofer" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-8 form-group">
                            <label for="">Dirección(Opcional):</label>
                            <textarea class="form-control" id="txt_direc" rows="2" style="resize:none" placeholder="Ingrese la dirección"></textarea>
                        </div>
                        <div class="col-6">
                            <label for="txt_foto">Subir Foto de conductor (Opcional):</label>
                            <div class="custom-file d-flex align-items-center" style="position: relative;">
                                <input type="file" class="custom-file-input" id="txt_foto" accept="image/*" onchange="previewImage(event)">
                                <label class="custom-file-label" for="txt_foto" id="label_txt_foto">Seleccione Foto...</label>
                                <button type="button" class="btn btn-danger btn-sm"
                                    id="btn_clear_foto" onclick="clearPhoto()"
                                    style="position: absolute; right: 80px; top: 50%; transform: translateY(-50%); z-index: 10;">
                                    X
                                </button>
                            </div>
                        </div>

                        <div class="col-6" align="center" style="border: 2px solid black; padding: 10px; display: inline-block;">
                            <img id="preview" src="#" alt="Vista previa" style="max-width: 100%; max-height: 150px; display: none;">
                        </div>


                        <div class="col-12"><br>
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE VEHICULO</b></li>
                        </div><br>
                        <div class="col-6 form-group"><br>
                            <label for="">Marca del vehiculo<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_marca" placeholder="Ingrese la marca del vehiculo" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-6 form-group"><br>
                            <label for="">Placa del vehiculo<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_placa" placeholder="Ingrese la placa del vehiculo" maxlenght="7">
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Clase y categoria de licencia<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_clase_categoria" placeholder="Ingrese la clase y categoria">
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Nro. de Licencia<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_nro_licencia" placeholder="Ingrese el Nro. de licencia">
                        </div>
                         <div class="col-6 form-group">
                            <label for="">Fecha de expiración de licencia<b style="color:red">(*)</b>:</label>
                            <input type="date" class="form-control" id="txt_fecha_expira">
                        </div>
                         <div class="col-6 form-group">
                            <label for="">Fecha de creación de registro<b style="color:red">(*)</b>:</label>
                            <input type="date" class="form-control" id="txt_fecha_creacion" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Registrar_Usuario()"><i class="fas fa-save"></i> Registrar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#1FA0E0;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>MODIFICAR DE USUARIO</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">DNI<b style="color:red">(*)</b>:</label>
                            <input type="text" id="id_usuario" hidden>
                            <input type="text" class="form-control" id="txt_dni_editar" placeholder="Ingrese el DNI del usuario" onkeypress="return soloNumeros(event)" maxlenght="8">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Nombres<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_nomb_editar" placeholder="Ingrese los nombres" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Apellidos<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_apelli_editar" placeholder="Ingrese los apellidos" onkeypress="return sololetras(event)">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Correo electronico<b style="color:red">(*)</b>:</label>
                            <input type="email" class="form-control" id="txt_correo_editar" placeholder="Ingrese el correo electronico">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Teléfono o Celular:</label>
                            <input type="text" class="form-control" id="txt_tele_editar" placeholder="Ingrese el teléfono o celular" onkeypress="return soloNumeros(event)">
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Dirección<b style="color:red">(*)</b>:</label>
                            <textarea class="form-control" id="txt_direc_editar" rows="2" style="resize:none" placeholder="Ingrese la dirección"></textarea>
                        </div>
                        <div class="col-6">
                            <input type="text" id="txt_foto_actual" hidden>

                            <label for="txt_foto_editar">Subir Foto <b style="color:red">*</b>:</label>
                            <div class="custom-file d-flex align-items-center" style="position: relative;">
                                <input type="file" class="custom-file-input" id="txt_foto_editar" accept="image/*" onchange="previewImage2(event)">
                                <label class="custom-file-label" for="txt_foto_editar" id="label_txt_foto_editar">Seleccione Foto...</label>
                                <button type="button" class="btn btn-danger btn-sm"
                                    id="btn_clear_foto_editar" onclick="clearPhoto2()"
                                    style="position: absolute; right: 80px; top: 50%; transform: translateY(-50%); z-index: 10;">
                                    X
                                </button>
                            </div>
                        </div>

                        <div class="col-6 text-center" style="border: 2px solid black; padding: 10px; display: flex; justify-content: center; align-items: center;">
                            <img id="preview2" src="#" alt="Vista previa" style="max-width: 100%; max-height: 150px; display: none; object-fit: contain;">
                        </div>



                        <div class="col-12"><br>
                            <li class="header text-center" style="color:#FFFFFF;background-color:Black;"><b>DATOS DE ACCESO PARA EL SISTEMA</b></li>
                        </div><br>
                        <div class="col-12 form-group"><br>
                            <label for="">Usuario<b style="color:red">(*)</b>:</label>
                            <input type="text" class="form-control" id="txt_usu_editar" placeholder="Ingrese el usuario">
                        </div>

                        <div class="col-6 form-group">
                            <label for="">Rol<b style="color:red">(*)</b>:</label>
                            <select type="text" class="js-example-basic-single" id="txt_roles_editar" style="width:100%"></select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Sucursal<b style="color:red">(*)</b>:</label>
                            <select type="text" class="js-example-basic-single" id="txt_sucursal_editar" style="width:100%"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Modificar_Usuario()"><i class="fas fa-edit"></i> Módificar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal_contra" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#005CA5;">
                    <h5 class="modal-title" id="exampleModalLabel" style="color:white; text-align:center"><b>CAMBIAR CONTRASEÑA</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 form-group" style="color:red">
                            <h6><b>Campos Obligatorios (*)</b></h6>
                        </div>
                        <div class="col-12">
                            <input type="text" id="txt_idusuario_contra" hidden>
                            <label for="">Contraseña Nueva(*):</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="txt_contra_nueva">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('txt_contra_nueva', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times ml-1"></i> Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="Modificar_Contra()"><i class="fas fa-check"></i> Modificar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.content -->
    <script>
        $(document).ready(function() {
            listar_choferes();
            $('.js-example-basic-single').select2();


        });
        $('#modal_registro').on('shown.bs.modal', function() {
            $('#txt_usu').trigger('focus')
        })


        var input = document.getElementById('txt_dni');
        input.addEventListener('input', function() {
            if (this.value.length > 8)
                this.value = this.value.slice(0, 8);
        })

        var input = document.getElementById('txt_dni_editar');
        input.addEventListener('input', function() {
            if (this.value.length > 8)
                this.value = this.value.slice(0, 8);
        })
         //TRAER FECHA ACTUAL
        var n = new Date();
        var y = n.getFullYear();
        var m = n.getMonth() + 1; // Los meses empiezan desde 0, por eso se suma 1
        var d = n.getDate();

        // Si el día o el mes es menor a 10, se le agrega un '0' al inicio
        if (d < 10) {
            d = '0' + d;
        }
        if (m < 10) {
            m = '0' + m;
        }

        // Establece el valor del campo de fecha con el formato YYYY-MM-DD
        document.getElementById('txt_fecha_creacion').value = y + "-" + m + "-" + d;
    </script>
    <script>
        function previewImage(event) {
            var input = event.target;
            var preview = document.getElementById('preview');
            var label = input.nextElementSibling;

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function() {
                    preview.src = reader.result;
                    preview.style.display = 'block'; // Mostrar la vista previa
                };
                reader.readAsDataURL(input.files[0]);

                // Actualizar el label con el nombre del archivo seleccionado
                var fileName = input.files[0].name;
                label.innerHTML = "Subir Foto (" + fileName + ")";
            }
        }

        function clearPhoto() {
            var fileInput = document.getElementById('txt_foto');
            var fileLabel = document.getElementById('label_txt_foto');
            var preview = document.getElementById('preview');

            // Limpiar el input de archivo
            fileInput.value = ''; // Elimina el archivo seleccionado

            // Restablecer el texto del label
            fileLabel.innerHTML = "Seleccione Foto...";

            // Ocultar la vista previa
            preview.src = '#';
            preview.style.display = 'none';
        }
    </script>

    <script>
        function previewImage2(event) {
            const input = event.target;
            const preview2 = document.getElementById('preview2');
            const label = document.getElementById('label_txt_foto_editar');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview2.src = e.target.result; // Cargar la imagen en el src del preview
                    preview2.style.display = 'block'; // Mostrar la vista previa
                };
                reader.readAsDataURL(input.files[0]);

                // Actualizar el label con el nombre del archivo seleccionado
                label.textContent = `Subir Foto (${input.files[0].name})`;
            } else {
                clearPhoto2(); // Si no hay archivo seleccionado, limpiar la vista previa
            }
        }

        function clearPhoto2() {
            const fileInput = document.getElementById('txt_foto_editar');
            const fileLabel = document.getElementById('label_txt_foto_editar');
            const preview2 = document.getElementById('preview2');

            // Limpiar el input de archivo
            fileInput.value = ''; // Elimina el archivo seleccionado

            // Restablecer el texto del label
            fileLabel.textContent = "Seleccione Foto...";

            // Ocultar la vista previa
            preview2.src = '#';
            preview2.style.display = 'none';
        }
         // Mostrar la sección correcta al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            const selectTipoDocumento = document.getElementById('select_tipo_documento');
            const dniSection = document.getElementById('dni_section');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section');

            if (selectTipoDocumento.value === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            }
        });

        // Cambiar la visibilidad según la selección del usuario
        document.getElementById('select_tipo_documento').addEventListener('change', function() {
            const selectedValue = this.value;
            const dniSection = document.getElementById('dni_section');
            const otrosDocumentosSection = document.getElementById('otros_documentos_section');

            if (selectedValue === 'DNI') {
                dniSection.style.display = 'block';
                otrosDocumentosSection.style.display = 'none';
            } else if (selectedValue === 'CARNET DE EXTRANJERIA' || selectedValue === 'PASAPORTE') {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'block';
            } else {
                dniSection.style.display = 'none';
                otrosDocumentosSection.style.display = 'none';
            }
        });
        txt_dni.focus();
        $(document).ready(function() {
            $('#txt_dni').change(function() {
                valor = $(this).val();
                $('#dni').val(valor);
            })
        })
        var input = document.getElementById("txt_dni");

        input.addEventListener("keyup", function(event) {
            // Usamos 'event.key' para obtener la tecla presionada
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("prueba").click();
            }
        });
$("#prueba").click(function() {
    var dni = $("#txt_dni").val();

    $.ajax({
        type: "POST",
        url: "consulta-dni-ajax.php",
        data: { dni: dni },
        dataType: 'json',
        success: function(data) {
            if (data == 1) {
                alert('El DNI tiene que tener 8 dígitos');
            } else if (data.error) {
                alert('Error en la consulta: ' + data.error);
            } else {
                $("#txt_nomb").val(
                    data.first_name + ' ' + data.first_last_name + ' ' + data.second_last_name
                );
            }
        }
    });
});


    </script>