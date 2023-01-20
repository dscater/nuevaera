<template>
    <div class="row">
        <div class="col-md-12">
            <form>
                <div class="row">
                    <div class="form-group col-md-12" v-if="accion != 'edit'">
                        <label
                            :class="{
                                'text-danger': errors.lugar,
                            }"
                            >Seleccionar sucursal</label
                        >
                        <el-select
                            class="w-full d-block"
                            :class="{
                                'is-invalid': errors.lugar,
                            }"
                            v-model="importacion_apertura.lugar"
                            clearable
                            :disabled="muestra_importacion"
                        >
                            <el-option
                                :key="'ALMACEN'"
                                :value="'ALMACEN'"
                                :label="'ALMACEN'"
                                v-if="!existe_importacion_almacen"
                            >
                            </el-option>
                            <el-option
                                v-for="item in listSucursales"
                                :key="item.id"
                                :value="item.id"
                                :label="item.nombre"
                            >
                            </el-option>
                        </el-select>
                        <span
                            class="error invalid-feedback"
                            v-if="errors.lugar"
                            v-text="errors.lugar[0]"
                        ></span>
                    </div>
                </div>
                <div class="row" v-if="!muestra_importacion">
                    <div class="col-md-12">
                        <el-button
                            type="primary"
                            class="bg-lightblue btn-block btn-flat"
                            :loading="enviando"
                            @click="iniciarImportación()"
                            :disabled="this.importacion_apertura.lugar == ''"
                            >INICIAR IMPORTACIÓN DE APERTURA</el-button
                        >
                    </div>
                </div>
                <div
                    class="row"
                    v-if="muestra_importacion"
                    style="height: 90vh; overflow: auto"
                >
                    <div class="col-md-12 contenedor_tabla_productos">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>
                                    <th>GRUPO</th>
                                    <th>STOCK</th>
                                </tr>
                            </thead>
                            <tbody id="contenedor_productos"
                                
                            >
                                <tr
                                    v-for="(item, index) in listProductos"
                                    :key="item.id"
                                >
                                    <td>{{ item.nombre }}</td>
                                    <td>{{ item.grupo.nombre }}</td>
                                    <td>
                                        <input
                                            type="number"
                                            class="form-control input_stock_importacion"
                                            v-model="item.cantidad"
                                            @change="
                                                enviaCantidad(
                                                    $event,
                                                    index,
                                                    item.id
                                                )
                                            "
                                            @keyup="
                                                enviaCantidad(
                                                    $event,
                                                    index,
                                                    item.id
                                                )
                                            "
                                        />
                                    </td>
                                </tr>
                                <infinite-loading
                                    @infinite="infiniteHandler"
                                    ref="infiniteLoading"
                                >
                                    <div slot="no-more">--</div>
                                    <div slot="spinner">Cargando...</div>
                                    <div slot="no-results">Sin resultados</div>
                                </infinite-loading>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer justify-content-between">
            <router-link
                :to="{ name: 'importacion_aperturas.index' }"
                class="btn btn-default btn-lg"
                ><i class="fa fa-list-alt"></i> Volver a importacion de
                aperturas</router-link
            >
        </div>
    </div>
</template>

