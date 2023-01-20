<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Orden de Ventas - <span>Editar</span></h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <FormOrdenVenta
                                    :orden_venta="oOrdenVenta"
                                    :accion="'edit'"
                                    @envioFormulario="recargaFormulario"
                                ></FormOrdenVenta>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import FormOrdenVenta from "./FormOrdenVenta.vue";
export default {
    components: {
        FormOrdenVenta,
    },
    props: ["id"],
    data() {
        return {
            fullscreenLoading: true,
            loadingWindow: Loading.service({
                fullscreen: this.fullscreenLoading,
            }),
            oOrdenVenta: {
                id: 0,
                sucursal_id: "",
                cliente_id: "",
                nit: "",
                venta_mayor: "NO",
                total: "0.00",
                detalle_ordens: [],
            },
        };
    },
    mounted() {
        this.getOrdenVenta();
        this.loadingWindow.close();
    },
    methods: {
        recargaFormulario(id) {
            this.$router.push({
                name: "orden_ventas.ticket",
                params: {
                    id: id,
                    imprime: true,
                },
            });
            // location.reload();
        },
        getOrdenVenta() {
            axios.get("/admin/orden_ventas/" + this.id).then((response) => {
                this.oOrdenVenta = response.data;
            });
        },
    },
};
</script>
<style></style>
