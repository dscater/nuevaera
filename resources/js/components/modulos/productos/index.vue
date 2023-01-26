<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Productos</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-3">
                                        <button
                                            v-if="
                                                permisos.includes(
                                                    'productos.create'
                                                )
                                            "
                                            class="btn btn-outline-primary bg-primary btn-flat btn-block"
                                            @click="
                                                abreModal('nuevo');
                                                limpiaProducto();
                                            "
                                        >
                                            <i class="fa fa-plus"></i>
                                            Nuevo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <b-col lg="10" class="my-1">
                                        <b-form-group
                                            label="Buscar"
                                            label-for="filter-input"
                                            label-cols-sm="3"
                                            label-align-sm="right"
                                            label-size="sm"
                                            class="mb-0"
                                        >
                                            <b-input-group size="sm">
                                                <b-form-input
                                                    id="filter-input"
                                                    v-model="filter"
                                                    @keyup="listaProductos"
                                                    type="search"
                                                    placeholder="Buscar"
                                                ></b-form-input>

                                                <b-input-group-append>
                                                    <b-button
                                                        class="bg-primary"
                                                        variant="primary"
                                                        :disabled="!filter"
                                                        @click="filter = ''"
                                                        >Borrar</b-button
                                                    >
                                                </b-input-group-append>
                                            </b-input-group>
                                        </b-form-group>
                                    </b-col>
                                    <div class="col-md-12">
                                        <b-overlay
                                            :show="showOverlay"
                                            rounded="sm"
                                        >
                                            <b-table
                                                :fields="fields"
                                                :items="getProductos"
                                                :busy.sync="isBusy"
                                                @sort-changed="sortingChanged"
                                                show-empty
                                                stacked="md"
                                                :currentPage="currentPage"
                                                :perPage="perPage"
                                                responsive
                                                empty-text="Sin resultados"
                                                empty-filtered-text="Sin resultados"
                                                ref="table"
                                            >
                                                <template
                                                    #cell(fecha_registro)="row"
                                                >
                                                    {{
                                                        formatoFecha(
                                                            row.item
                                                                .fecha_registro
                                                        )
                                                    }}
                                                </template>

                                                <template #cell(accion)="row">
                                                    <div
                                                        class="row justify-content-between"
                                                    >
                                                        <b-button
                                                            size="sm"
                                                            pill
                                                            variant="outline-warning"
                                                            class="btn-flat btn-block"
                                                            title="Editar registro"
                                                            @click="
                                                                editarRegistro(
                                                                    row.item
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-edit"
                                                            ></i>
                                                        </b-button>
                                                        <b-button
                                                            size="sm"
                                                            pill
                                                            variant="outline-danger"
                                                            class="btn-flat btn-block"
                                                            title="Eliminar registro"
                                                            @click="
                                                                eliminaProducto(
                                                                    row.item.id,
                                                                    row.item
                                                                        .nombre
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-trash"
                                                            ></i>
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </b-overlay>
                                        <div class="row">
                                            <b-col
                                                sm="6"
                                                md="2"
                                                class="ml-auto my-1"
                                            >
                                                <b-form-select
                                                    align="right"
                                                    id="per-page-select"
                                                    v-model="perPage"
                                                    :options="pageOptions"
                                                    size="sm"
                                                ></b-form-select>
                                            </b-col>
                                            <b-col
                                                sm="6"
                                                md="2"
                                                class="my-1 mr-auto"
                                                v-if="perPage"
                                            >
                                                <b-pagination
                                                    v-model="page"
                                                    :total-rows="totalRows"
                                                    :per-page="perPage"
                                                    align="left"
                                                ></b-pagination>
                                            </b-col>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Nuevo
            :muestra_modal="muestra_modal"
            :accion="modal_accion"
            :producto="oProducto"
            @close="muestra_modal = false"
            @envioModal="listaProductos"
        ></Nuevo>
    </div>
</template>

<script>
import Nuevo from "./Nuevo.vue";
export default {
    components: {
        Nuevo,
    },
    data() {
        return {
            permisos: localStorage.getItem("permisos"),
            search: "",
            listRegistros: [],
            showOverlay: false,
            fields: [
                {
                    key: "codigo",
                    label: "Código",
                    sortable: true,
                },
                { key: "nombre", label: "Nombre", sortable: true },
                { key: "medida", label: "Medida", sortable: true },
                { key: "grupo.nombre", label: "Grupo", sortable: true },
                { key: "precio", label: "Precio de venta", sortable: true },
                {
                    key: "precio_mayor",
                    label: "Previo de venta por mayor",
                    sortable: true,
                },
                { key: "stock_min", label: "Stock mínimo", sortable: true },
                {
                    key: "descontar_stock",
                    label: "Descontar de stock",
                    sortable: true,
                },
                {
                    key: "fecha_registro",
                    label: "Fecha de registro",
                    sortable: true,
                },
                { key: "accion", label: "Acción" },
            ],
            isBusy: false,
            loading: true,
            fullscreenLoading: true,
            loadingWindow: Loading.service({
                fullscreen: this.fullscreenLoading,
            }),
            muestra_modal: false,
            modal_accion: "nuevo",
            oProducto: {
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
            page: 1,
            currentPage: 1,
            perPage: 5,
            pageOptions: [
                { value: 5, text: "Mostrar 5 Registros" },
                { value: 10, text: "Mostrar 10 Registros" },
                { value: 25, text: "Mostrar 25 Registros" },
                { value: 50, text: "Mostrar 50 Registros" },
                { value: 100, text: "Mostrar 100 Registros" },
                // { value: this.totalRows, text: "Mostrar Todo" },
            ],
            totalRows: 10,
            filter: null,
            sortBy: null,
            sortDesc: null,
            links: null,
        };
    },
    watch: {
        page(newVal) {
            // this.listaProductos();
            this.$refs.table.refresh();
        },
        perPage(newVal) {
            // this.listaProductos();
            this.$refs.table.refresh();
        },
    },
    mounted() {
        this.loadingWindow.close();
        this.listaProductos();
    },
    methods: {
        // Seleccionar Opciones de Tabla
        editarRegistro(item) {
            this.oProducto.id = item.id;
            this.oProducto.codigo = item.codigo ? item.codigo : "";
            this.oProducto.nombre = item.nombre ? item.nombre : "";
            this.oProducto.medida = item.medida ? item.medida : "";
            this.oProducto.grupo_id = item.grupo_id ? item.grupo_id : "";
            this.oProducto.precio = item.precio ? item.precio : "";
            this.oProducto.precio_mayor = item.precio_mayor
                ? item.precio_mayor
                : "";
            this.oProducto.stock_min = item.stock_min ? item.stock_min : "";
            this.oProducto.descontar_stock = item.descontar_stock
                ? item.descontar_stock
                : "SI";

            this.modal_accion = "edit";
            this.muestra_modal = true;
        },
        listaProductos() {
            this.page = 1;
            this.$refs.table.refresh();
            this.muestra_modal = false;
        },
        // Listar Productos
        sortingChanged(ctx) {
            this.sortBy = ctx.sortBy;
            this.sortDesc = ctx.sortDesc;
            this.$refs.table.refresh();
        },

        getProductos(ctx) {
            this.isBusy = true;
            let promise = axios.get("/admin/productos/paginado", {
                params: {
                    page: this.page,
                    per_page: this.perPage,
                    value: this.filter,
                    sortBy: this.sortBy,
                    sortDesc: this.sortDesc,
                },
            });
            return promise
                .then((data) => {
                    this.currentPage = data.data.productos.current_page;
                    this.totalRows = data.data.productos.total;
                    const items = data.data.productos.data;
                    this.isBusy = false;
                    return items;
                })
                .catch((error) => {
                    console.log(error);
                    this.isBusy = false;
                    return [];
                });
        },
        eliminaProducto(id, descripcion) {
            Swal.fire({
                title: "¿Quierés eliminar este registro?",
                html: `Esta acción eliminara también los registros de Kardex tanto del Almacén y Sucursales; siempre y cuando no se hallan realizado Orden de ventas<br><strong>${descripcion}</strong>`,
                showCancelButton: true,
                confirmButtonColor: "#5398d8",
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "No, cancelar",
                denyButtonText: `No, cancelar`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    axios
                        .post("/admin/productos/" + id, {
                            _method: "DELETE",
                        })
                        .then((res) => {
                            this.listaProductos();
                            this.filter = "";
                            Swal.fire({
                                icon: "success",
                                title: res.data.msj,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        })
                        .catch((error) => {
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
                }
            });
        },
        abreModal(tipo_accion = "nuevo", producto = null) {
            this.muestra_modal = true;
            this.modal_accion = tipo_accion;
            if (producto) {
                this.oProducto = producto;
            }
        },
        limpiaProducto() {
            this.oProducto.codigo = "";
            this.oProducto.nombre = "";
            this.oProducto.medida = "";
            this.oProducto.grupo_id = "";
            this.oProducto.precio = "";
            this.oProducto.precio_mayor = "";
            this.oProducto.stock_min = "";
            this.oProducto.descontar_stock = "SI";
        },
        formatoFecha(date) {
            return this.$moment(String(date)).format("DD/MM/YYYY");
        },
    },
};
</script>

<style></style>
