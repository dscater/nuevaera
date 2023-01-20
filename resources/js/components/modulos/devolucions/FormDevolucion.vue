<template>
    <div class="row">
        <div class="col-md-12">
            <form>
                <div class="row">
                    <template>
                        <div
                            class="form-group col-md-12"
                            v-if="accion != 'edit'"
                        >
                            <label
                                :class="{
                                    'text-danger': errors.orden_id,
                                }"
                                >Seleccionar orden de venta</label
                            >
                            <el-select
                                class="w-full d-block"
                                :class="{
                                    'is-invalid': errors.orden_id,
                                }"
                                v-model="devolucion.orden_id"
                                clearable
                                filterable
                                @change="getOrdenVenta()"
                            >
                                <el-option
                                    v-for="item in listOrdens"
                                    :key="item.id"
                                    :value="item.id"
                                    :label="
                                        item.id + ' | ' + item.cliente.nombre
                                    "
                                >
                                </el-option>
                            </el-select>
                            <span
                                class="error invalid-feedback"
                                v-if="errors.orden_id"
                                v-text="errors.orden_id[0]"
                            ></span>
                        </div>
                    </template>
                </div>
                <div class="row">
                    <div class="col-md-12 contenedor_tabla">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th
                                        colspan="4"
                                        class="bg-blue text-md text-center"
                                    >
                                        DETALLE DE LA VENTA
                                        <i class="fa fa-list-alt"></i>
                                    </th>
                                </tr>
                                <tr>
                                    <th width="5px">N°</th>
                                    <th>Producto</th>
                                    <th width="180px">Cantidad</th>
                                    <th width="180px">Cantidad a devolver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        devolucion.devolucion_detalles.length >
                                        0
                                    "
                                    v-for="(
                                        item, index
                                    ) in devolucion.devolucion_detalles"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.producto.nombre }}</td>
                                    <td>{{ item.cantidad_detalle_orden }}</td>
                                    <td class="p-0">
                                        <input
                                            type="number"
                                            min="0.01"
                                            class="form-control"
                                            v-model="item.cantidad"
                                            @change="validaCantidad(index)"
                                            @keyup="validaCantidad(index)"
                                        />
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        devolucion.devolucion_detalles.length ==
                                        0
                                    "
                                >
                                    <td
                                        colspan="4"
                                        class="text-center text-gray font-weight-bold"
                                    >
                                        NO SE AGREGÓ NINGUN PRODUCTO
                                    </td>
                                </tr>
                            </tbody>
                            <!-- <tfoot class="bg-blue">
                                <tr>
                                    <td colspan="3">TOTAL</td>
                                    <td>{{ oOrdenVenta?.total }}</td>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer justify-content-between">
            <el-button
                type="primary"
                class="bg-lightblue"
                :loading="enviando"
                @click="enviarFormulario()"
                v-html="textoBoton"
                :disabled="devolucion.devolucion_detalles.length <= 0"
            ></el-button>

            <router-link
                :to="{ name: 'devolucions.index' }"
                class="btn btn-default btn-lg"
                ><i class="fa fa-list-alt"></i> Volver a
                devoluciones</router-link
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
        devolucion: {
            type: Object,
            default() {
                return {
                    id: 0,
                    orden_id: "",
                    devolucion_detalles: [],
                };
            },
        },
    },
    watch: {
        devolucion(newVal, oldVal) {
            if (newVal.id != 0) {
                this.getOrdens();
            }
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
            listClientes: [],
            aux_lista_productos: [],
            listProductos: [],
            listOrdens: [],
            eliminados: [],
            oOrdenVenta: null,
            oProducto: null,
            loading_buscador: false,
        };
    },
    mounted() {
        this.getOrdens();
        if (this.user.tipo != "ADMINISTRADOR") {
            this.devolucion.sucursal_id = this.user.sucursal_id;
        }
        this.iniciaSeleccionFilas();
    },
    methods: {
        // OBTENER LISTADOS E INFORMACIÓN
        getOrdens() {
            axios.get("/admin/orden_ventas").then((response) => {
                this.listOrdens = response.data.orden_ventas;
            });
        },
        getOrdenVenta() {
            if (this.devolucion.orden_id != "") {
                this.devolucion.devolucion_detalles = [];
                axios
                    .get("/admin/orden_ventas/" + this.devolucion.orden_id)
                    .then((response) => {
                        this.oOrdenVenta = response.data;
                        this.oOrdenVenta.detalle_ordens.forEach((item) => {
                            this.devolucion.devolucion_detalles.push({
                                id: 0,
                                producto: item.producto,
                                detalle_orden_id: item.id,
                                producto_id: item.producto_id,
                                sucursal_stock_id: item.sucursal_stock_id,
                                cantidad: 0,
                                cantidad_detalle_orden: item.cantidad,
                            });
                        });
                    });
            } else {
                this.devolucion.devolucion_detalles = [];
            }
        },
        getProducto() {
            if (this.producto_id != "") {
                axios
                    .get("/admin/productos/" + this.producto_id, {
                        params: {
                            id: this.devolucion.sucursal_id,
                        },
                    })
                    .then((response) => {
                        this.oProducto = response.data.producto;
                        this.oProducto["stock_actual"] =
                            response.data.stock_actual;
                    });
            } else {
                this.oProducto = null;
            }
        },
        getProductos() {
            if (this.devolucion.sucursal_id != "") {
                axios
                    .get("/admin/productos/productos_sucursal", {
                        params: {
                            id: this.devolucion.sucursal_id,
                        },
                    })
                    .then((response) => {
                        if (response.data.length > 0) {
                            this.listProductos = response.data;
                            this.aux_lista_productos = this.listProductos;
                        } else {
                            this.listProductos = [];
                            this.aux_lista_productos = [];
                            this.producto_id = "";
                        }
                    });
            } else {
                this.listProductos = [];
            }
        },
        // ENVIAR REGISTRO
        enviarFormulario() {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/devolucions";
                if (this.accion == "edit") {
                    url = "/admin/devolucions/" + this.devolucion.id;
                    this.devolucion["_method"] = "PUT";
                    this.devolucion.eliminados = this.eliminados;
                }
                axios
                    .post(url, this.devolucion)
                    .then((res) => {
                        this.enviando = false;
                        if (res.data.sw) {
                            Swal.fire({
                                icon: "success",
                                title: res.data.msj,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                            this.$emit("envioFormulario", res.data.id);
                            this.errors = [];
                            if (this.accion == "edit") {
                                this.textoBtn = "Actualizar";
                            } else {
                                this.textoBtn = "Registrar";
                            }
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
        limpiaDevolucion() {
            this.errors = [];
        },
        generaReporte() {
            this.enviando = true;
            let config = {
                responseType: "blob",
            };
            axios
                .post(
                    "/admin/devolucions/pdf/" + this.devolucion.id,
                    null,
                    config
                )
                .then((res) => {
                    this.errors = [];
                    this.enviando = false;
                    let pdfBlob = new Blob([res.data], {
                        type: "application/pdf",
                    });
                    let urlReporte = URL.createObjectURL(pdfBlob);
                    window.open(urlReporte);
                    this.enviando = false;
                })
                .catch(async (error) => {
                    let responseObj = await error.response.data.text();
                    responseObj = JSON.parse(responseObj);
                    this.enviando = false;
                    if (error.response) {
                        if (error.response.status == 422)
                            this.errors = responseObj.errors;
                    }
                    this.enviando = false;
                });
        },
        iniciaSeleccionFilas() {
            $(".detalle_ordens").on("focusin", "input", function () {
                $(this).parents("tr").addClass("seleccionado");
            });
            $(".detalle_ordens").on("focusout", "input", function () {
                $(this).parents("tr").removeClass("seleccionado");
            });
        },
        validaCantidad(index) {
            let cantidad_detalle_orden =
                this.devolucion.devolucion_detalles[index]
                    .cantidad_detalle_orden;
            let cantidad = this.devolucion.devolucion_detalles[index].cantidad;
            if (cantidad > cantidad_detalle_orden) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: `La cantidad a devolver no debe superar ${cantidad_detalle_orden}`,
                    showConfirmButton: false,
                    timer: 2500,
                });
                this.devolucion.devolucion_detalles[index].cantidad =
                    cantidad_detalle_orden;
            }
        },
    },
};
</script>

<style>
.devolucion_detalles tbody tr td {
    padding: 0px;
    vertical-align: middle;
}

.devolucion_detalles tbody tr td:nth-child(1) {
    padding-left: 5px;
}

.contenedor_tabla {
    overflow: auto;
}
</style>
