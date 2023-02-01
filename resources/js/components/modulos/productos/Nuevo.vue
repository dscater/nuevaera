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
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.codigo,
                                    }"
                                    >Código*</label
                                >
                                <el-input
                                    placeholder="Código"
                                    :class="{ 'is-invalid': errors.codigo }"
                                    v-model="producto.codigo"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.codigo"
                                    v-text="errors.codigo[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.nombre,
                                    }"
                                    >Nombre*</label
                                >
                                <el-input
                                    placeholder="Nombre"
                                    :class="{ 'is-invalid': errors.nombre }"
                                    v-model="producto.nombre"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.nombre"
                                    v-text="errors.nombre[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.medida,
                                    }"
                                    >Medida*</label
                                >
                                <el-input
                                    placeholder="Nombre"
                                    :class="{ 'is-invalid': errors.medida }"
                                    v-model="producto.medida"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.medida"
                                    v-text="errors.medida[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.grupo_id,
                                    }"
                                    >Seleccionar Grupo*</label
                                >
                                <el-select
                                    placeholder="Seleccionar Grupo"
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.grupo_id,
                                    }"
                                    v-model="producto.grupo_id"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listGrupos"
                                        :key="item.id"
                                        :label="item.nombre"
                                        :value="item.id"
                                    >
                                    </el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.grupo_id"
                                    v-text="errors.grupo_id[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.precio,
                                    }"
                                    >Precio de venta*</label
                                >
                                <el-input
                                    type="number"
                                    step="0.01"
                                    placeholder="Previo de venta"
                                    :class="{ 'is-invalid': errors.precio }"
                                    v-model="producto.precio"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.precio"
                                    v-text="errors.precio[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.precio_mayor,
                                    }"
                                    >Precio de venta por Mayor*</label
                                >
                                <el-input
                                    type="number"
                                    step="0.01"
                                    placeholder="Previo de venta por Mayor"
                                    :class="{
                                        'is-invalid': errors.precio_mayor,
                                    }"
                                    v-model="producto.precio_mayor"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.precio_mayor"
                                    v-text="errors.precio_mayor[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.stock_min,
                                    }"
                                    >Stock mínimo*</label
                                >
                                <el-input
                                    type="number"
                                    step="0.01"
                                    placeholder="Stock mínimo"
                                    :class="{
                                        'is-invalid': errors.stock_min,
                                    }"
                                    v-model="producto.stock_min"
                                    clearable
                                >
                                </el-input>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.stock_min"
                                    v-text="errors.stock_min[0]"
                                ></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label
                                    :class="{
                                        'text-danger': errors.descontar_stock,
                                    }"
                                    >Descontar de Stock</label
                                >
                                <el-select
                                    placeholder="Descontar de Stock"
                                    class="w-100"
                                    :class="{
                                        'is-invalid': errors.descontar_stock,
                                    }"
                                    v-model="producto.descontar_stock"
                                >
                                    <el-option
                                        v-for="(item, index) in ['SI', 'NO']"
                                        :key="index"
                                        :label="item"
                                        :value="item"
                                    ></el-option>
                                </el-select>
                                <span
                                    class="error invalid-feedback"
                                    v-if="errors.descontar_stock"
                                    v-text="errors.descontar_stock[0]"
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
        producto: {
            type: Object,
            default: {
                id: 0,
                codigo: "",
                nombre: "",
                medida: "",
                grupo_id: "",
                precio: "",
                precio_mayor: "",
                stock_min: "",
                descontar_stock: "SI",
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
            listGrupos: [],
        };
    },
    mounted() {
        this.bModal = this.muestra_modal;
        this.getGrupos();
    },
    methods: {
        getGrupos() {
            axios.get("/admin/grupos").then((response) => {
                this.listGrupos = response.data.grupos;
            });
        },
        setRegistroModal() {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/productos";
                let config = {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                };
                let formdata = new FormData();
                formdata.append(
                    "codigo",
                    this.producto.codigo ? this.producto.codigo : ""
                );
                formdata.append(
                    "nombre",
                    this.producto.nombre ? this.producto.nombre : ""
                );
                formdata.append(
                    "medida",
                    this.producto.medida ? this.producto.medida : ""
                );
                formdata.append(
                    "grupo_id",
                    this.producto.grupo_id ? this.producto.grupo_id : ""
                );
                formdata.append(
                    "precio",
                    this.producto.precio ? this.producto.precio : ""
                );
                formdata.append(
                    "precio_mayor",
                    this.producto.precio_mayor ? this.producto.precio_mayor : ""
                );
                formdata.append(
                    "stock_min",
                    this.producto.stock_min ? this.producto.stock_min : ""
                );
                formdata.append(
                    "descontar_stock",
                    this.producto.descontar_stock
                        ? this.producto.descontar_stock
                        : ""
                );
                if (this.accion == "edit") {
                    url = "/admin/productos/" + this.producto.id;
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
                            this.limpiaProducto();
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
        limpiaProducto() {
            this.errors = [];
            this.producto.codigo = "";
            this.producto.nombre = "";
            this.producto.medida = "";
            this.producto.grupo_id = "";
            this.producto.precio = "";
            this.producto.precio_mayor = "";
            this.producto.stock_min = "";
            this.producto.descontar_stock = "SI";
        },
    },
};
</script>

<style></style>