<script>
export default {
    props: {
        accion: {
            type: String,
            default: "nuevo",
        },
    },
    computed: {
        textoBoton() {
            if (this.accion == "nuevo") {
                return '<i class="fa fa-save"></i> Registrar';
            } else {
                return '<i class="fa fa-edit"></i> Actualizar';
            }
        },
    },
    data() {
        return {
            user: JSON.parse(localStorage.getItem("user")),
            enviando: false,
            producto_id: "",
            cantidad: 1,
            errors: [],
            listSucursales: [],
            importacion_apertura: {
                lugar: "",
            },
            listProductos: [],
            muestra_importacion: false,
            setTimeEnvios: {},
            existe_importacion_almacen: false,
            page: 0,
        };
    },
    mounted() {
        this.getSucursales();
        if (this.user.tipo != "ADMINISTRADOR") {
            this.importacion_apertura.sucursal_id = this.user.sucursal_id;
        }
    },
    methods: {
        // OBTENER LISTADOS E INFORMACIÓN
        getSucursales() {
            axios.get("/admin/sucursals/sin_importacion").then((response) => {
                this.listSucursales = response.data.sucursals;
                this.existe_importacion_almacen = response.data.almacen;
            });
        },
        infiniteHandler($state) {
            try {
                this.page = this.page + 1;
                axios
                    .get("/admin/productos/paginado", {
                        params: { page: this.page },
                    })
                    .then((response) => {
                        let nuevos_datos = response.data.productos.data;
                        if (nuevos_datos.length) {
                            this.listProductos =
                                this.listProductos.concat(nuevos_datos);
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    });
            } catch (e) {
                console.error(e);
                $state.complete();
            }
        },
        iniciarImportación() {
            let lugar = "ALMACEN";
            if (this.importacion_apertura.lugar != "ALMACEN") {
                lugar = this.listSucursales.filter((item) => {
                    return item.id == this.importacion_apertura.lugar;
                })[0].nombre;
            }

            Swal.fire({
                title: "¿Iniciar importación de apertura?",
                html: `<div class="alert alert-danger">Esta acción solo se podrá realizar esta única vez</div><div class="text-lg"><strong>Lugar: </strong> ${lugar}</div>`,
                showCancelButton: true,
                confirmButtonColor: "#05568e",
                confirmButtonText: "Iniciar apertura",
                cancelButtonText: "Cancelar",
                denyButtonText: `Cancelar`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    try {
                        let url = "/admin/importacion_aperturas";
                        axios
                            .post(url, this.importacion_apertura)
                            .then((res) => {
                                this.enviando = false;
                                if (res.data.sw) {
                                    Swal.fire({
                                        icon: "success",
                                        title: res.data.msj,
                                        showConfirmButton: false,
                                        timer: 2000,
                                    });
                                    setTimeout(() => {
                                        this.muestra_importacion = true;
                                    }, 500);
                                    this.errors = [];
                                } else {
                                    Swal.fire({
                                        icon: "info",
                                        title: "Atención",
                                        html: res.data.msj,
                                        showConfirmButton: false,
                                        timer: 2000,
                                    });
                                }
                            })
                            .catch((error) => {
                                this.enviando = false;
                                if (error.response) {
                                    if (error.response.status === 422) {
                                        this.errors =
                                            error.response.data.errors;
                                    }
                                    if (
                                        error.response.status === 420 ||
                                        error.response.status === 419 ||
                                        error.response.status === 401
                                    ) {
                                        window.location = "/";
                                    }
                                    if (error.response.status === 500) {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            html: error.response.data.message,
                                            showConfirmButton: false,
                                            timer: 2000,
                                        });
                                    }
                                }
                            });
                    } catch (e) {
                        this.enviando = false;
                        console.log(e);
                    }
                }
            });
        },
        enviaCantidad(event, index, id) {
            let cantidad = 0;
            clearTimeout(this.setTimeEnvios[index]);
            if (event.key == "Tab") return false;
            if (event.target.value != "") {
                cantidad = event.target.value;
            }
            let self = this;
            this.setTimeEnvios[index] = setTimeout(function () {
                self.actualizaStock(cantidad, id);
            }, 700);
        },
        // ENVIAR REGISTRO
        actualizaStock(cantidad, id) {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/importacion_aperturas/actualiza_stock";
                if (this.accion == "edit") {
                    url =
                        "/admin/importacion_aperturas/" +
                        this.importacion_apertura.id;
                    this.importacion_apertura["_method"] = "PUT";
                    this.importacion_apertura.eliminados = this.eliminados;
                }
                axios
                    .post(url, {
                        id: id,
                        lugar: this.importacion_apertura.lugar,
                        cantidad: cantidad,
                    })
                    .then((res) => {
                        this.enviando = false;
                        if (res.data.sw) {
                            // Swal.fire({
                            //     icon: "success",
                            //     title: res.data.msj,
                            //     showConfirmButton: false,
                            //     timer: 2000,
                            // });
                            this.errors = [];
                        } else {
                            Swal.fire({
                                icon: "info",
                                title: "Atención",
                                html: res.data.msj,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                        }
                    })
                    .catch((error) => {
                        this.enviando = false;
                        if (this.accion == "edit") {
                            this.textoBtn = "Actualizar";
                        } else {
                            this.textoBtn = "Registrar";
                        }
                        if (error.response) {
                            if (error.response.status === 422) {
                                this.errors = error.response.data.errors;
                            }
                            if (
                                error.response.status === 420 ||
                                error.response.status === 419 ||
                                error.response.status === 401
                            ) {
                                window.location = "/";
                            }
                            if (error.response.status === 500) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    html: error.response.data.message,
                                    showConfirmButton: false,
                                    timer: 2000,
                                });
                            }
                        }
                    });
            } catch (e) {
                this.enviando = false;
                console.log(e);
            }
        },
        limpiaImportacionApertura() {
            this.errors = [];
        },
        loadMore() {},
    },
};
</script>

<style>
.importacion_apertura_detalles tbody tr td {
    padding: 0px;
    vertical-align: middle;
}

.importacion_apertura_detalles tbody tr td:nth-child(1) {
    padding-left: 5px;
}

.contenedor_tabla_productos {
    overflow: auto;
}

.contenedor_tabla_productos table {
    height: 40px !important;
    max-height: 40px !important;
}

.input_stock_importacion {
    min-width: 120px !important;
}

#contenedor_productos {
    height: 40px !important;
    max-height: 40px !important;
    overflow: auto;
}
</style>
