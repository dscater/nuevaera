<template>
    <div class="row">
        <div class="col-md-6">
            <form>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <template v-if="user.tipo != 'CAJA'">
                                        <div
                                            class="form-group col-md-6"
                                            v-if="accion != 'edit'"
                                        >
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.sucursal_id,
                                                }"
                                                >Seleccionar sucursal</label
                                            >
                                            <el-select
                                                class="w-full d-block"
                                                :class="{
                                                    'is-invalid':
                                                        errors.sucursal_id,
                                                }"
                                                v-model="
                                                    orden_venta.sucursal_id
                                                "
                                                clearable
                                                @change="getCajas()"
                                            >
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
                                                v-if="errors.sucursal_id"
                                                v-text="errors.sucursal_id[0]"
                                            ></span>
                                        </div>
                                        <div class="form-group col-md-6" v-else>
                                            <label
                                                :class="{
                                                    'text-danger':
                                                        errors.sucursal_id,
                                                }"
                                                >Sucursal</label
                                            >
                                            <input
                                                type="readonly"
                                                class="form-control"
                                                readonly
                                                :value="getNombreSucursal"
                                            />
                                            <span
                                                class="error invalid-feedback"
                                                v-if="errors.sucursal_id"
                                                v-text="errors.sucursal_id[0]"
                                            ></span>
                                        </div>
                                    </template>
                                    <div
                                        class="form-group col-md-6"
                                        v-if="user.tipo != 'CAJA'"
                                    >
                                        <label
                                            :class="{
                                                'text-danger': errors.caja_id,
                                            }"
                                            >Seleccionar caja</label
                                        >
                                        <el-select
                                            class="w-full d-block"
                                            :class="{
                                                'is-invalid': errors.caja_id,
                                            }"
                                            v-model="orden_venta.caja_id"
                                            clearable
                                        >
                                            <el-option
                                                v-for="item in listCajas"
                                                :key="item.id"
                                                :value="item.id"
                                                :label="item.nombre"
                                            >
                                            </el-option>
                                        </el-select>
                                        <span
                                            class="error invalid-feedback"
                                            v-if="errors.caja_id"
                                            v-text="errors.caja_id[0]"
                                        ></span>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label
                                            :class="{
                                                'text-danger':
                                                    errors.cliente_id,
                                            }"
                                            >Seleccionar cliente</label
                                        >
                                        <el-select
                                            class="w-full d-block"
                                            :class="{
                                                'is-invalid': errors.cliente_id,
                                            }"
                                            v-model="orden_venta.cliente_id"
                                            clearable
                                            @change="getCliente()"
                                        >
                                            <el-option
                                                v-for="item in listClientes"
                                                :key="item.id"
                                                :value="item.id"
                                                :label="item.nombre"
                                            >
                                            </el-option>
                                        </el-select>
                                        <span
                                            class="error invalid-feedback"
                                            v-if="errors.cliente_id"
                                            v-text="errors.cliente_id[0]"
                                        ></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table
                                            class="table table-bordered tabla_responsive"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Nombre Completo</th>
                                                    <th>Número C.I. o Nit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td
                                                        data-col="Nombre Completo: "
                                                        v-text
                                                    >
                                                        {{ oCliente.nombre }}
                                                    </td>
                                                    <td
                                                        data-col="Número C.I. o Nit: "
                                                    >
                                                        <select
                                                            class="form-control"
                                                            v-model="
                                                                orden_venta.nit
                                                            "
                                                        >
                                                            <option value="0">
                                                                0
                                                            </option>
                                                            <option
                                                                :value="
                                                                    oCliente.nit
                                                                "
                                                            >
                                                                Nit:
                                                                {{
                                                                    oCliente.nit
                                                                }}
                                                            </option>
                                                            <option
                                                                :value="
                                                                    oCliente.ci
                                                                "
                                                            >
                                                                C.I.:
                                                                {{
                                                                    oCliente.ci
                                                                }}
                                                            </option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 contenedor_tabla">
                                        <h5 class="w-100 text-center">
                                            AGREGAR PRODUCTOS
                                        </h5>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label
                                            :class="{
                                                'text-danger':
                                                    errors.producto_id,
                                            }"
                                            >Seleccionar producto</label
                                        >
                                        <el-select
                                            class="w-full d-block"
                                            :class="{
                                                'is-invalid':
                                                    errors.producto_id,
                                            }"
                                            v-model="producto_id"
                                            filterable
                                            remote
                                            reserve-keyword
                                            clearable
                                            placeholder="Buscar producto"
                                            :remote-method="buscarProducto"
                                            :loading="loading_buscador"
                                            @change="getProducto"
                                        >
                                            <el-option
                                                v-for="item in aux_lista_productos"
                                                :key="item.id"
                                                :value="item.producto.id"
                                                :label="
                                                    item.producto.nombre +
                                                    ' (' +
                                                    item.producto.medida +
                                                    ')'
                                                "
                                            >
                                            </el-option>
                                        </el-select>
                                        <span
                                            class="error invalid-feedback"
                                            v-if="errors.producto_id"
                                            v-text="errors.producto_id[0]"
                                        ></span>
                                    </div>
                                    <div
                                        class="col-md-12"
                                        style="overflow: auto"
                                    >
                                        <table
                                            class="table table-bordered tabla_responsive"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Grupo</th>
                                                    <th>
                                                        Precios<br />Menor /
                                                        Mayor
                                                    </th>
                                                    <th>Medida</th>
                                                    <th>Stock Disponible</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td
                                                        data-col="Código: "
                                                        v-text="
                                                            oProducto?.codigo
                                                        "
                                                    ></td>
                                                    <td
                                                        data-col="Nombre: "
                                                        v-text="
                                                            oProducto?.nombre
                                                        "
                                                    ></td>
                                                    <td
                                                        data-col="Grupo:"
                                                        v-text="
                                                            oProducto?.grupo
                                                                .nombre
                                                        "
                                                    ></td>
                                                    <td
                                                        data-col="Precio Menor/Mayor: "
                                                        v-text="
                                                            (oProducto
                                                                ? oProducto?.precio
                                                                : '') +
                                                            ' / ' +
                                                            (oProducto
                                                                ? oProducto?.precio_mayor
                                                                : '')
                                                        "
                                                    ></td>
                                                    <td
                                                        data-col="Medida: "
                                                        v-text="
                                                            oProducto?.medida
                                                        "
                                                    ></td>
                                                    <td
                                                        data-col="Stock actual: "
                                                        v-text="
                                                            oProducto?.stock_actual
                                                        "
                                                    ></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label
                                            :class="{
                                                'text-danger': errors.cantidad,
                                            }"
                                            >Cantidad</label
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errors.cantidad,
                                            }"
                                            v-model="cantidad"
                                        />
                                        <span
                                            class="error invalid-feedback"
                                            v-if="errors.cantidad"
                                            v-text="errors.cantidad[0]"
                                        ></span>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label
                                                >Seleccionar Venta por
                                                Mayor*</label
                                            >
                                            <select
                                                class="form-control"
                                                v-model="venta_mayor"
                                            >
                                                <option value="NO">NO</option>
                                                <option value="SI">SI</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button
                                            class="btn btn-primary btn-flat btn-block"
                                            :disabled="
                                                cantidad <= 0 ||
                                                cantidad == '' ||
                                                producto_id == ''
                                            "
                                            @click.prevent="validaStock()"
                                        >
                                            <i class="fa fa-plus"></i> Agregar
                                            producto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 contenedor_tabla">
                            <table class="table table-striped tabla_responsive">
                                <thead>
                                    <tr>
                                        <th
                                            colspan="5"
                                            class="bg-blue text-md text-center"
                                        >
                                            DETALLE DE LA VENTA
                                            <i class="fa fa-list-alt"></i>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio Unitario</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th width="5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-if="
                                            orden_venta.detalle_ordens.length >
                                            0
                                        "
                                        v-for="(
                                            item, index
                                        ) in orden_venta.detalle_ordens"
                                    >
                                        <td data-col="Nombre: ">
                                            {{ item.producto.nombre }}
                                        </td>
                                        <td data-col="Precio Unitario: ">
                                            {{ item.precio }}
                                        </td>
                                        <td data-col="Cantidad: ">
                                            {{ item.cantidad }}
                                        </td>
                                        <td data-col="Subtotal: ">
                                            {{ item.subtotal }}
                                        </td>
                                        <td class="text-center">
                                            <button
                                                v-if="orden_venta.editable"
                                                class="btn-sm btn-flat btn-danger"
                                                @click.prevent="
                                                    quitarDetalleOrdenVenta(
                                                        item.id,
                                                        index
                                                    )
                                                "
                                            >
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr
                                        v-if="
                                            orden_venta.detalle_ordens.length ==
                                            0
                                        "
                                    >
                                        <td
                                            colspan="5"
                                            class="text-center text-gray font-weight-bold"
                                        >
                                            NO SE AGREGÓ NINGUN PRODUCTO
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-blue">
                                    <tr>
                                        <td colspan="3" class="ocultar">
                                            TOTAL
                                        </td>
                                        <td
                                            data-col="TOTAL: "
                                            class="font-weight-bold text-lg"
                                        >
                                            {{ orden_venta.total }}
                                        </td>
                                        <td class="ocultar"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <el-button
                        type="primary"
                        class="bg-lightblue btn-block"
                        :loading="enviando"
                        @click="enviarFormulario()"
                        v-html="textoBoton"
                        :disabled="orden_venta.detalle_ordens.length <= 0"
                        v-if="orden_venta.editable || accion == 'nuevo'"
                    ></el-button>
                    <el-button type="info" class="btn-block" v-else>
                        No editable, debido a que existe una
                        devolución</el-button
                    >
                    <router-link
                        v-if="this.orden_venta.id != 0"
                        class="btn btn-success btn-lg btn-block"
                        :to="{ name: 'orden_ventas.ticket' }"
                        ><i class="fa fa-print"></i> Imprimir
                        Ticket</router-link
                    >

                    <router-link
                        :to="{ name: 'orden_ventas.create' }"
                        v-if="this.orden_venta.id != 0"
                        class="btn btn-danger btn-lg btn-block"
                        ><i class="fa fa-plus"></i> Realizar nueva
                        orden</router-link
                    >

                    <router-link
                        :to="{ name: 'orden_ventas.index' }"
                        class="btn btn-default btn-lg btn-block"
                        ><i class="fa fa-cash-register"></i> Volver a orden de
                        ventas</router-link
                    >
                </div>
            </div>
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
        orden_venta: {
            type: Object,
            default() {
                return {
                    id: 0,
                    sucursal_id: "",
                    cliente_id: "",
                    nit: "",
                    total: "0.00",
                    detalle_ordens: [],
                    editable: true,
                };
            },
        },
    },
    watch: {
        orden_venta(newVal, oldVal) {
            if (newVal.id != 0) {
                this.getClientes();
                this.getCliente();
                this.getCajas();
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
        getNombreSucursal() {
            if (this.orden_venta) {
                if (this.orden_venta.sucursal) {
                    return this.orden_venta.sucursal.nombre;
                }
            }
            return "";
        },
    },
    data() {
        return {
            user: JSON.parse(localStorage.getItem("user")),
            enviando: false,
            producto_id: "",
            venta_mayor: "NO",
            cantidad: 1,
            errors: [],
            listClientes: [],
            aux_lista_productos: [],
            listProductos: [],
            listSucursales: [],
            listCajas: [],
            eliminados: [],
            oCliente: {
                nombre: "",
                ci: "",
                full_ci: "",
                dir: "",
            },
            oProducto: null,
            loading_buscador: false,
            timeOutProductos: null,
        };
    },
    mounted() {
        this.getSucursales();
        if (this.orden_venta.id == 0) {
            this.orden_venta.fecha = this.fechaActual();
        }
        if (this.user.tipo == "CAJA") {
            this.orden_venta.sucursal_id = this.user.sucursal.sucursal_id;
            this.orden_venta.caja_id = this.user.sucursal.caja_id;
        }
        this.getClientes();
        this.iniciaSeleccionFilas();
    },
    methods: {
        // OBTENER LISTADOS E INFORMACIÓN
        getSucursales() {
            axios.get("/admin/sucursals").then((response) => {
                this.listSucursales = response.data.sucursals;
            });
        },
        getCajas() {
            if (this.orden_venta.sucursal_id != "") {
                axios
                    .get("/admin/cajas/cajas_sucursal", {
                        params: {
                            id: this.orden_venta.sucursal_id,
                        },
                    })
                    .then((response) => {
                        this.listCajas = response.data;
                    });
            } else {
                this.listCajas = [];
            }
        },
        getCliente() {
            if (this.orden_venta.cliente_id != "") {
                axios
                    .get("/admin/clientes/" + this.orden_venta.cliente_id)
                    .then((response) => {
                        this.oCliente = response.data.cliente;
                        if (this.accion != "edit") {
                            this.orden_venta.nit = this.oCliente.ci;
                        }
                    });
            } else {
                this.oCliente = {
                    nombre: "",
                    ci: "",
                    full_ci: "",
                    dir: "",
                };
            }
        },
        getClientes() {
            axios.get("/admin/clientes").then((response) => {
                this.listClientes = response.data.clientes;
            });
        },
        getProducto() {
            if (this.producto_id != "") {
                axios
                    .get("/admin/productos/" + this.producto_id, {
                        params: {
                            id: this.orden_venta.sucursal_id,
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
        // ENVIAR REGISTRO
        enviarFormulario() {
            this.enviando = true;
            try {
                this.textoBtn = "Enviando...";
                let url = "/admin/orden_ventas";
                if (this.accion == "edit") {
                    url = "/admin/orden_ventas/" + this.orden_venta.id;
                    this.orden_venta["_method"] = "PUT";
                    this.orden_venta.eliminados = this.eliminados;
                }
                axios
                    .post(url, this.orden_venta)
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
        limpiaOrdenVenta() {
            this.errors = [];
        },
        fechaActual() {
            // crea un nuevo objeto `Date`
            var today = new Date();

            // `getDate()` devuelve el día del mes (del 1 al 31)
            var day = today.getDate();
            if (day < 10) {
                day = "0" + day;
            }
            // `getMonth()` devuelve el mes (de 0 a 11)
            var month = today.getMonth() + 1;
            if (month < 10) {
                month = "0" + month;
            }

            // `getFullYear()` devuelve el año completo
            var year = today.getFullYear();

            // muestra la fecha de hoy en formato `MM/DD/YYYY`
            return `${year}-${month}-${day}`;
        },
        iniciaSeleccionFilas() {
            $(".detalle_ordens").on("focusin", "input", function () {
                $(this).parents("tr").addClass("seleccionado");
            });
            $(".detalle_ordens").on("focusout", "input", function () {
                $(this).parents("tr").removeClass("seleccionado");
            });
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
                    .get("/admin/productos/productos_sucursal", {
                        params: {
                            value: query,
                            id: this.orden_venta.sucursal_id,
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
        generaReporte() {
            this.enviando = true;
            let config = {
                responseType: "blob",
            };
            axios
                .post(
                    "/admin/orden_ventas/pdf/" + this.orden_venta.id,
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
        validaStock() {
            axios
                .get("/admin/productos/valida_stock", {
                    params: {
                        id: this.producto_id,
                        sucursal_id: this.orden_venta.sucursal_id,
                        cantidad: this.cantidad,
                    },
                })
                .then((response) => {
                    if (response.data.sw) {
                        let subtotal = 0;
                        let precio = 0;
                        if (this.venta_mayor == "SI") {
                            precio = response.data.producto.precio_mayor;
                            subtotal =
                                parseFloat(this.cantidad) * parseFloat(precio);
                        } else {
                            precio = response.data.producto.precio;
                            subtotal =
                                parseFloat(this.cantidad) * parseFloat(precio);
                        }

                        this.orden_venta.detalle_ordens.push({
                            id: 0,
                            orden_id: 0,
                            producto_id: response.data.producto.id,
                            sucursal_stock_id: response.data.sucursal_stock.id,
                            cantidad: this.cantidad,
                            venta_mayor: this.venta_mayor,
                            precio: precio,
                            subtotal: subtotal.toFixed(2),
                            producto: response.data.producto,
                        });
                        this.sumaTotalOrdenVenta();
                        this.producto_id = "";
                        this.venta_mayor = "NO";
                        this.oProducto = null;
                        this.cantidad = 1;
                    } else {
                        Swal.fire({
                            icon: "info",
                            title: "ATENCIÓN",
                            html: response.data.msj,
                            showConfirmButton: false,
                            timer: 2500,
                        });
                    }
                });
        },
        sumaTotalOrdenVenta() {
            let suma_total = 0;
            let precio = 0;
            let subtotal = 0;
            this.orden_venta.detalle_ordens.forEach((elem) => {
                suma_total += parseFloat(elem.subtotal);
            });
            this.orden_venta.total = suma_total.toFixed(2);
        },
        quitarDetalleOrdenVenta(id, index) {
            if (id) {
                this.eliminados.push(id);
            }
            this.orden_venta.detalle_ordens.splice(index, 1);
            this.sumaTotalOrdenVenta();
        },
    },
};
</script>

<style>
.detalle_ordens tbody tr td {
    padding: 0px;
    vertical-align: middle;
}

.detalle_ordens tbody tr td:nth-child(1) {
    padding-left: 5px;
}

.contenedor_tabla {
    overflow: auto;
}

@media (max-width: 780px) {
    .tabla_responsive thead {
        display: none;
    }

    .tabla_responsive.table-striped tbody tr td,
    .tabla_responsive.table-bordered tbody tr td {
        display: block !important;
    }
    .tabla_responsive.table-striped tbody tr td:before,
    .tabla_responsive.table-bordered tbody tr td:before {
        content: attr(data-col);
        font-weight: bold;
    }

    .tabla_responsive.table-bordered tfoot tr td,
    .tabla_responsive.table-bordered tfoot tr th,
    .tabla_responsive.table-striped tfoot tr td,
    .tabla_responsive.table-striped tfoot tr th {
        display: block;
    }

    .tabla_responsive.table-bordered tfoot tr td.ocultar,
    .tabla_responsive.table-bordered tfoot tr th.ocultar,
    .tabla_responsive.table-striped tfoot tr td.ocultar,
    .tabla_responsive.table-striped tfoot tr th.ocultar {
        display: none !important;
    }

    .tabla_responsive.table-bordered tfoot tr td:before,
    .tabla_responsive.table-bordered tfoot tr th:before,
    .tabla_responsive.table-striped tfoot tr td:before,
    .tabla_responsive.table-striped tfoot tr th:before {
        content: attr(data-col);
        font-weight: bold;
    }
}
</style>
