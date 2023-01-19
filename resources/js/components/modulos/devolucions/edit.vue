<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Devoluciones - <span>Editar</span></h1>
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
                                <FormDevolucion
                                    :devolucion="oDevolucion"
                                    :accion="'edit'"
                                    @envioFormulario="recargaFormulario()"
                                ></FormDevolucion>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import FormDevolucion from "./FormDevolucion.vue";
export default {
    components: {
        FormDevolucion,
    },
    props: ["id"],
    data() {
        return {
            fullscreenLoading: true,
            loadingWindow: Loading.service({
                fullscreen: this.fullscreenLoading,
            }),
            oDevolucion: {
                id: 0,
                orden_id: "",
                devolucion_detalles: [],
            },
        };
    },
    mounted() {
        this.getDevolucion();
        this.loadingWindow.close();
    },
    methods: {
        recargaFormulario() {
            location.reload();
        },
        getDevolucion() {
            axios.get("/admin/devolucions/" + this.id).then((response) => {
                this.oDevolucion = response.data;
            });
        },
    },
};
</script>
<style></style>
