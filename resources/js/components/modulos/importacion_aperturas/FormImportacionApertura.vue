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
                            >Seleccionar lugar</label
                        >
                        <el-select
                            class="w-full d-block"
                            :class="{
                                'is-invalid': errors.lugar,
                            }"
                            v-model="importacion_apertura.lugar"
                            @change="detectaImportacion"
                            clearable
                        >
                            <el-option
                                v-for="(item, index) in ['ALMACEN', 'SUCURSAL']"
                                :key="index"
                                :value="item"
                                :label="item"
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
                <div
                    class="row"
                    v-if="
                        importacion_apertura.cambio_stock == 1 &&
                        importacion_apertura.lugar != ''
                    "
                >
                    <div class="col-md-12">
                        <el-button
                            type="warning"
                            class="bg-warning btn-block btn-flat"
                            :loading="enviando"
                            @click="finalizarImportacion()"
                            >FINALIZAR IMPORTACIÓN</el-button
                        >
                    </div>
                </div>
                <template v-else>
                    <div
                        class="row"
                        v-if="importacion_apertura?.cambio_stock == 0"
                    >
                        <div
                            class="col-md-12 text-center text-gray font-weight-bold text-lg"
                        >
                            IMPORTACIÓN FINALIZADA
                        </div>
                    </div>
                </template>

                <div class="row" v-if="muestra_importacion">
                    <div class="col-md-12 contenedor_tabla_productos">
                        <table class="table table-striped tabla_importacion">
                            <thead>
                                <tr>
                                    <th>GRUPO</th>
                                    <th>MEDIDA</th>
                                    <th>PRODUCTO</th>
                                    <th>STOCK</th>
                                </tr>
                            </thead>
                            <tbody id="contenedor_productos">
                                <Fila
                                    v-for="(item, index) in listProductos"
                                    :key="index"
                                    :o_importacion_apertura="
                                        importacion_apertura
                                    "
                                    :producto="item"
                                ></Fila>
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="cargando col-md-12 text-center"
                        v-if="!fin_registros"
                    >
                        <i class="fa fa-spinner fa-spin text-lg"></i>
                    </div>
                    <div class="cargando col-md-12 text-center" v-else>
                        <span class="text-lg">-- Fin de registros --</span>
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
import Fila from "./Fila.vue";
export default {
    components: {
        Fila,
    },
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
            importacion_apertura: {
                id: 0,
                lugar: "",
                cambio_stock: 1,
            },
            listProductos: [],
            muestra_importacion: false,
            existe_importacion_almacen: false,
            page: 1,
            sw_scroll: true,
            fin_registros: false,
        };
    },
    mounted() {
        window.addEventListener("scroll", this.handleScroll);
    },
    methods: {
        detectaImportacion() {
            window.addEventListener("scroll", this.handleScroll);
            this.page = 1;
            this.listProductos = [];
            axios
                .get("/admin/importacion_aperturas/verifica_importacion", {
                    params: {
                        lugar: this.importacion_apertura.lugar,
                    },
                })
                .then((response) => {
                    this.muestra_importacion = response.data.sw;
                    this.importacion_apertura =
                        response.data.importacion_apertura;
                    if (this.muestra_importacion) {
                        this.cargarProductos();
                    }
                });
        },
        // OBTENER LISTADOS E INFORMACIÓN
        cargarProductos() {
            try {
                axios
                    .get("/admin/productos/paginado", {
                        params: {
                            page: this.page,
                            importacion: true,
                            lugar: this.importacion_apertura.lugar,
                        },
                    })
                    .then((response) => {
                        let nuevos_datos = response.data.productos.data;
                        if (nuevos_datos.length) {
                            this.listProductos =
                                this.listProductos.concat(nuevos_datos);
                            this.sw_scroll = false;
                            this.fin_registros = false;
                        } else {
                            window.removeEventListener(
                                "scroll",
                                this.handleScroll
                            );
                            this.fin_registros = true;
                        }
                    });
            } catch (e) {
                console.error(e);
                $state.complete();
            }
        },
        finalizarImportacion() {
            Swal.fire({
                title: "¿Finalizar importación de apertura?",
                html: `<strong>Lugar: </strong> ${this.importacion_apertura.lugar}`,
                showCancelButton: true,
                confirmButtonColor: "#ffc107",
                confirmButtonText: "Si, finalizar",
                cancelButtonText: "Cancelar",
                denyButtonText: `Cancelar`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    try {
                        let url =
                            "/admin/importacion_aperturas/" +
                            this.importacion_apertura.id;
                        this.importacion_apertura["_method"] = "put";
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
                                    this.importacion_apertura =
                                        res.data.importacion_apertura;
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
        limpiaImportacionApertura() {
            this.errors = [];
        },
        handleScroll(e) {
            if (!this.fin_registros) {
                this.sw_scroll = true;
                if (
                    $(window).scrollTop() + $(window).height() >=
                    $(document).height() - 100
                ) {
                    if (this.sw_scroll) {
                        this.page++;
                        this.cargarProductos();
                    }
                }
                $(window).scroll(function () {
                    if (
                        $(window).scrollTop() + $(window).height() >=
                        $(document).height() - 100
                    ) {
                        if (this.sw_scroll) {
                            this.page++;
                            this.cargarProductos();
                        }
                    }
                });
            } else {
                window.removeEventListener("scroll", this.handleScroll);
            }
        },
    },
    destroyed: function () {
        window.removeEventListener("scroll", this.handleScroll);
    },
};
</script>

<style>
.contenedor_tabla_productos {
    overflow: auto;
}
.input_stock_importacion {
    min-width: 100px !important;
}
@media (max-width: 780px) {
    .tabla_importacion thead {
        display: none;
    }

    .tabla_importacion.table-striped tbody tr td {
        display: block !important;
    }
    .tabla_importacion.table-striped tbody tr td:before {
        content: attr(data-col);
        font-weight: bold;
    }
}
</style>
