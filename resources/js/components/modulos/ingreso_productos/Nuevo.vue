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
                <div class="modal-header bg-warning">
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
                                        'text-danger': errors.lugar,
                                    }"
                                    >Seleccionar lugar*</label
                                >
                                <el-select
                                    placeholder="Proveedor"
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.lugar,
                                    }"
                                    v-model="ingreso_producto.lugar"
                                    filterable
                                >
                                    <el-option
                                        v-for="(item, index) in [
                                            'ALMACEN',
                                            'SUCURSAL',
                                        ]"
                                        :key="index"
                                        :label="item"
                                        :value="item"
                                    >
                                    </el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.lugar"
                                    v-text="errors.lugar[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-12" v-else>
                                <label
                                    :class="{
                                        'text-danger': errors.lugar,
                                    }"
                                    >Lugar*</label
                                >
                                <input
                                    type="readonly"
                                    class="form-control"
                                    readonly
                                    v-model="ingreso_producto.lugar"
                                />
                            </div>
                            <div
                                class="form-group col-md-12"
                                v-if="accion != 'edit'"
                            >
                                <div
                                    class="text-center form-group clearfix mb-0 mt-0"
                                >
                                    <label
                                        >Ajustar busqueda <br /><small
                                            ><i
                                                >Realizará la busqueda
                                                exactamente por la columna
                                                seleccionada</i
                                            ></small
                                        ></label
                                    >
                                </div>
                                <div
                                    class="text-center form-group clearfix mb-1"
                                >
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="radio"
                                            id="radioPrimary5"
                                            name="sw_busqueda"
                                            value="todos"
                                            v-model="sw_busqueda"
                                            @change="
                                                aux_lista_productos = [];
                                                ingreso_producto.producto_id =
                                                    '';
                                            "
                                            checked=""
                                        />
                                        <label for="radioPrimary5">
                                            Todos
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="radio"
                                            id="radioPrimary6"
                                            name="sw_busqueda"
                                            value="codigo"
                                            v-model="sw_busqueda"
                                            @change="
                                                aux_lista_productos = [];
                                                ingreso_producto.producto_id =
                                                    '';
                                            "
                                        />
                                        <label for="radioPrimary6">
                                            Código
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="radio"
                                            id="radioPrimary7"
                                            name="sw_busqueda"
                                            value="medida"
                                            v-model="sw_busqueda"
                                            @change="
                                                aux_lista_productos = [];
                                                ingreso_producto.producto_id =
                                                    '';
                                            "
                                        />
                                        <label for="radioPrimary7">
                                            Medida
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input
                                            type="radio"
                                            id="radioPrimary8"
                                            name="sw_busqueda"
                                            value="nombre"
                                            v-model="sw_busqueda"
                                            @change="
                                                aux_lista_productos = [];
                                                ingreso_producto.producto_id =
                                                    '';
                                            "
                                        />
                                        <label for="radioPrimary8">
                                            Nombre
                                        </label>
                                    </div>
                                </div>

                                <label
                                    :class="{
                                        'text-danger': errors.producto_id,
                                    }"
                                    >Seleccionar Producto*</label
                                >

                                <el-select
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.producto_id,
                                    }"
                                    v-model="ingreso_producto.producto_id"
                                    filterable
                                    remote
                                    reserve-keyword
                                    placeholder="Buscar producto"
                                    :remote-method="buscarProducto"
                                    :loading="loading_buscador"
                                >
                                    <el-option
                                        v-for="item in aux_lista_productos"
                                        :key="item.id"
                                        :label="
                                            item.codigo +
                                            ' | ' +
                                            item.nombre +
                                            ' | ' +
                                            item.medida
                                        "
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
                                        ingreso_producto.nombre_producto_full
                                    "
                                />
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.proveedor_id,
                                    }"
                                    >Seleccionar Proveedor*</label
                                >
                                <el-select
                                    placeholder="Proveedor"
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.proveedor_id,
                                    }"
                                    v-model="ingreso_producto.proveedor_id"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listProveedors"
                                        :key="item.id"
                                        :label="item.nombre"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.proveedor_id"
                                    v-text="errors.proveedor_id[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.precio_compra,
                                    }"
                                    >Precio de compra*</label
                                >
                                <el-input
                                    type="number"
                                    min="0.01"
                                    placeholder="Precio de compra"
                                    :class="{
                                        'is-invalid': errors.precio_compra,
                                    }"
                                    v-model="ingreso_producto.precio_compra"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.precio_compra"
                                    v-text="errors.precio_compra[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.cantidad,
                                    }"
                                    >Cantidad*</label
                                >
                                <el-input
                                    type="number"
                                    min="0.01"
                                    placeholder="Cantidad"
                                    :class="{ 'is-invalid': errors.cantidad }"
                                    v-model="ingreso_producto.cantidad"
                                    clearable
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
                                        'text-danger': errors.tipo_ingreso_id,
                                    }"
                                    >Seleccionar Tipo de ingreso*</label
                                >
                                <el-select
                                    placeholder="Tipo de ingreso"
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.tipo_ingreso_id,
                                    }"
                                    v-model="ingreso_producto.tipo_ingreso_id"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listTipoIngresos"
                                        :key="item.id"
                                        :label="item.nombre"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.tipo_ingreso_id"
                                    v-text="errors.tipo_ingreso_id[0]"
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
                                    v-model="ingreso_producto.descripcion"
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
                        type="warning"
                        class="bg-warning"
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
        ingreso_producto: {
            type: Object,
            default: {
                id: 0,
                lugar: "",
                producto_id: "",
                proveedor_id: "",
                precio_compra: "",
                cantidad: "",
                tipo_ingreso_id: "",
                descripcion: "",
                nombre_producto_full: "",
            },
        },
    },
    watch: {
        muestra_modal: function (newVal, oldVal) {
            this.errors = [];
            if (newVal) {
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
            listProveedors: [],
            listTipoIngresos: [],
            loading_buscador: false,
            timeOutProductos: null,
            sw_busqueda: "todos",
        };
    },
    mounted() {
        this.bModal = this.muestra_modal;
        this.getProveedors();
        this.getTipoIngresos();
    },
    methods: {
        getProveedors() {
            axios.get("/admin/proveedors").then((response) => {
                this.listProveedors = response.data.proveedors;
            });
        },
        getTipoIngresos() {
            axios.get("/admin/tipo_ingresos").then((response) => {
                this.listTipoIngresos = response.data.tipo_ingresos;
            });
        },
        setRegistroModal() {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/ingreso_productos";
                let config = {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                };
                let formdata = new FormData();
                formdata.append(
                    "lugar",
                    this.ingreso_producto.lugar
                        ? this.ingreso_producto.lugar
                        : ""
                );
                formdata.append(
                    "producto_id",
                    this.ingreso_producto.producto_id
                        ? this.ingreso_producto.producto_id
                        : ""
                );
                formdata.append(
                    "proveedor_id",
                    this.ingreso_producto.proveedor_id
                        ? this.ingreso_producto.proveedor_id
                        : ""
                );
                formdata.append(
                    "precio_compra",
                    this.ingreso_producto.precio_compra
                        ? this.ingreso_producto.precio_compra
                        : ""
                );
                formdata.append(
                    "cantidad",
                    this.ingreso_producto.cantidad
                        ? this.ingreso_producto.cantidad
                        : ""
                );
                formdata.append(
                    "tipo_ingreso_id",
                    this.ingreso_producto.tipo_ingreso_id
                        ? this.ingreso_producto.tipo_ingreso_id
                        : ""
                );
                formdata.append(
                    "descripcion",
                    this.ingreso_producto.descripcion
                        ? this.ingreso_producto.descripcion
                        : ""
                );
                if (this.accion == "edit") {
                    url =
                        "/admin/ingreso_productos/" + this.ingreso_producto.id;
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
                            this.limpiaIngresoProducto();
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
        limpiaIngresoProducto() {
            this.errors = [];
            this.ingreso_producto.lugar = "";
            this.ingreso_producto.producto_id = "";
            this.ingreso_producto.proveedor_id = "";
            this.ingreso_producto.precio_compra = "";
            this.ingreso_producto.cantidad = "";
            this.ingreso_producto.tipo_ingreso_id = "";
            this.ingreso_producto.descripcion = "";
        },
        buscarProducto(query) {
            this.aux_lista_productos = [];
            this.loading_buscador = true;
            clearTimeout(this.timeOutProductos);
            let self = this;
            this.timeOutProductos = setTimeout(() => {
                self.getProductosQuery(query);
            }, 1000);
        },
        getProductosQuery(query) {
            if (query !== "") {
                axios
                    .get("/admin/productos/buscar_producto", {
                        params: {
                            value: query,
                            sw_busqueda: this.sw_busqueda,
                        },
                    })
                    .then((response) => {
                        this.loading_buscador = false;
                        this.listProductos;
                        this.aux_lista_productos = response.data;
                    });
            } else {
                this.loading_buscador = false;
                this.aux_lista_productos = [];
            }
        },
    },
};
</script>

<style></style>
