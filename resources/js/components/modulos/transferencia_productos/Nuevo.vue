<template>
    <div
        class="modal fade"
        :class="{ show: bModal }"
        id="modal-default"
        aria-modal="true"
        role="dialog"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                        @click="cierraModal"
                    >
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div
                                class="form-group col-md-12"
                                v-if="accion != 'edit'"
                            >
                                <label
                                    :class="{
                                        'text-danger': errors.producto_id,
                                    }"
                                    >Seleleccionar Producto*</label
                                >

                                <el-select
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.producto_id,
                                    }"
                                    v-model="transferencia_producto.producto_id"
                                    filterable
                                    remote
                                    reserve-keyword
                                    placeholder="Buscar producto"
                                    :remote-method="buscarProducto"
                                    :loading="loading_buscador"
                                    @change="getStockProducto"
                                >
                                    <el-option
                                        v-for="item in aux_lista_productos"
                                        :key="item.id"
                                        :label="item.nombre"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.producto_id"
                                    v-text="errors.producto_id[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-12" v-else>
                                <label
                                    :class="{
                                        'text-danger': errors.producto_id,
                                    }"
                                    >Producto*</label
                                >
                                <input
                                    type="readonly"
                                    class="form-control"
                                    readonly
                                    v-model="
                                        transferencia_producto.nombre_producto
                                    "
                                />
                            </div>

                            <div class="form-group col-md-12">
                                <div class="card card-body">
                                    <div class="row text-center">
                                        <div class="col-md-12">
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.origen,
                                                }"
                                                >Seleccionar Origen*</label
                                            >
                                            <div class="form-group clearfix">
                                                <div
                                                    class="icheck-primary d-inline"
                                                >
                                                    <input
                                                        type="radio"
                                                        id="radioPrimary1"
                                                        checked=""
                                                        value="ALMACEN"
                                                        name="origen"
                                                        v-model="
                                                            transferencia_producto.origen
                                                        "
                                                        @change="
                                                            detectarOrigen();
                                                            getStockProducto();
                                                        "
                                                        :disabled="
                                                            accion == 'edit'
                                                        "
                                                    />
                                                    <label for="radioPrimary1"
                                                        >Almacén
                                                    </label>
                                                </div>
                                                <div
                                                    class="icheck-primary d-inline"
                                                >
                                                    <input
                                                        type="radio"
                                                        id="radioPrimary2"
                                                        value="SUCURSAL"
                                                        name="origen"
                                                        v-model="
                                                            transferencia_producto.origen
                                                        "
                                                        @change="
                                                            detectarOrigen();
                                                            getStockProducto();
                                                        "
                                                        :disabled="
                                                            accion == 'edit'
                                                        "
                                                    />
                                                    <label for="radioPrimary2"
                                                        >Sucursal</label
                                                    >
                                                </div>
                                            </div>
                                            <span
                                                class="error invalid-feedback"
                                                v-if="errors.origen"
                                                v-text="errors.origen[0]"
                                            ></span>
                                        </div>

                                        <div
                                            class="form-group col-md-12"
                                            v-if="
                                                transferencia_producto.origen ==
                                                'SUCURSAL'
                                            "
                                        >
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.origen_id,
                                                }"
                                                >Seleccionar Sucursal de
                                                Origen*</label
                                            >
                                            <el-select
                                                placeholder="Sucursal"
                                                class="w-100"
                                                :class="{
                                                    'is-invalid':
                                                        errors.origen_id,
                                                }"
                                                v-model="
                                                    transferencia_producto.origen_id
                                                "
                                                filterable
                                                @change="getStockProducto"
                                                :disabled="accion == 'edit'"
                                            >
                                                <el-option
                                                    v-for="item in listSucursals"
                                                    :key="item.id"
                                                    :label="item.nombre"
                                                    :value="item.id"
                                                >
                                                </el-option>
                                            </el-select>
                                            <span
                                                class="error invalid-feedback"
                                                v-if="errors.origen_id"
                                                v-text="errors.origen_id[0]"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="card card-body">
                                    <div class="row text-center">
                                        <div class="col-md-12">
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.destino,
                                                }"
                                                >Seleccionar Destino*</label
                                            >
                                            <div class="form-group clearfix">
                                                <div
                                                    class="icheck-primary d-inline"
                                                >
                                                    <input
                                                        type="radio"
                                                        id="radioPrimary3"
                                                        checked=""
                                                        value="ALMACEN"
                                                        name="destino"
                                                        v-model="
                                                            transferencia_producto.destino
                                                        "
                                                        :disabled="
                                                            transferencia_producto.origen ==
                                                                'ALMACEN' ||
                                                            accion == 'edit'
                                                        "
                                                    />
                                                    <label for="radioPrimary3"
                                                        >Almacén
                                                    </label>
                                                </div>
                                                <div
                                                    class="icheck-primary d-inline"
                                                >
                                                    <input
                                                        type="radio"
                                                        id="radioPrimary4"
                                                        value="SUCURSAL"
                                                        name="destino"
                                                        v-model="
                                                            transferencia_producto.destino
                                                        "
                                                        :disabled="
                                                            accion == 'edit'
                                                        "
                                                    />
                                                    <label for="radioPrimary4"
                                                        >Sucursal</label
                                                    >
                                                </div>
                                            </div>
                                            <span
                                                class="error invalid-feedback"
                                                v-if="errors.destino"
                                                v-text="errors.destino[0]"
                                            ></span>
                                        </div>

                                        <div
                                            class="form-group col-md-12"
                                            v-if="
                                                transferencia_producto.destino ==
                                                'SUCURSAL'
                                            "
                                        >
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.destino_id,
                                                }"
                                                >Seleccionar Sucursal de
                                                Destino*</label
                                            >
                                            <el-select
                                                placeholder="Sucursal"
                                                class="w-100"
                                                :class="{
                                                    'is-invalid':
                                                        errors.destino_id,
                                                }"
                                                v-model="
                                                    transferencia_producto.destino_id
                                                "
                                                filterable
                                                :disabled="accion == 'edit'"
                                            >
                                                <el-option
                                                    v-for="item in listSucursals"
                                                    :key="item.id"
                                                    :label="item.nombre"
                                                    :value="item.id"
                                                >
                                                </el-option>
                                            </el-select>
                                            <span
                                                class="error invalid-feedback"
                                                v-if="errors.destino_id"
                                                v-text="errors.destino_id[0]"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label
                                                    >Stock actual del Producto
                                                    (Origen):
                                                    <span
                                                        class="badge badge-primary text-md"
                                                        v-text="
                                                            aux_stock_actual
                                                        "
                                                    ></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.cantidad,
                                    }"
                                    >Cantidad a transferir*</label
                                >
                                <el-input
                                    type="number"
                                    min="0.01"
                                    placeholder="Cantidad"
                                    :class="{ 'is-invalid': errors.cantidad }"
                                    v-model="transferencia_producto.cantidad"
                                    clearable
                                    @change.native="detectaCantidad"
                                    @keyup.native="detectaCantidad"
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.cantidad"
                                    v-text="errors.cantidad[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.descripcion,
                                    }"
                                    >Descripción</label
                                >
                                <el-input
                                    type="textarea"
                                    autosize
                                    placeholder="Descripción"
                                    :class="{
                                        'is-invalid': errors.descripcion,
                                    }"
                                    v-model="transferencia_producto.descripcion"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.descripcion"
                                    v-text="errors.descripcion[0]"
                                ></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                        @click="cierraModal"
                    >
                        Cerrar
                    </button>
                    <el-button
                        type="primary"
                        class="bg-primary"
                        :loading="enviando"
                        @click="setRegistroModal()"
                        >{{ textoBoton }}</el-button
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        muestra_modal: {
            type: Boolean,
            default: false,
        },
        accion: {
            type: String,
            default: "nuevo",
        },
        transferencia_producto: {
            type: Object,
            default: {
                id: 0,
                origen: "",
                origen_id: "",
                destino: "",
                destino_id: "",
                producto_id: "",
                cantidad: "",
                descripcion: "",
            },
        },
    },
    watch: {
        muestra_modal: function (newVal, oldVal) {
            this.errors = [];
            if (newVal) {
                this.getStockProducto();
                this.bModal = true;
            } else {
                this.bModal = false;
            }
        },
    },
    computed: {
        tituloModal() {
            if (this.accion == "nuevo") {
                return "NUEVO REGISTRO";
            } else {
                return "MODIFICAR REGISTRO";
            }
        },
        textoBoton() {
            if (this.accion == "nuevo") {
                return "Registrar";
            } else {
                return "Actualizar";
            }
        },
    },
    data() {
        return {
            user: JSON.parse(localStorage.getItem("user")),
            bModal: this.muestra_modal,
            enviando: false,
            errors: [],
            listProductos: [],
            aux_lista_productos: [],
            listSucursals: [],
            loading_buscador: false,
            stock_actual: 0,
            aux_stock_actual: 0,
        };
    },
    mounted() {
        this.bModal = this.muestra_modal;
        this.getProductos();
        this.getSucursals();
    },
    methods: {
        getProductos() {
            axios.get("/admin/productos").then((response) => {
                this.listProductos = response.data.productos;
                this.aux_lista_productos = this.listProductos;
            });
        },
        getSucursals() {
            axios.get("/admin/sucursals").then((response) => {
                this.listSucursals = response.data.sucursals;
            });
        },
        getStockProducto() {
            let sw_envia = false;
            if (this.transferencia_producto.origen == "SUCURSAL") {
                if (this.transferencia_producto.origen_id != "") {
                    sw_envia = true;
                }
            }
            if (this.transferencia_producto.origen == "ALMACEN") {
                sw_envia = true;
            }
            if (
                this.transferencia_producto.producto_id != "" &&
                this.transferencia_producto.origen != "" &&
                sw_envia
            ) {
                axios
                    .get("/admin/productos/getStock", {
                        params: {
                            lugar: this.transferencia_producto.origen,
                            lugar_id: this.transferencia_producto.origen_id,
                            producto_id:
                                this.transferencia_producto.producto_id,
                        },
                    })
                    .then((response) => {
                        this.stock_actual = response.data.stock_actual;
                        if (this.accion == "edit") {
                            this.stock_actual += parseFloat(
                                this.transferencia_producto.cantidad
                            );
                            this.aux_stock_actual =
                                this.stock_actual -
                                this.transferencia_producto.cantidad;
                        } else {
                            this.aux_stock_actual = this.stock_actual;
                        }
                        if (this.transferencia_producto.cantidad != "") {
                            this.detectaCantidad();
                        }
                    });
            } else {
                this.stock_actual = 0;
                this.aux_stock_actual = 0;
            }
        },
        setRegistroModal() {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/transferencia_productos";
                let config = {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                };
                let formdata = new FormData();
                if (this.accion != "edit") {
                    formdata.append(
                        "origen",
                        this.transferencia_producto.origen
                            ? this.transferencia_producto.origen
                            : ""
                    );
                    formdata.append(
                        "origen_id",
                        this.transferencia_producto.origen_id
                            ? this.transferencia_producto.origen_id
                            : ""
                    );
                    formdata.append(
                        "destino",
                        this.transferencia_producto.destino
                            ? this.transferencia_producto.destino
                            : ""
                    );
                    formdata.append(
                        "destino_id",
                        this.transferencia_producto.destino_id
                            ? this.transferencia_producto.destino_id
                            : ""
                    );
                    formdata.append(
                        "producto_id",
                        this.transferencia_producto.producto_id
                            ? this.transferencia_producto.producto_id
                            : ""
                    );
                }
                formdata.append(
                    "cantidad",
                    this.transferencia_producto.cantidad
                        ? this.transferencia_producto.cantidad
                        : ""
                );
                formdata.append(
                    "descripcion",
                    this.transferencia_producto.descripcion
                        ? this.transferencia_producto.descripcion
                        : ""
                );

                if (this.accion == "edit") {
                    url =
                        "/admin/transferencia_productos/" +
                        this.transferencia_producto.id;
                    formdata.append("_method", "PUT");
                }
                axios
                    .post(url, formdata, config)
                    .then((res) => {
                        this.enviando = false;
                        if (res.data.sw) {
                            Swal.fire({
                                icon: "success",
                                title: res.data.msj,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                            this.limpiaTransferenciaProducto();
                            this.$emit("envioModal");
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
        // Dialog/modal
        cierraModal() {
            this.bModal = false;
            this.$emit("close");
        },
        limpiaTransferenciaProducto() {
            this.errors = [];
            this.transferencia_producto.origen = "";
            this.transferencia_producto.origen_id = "";
            this.transferencia_producto.destino = "";
            this.transferencia_producto.destino_id = "";
            this.transferencia_producto.producto_id = "";
            this.transferencia_producto.cantidad = "";
            this.transferencia_producto.descripcion = "";
        },
        buscarProducto(query) {
            if (query !== "") {
                this.loading_buscador = true;
                setTimeout(() => {
                    this.loading_buscador = false;
                    this.aux_lista_productos = this.listProductos.filter(
                        (item) => {
                            return (
                                item.codigo
                                    .toLowerCase()
                                    .includes(query.toLowerCase()) ||
                                item.nombre
                                    .toLowerCase()
                                    .includes(query.toLowerCase()) ||
                                item.medida
                                    .toLowerCase()
                                    .includes(query.toLowerCase()) ||
                                item.grupo.nombre
                                    .toLowerCase()
                                    .includes(query.toLowerCase())
                            );
                        }
                    );
                }, 200);
            } else {
                this.aux_lista_productos = this.listProductos;
            }
        },
        detectarOrigen() {
            if (this.transferencia_producto.origen == "ALMACEN") {
                this.transferencia_producto.destino = "SUCURSAL";
            }
        },
        detectaCantidad() {
            if (this.transferencia_producto.cantidad > this.stock_actual) {
                Swal.fire({
                    icon: "info",
                    title: "Atención",
                    html: `La cantidad de transferencia no puede superar al Stock disponible del Origen: ${this.stock_actual}`,
                    showConfirmButton: false,
                    timer: 2000,
                });
                this.transferencia_producto.cantidad = this.stock_actual;
                this.aux_stock_actual = 0;
            } else {
                this.aux_stock_actual =
                    this.stock_actual - this.transferencia_producto.cantidad;
            }
        },
    },
};
</script>

<style></style>
