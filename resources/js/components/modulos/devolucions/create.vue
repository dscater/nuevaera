<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Devoluciones - <span>Nuevo</span></h1>
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
                                    @envioFormulario="enviaEdit"
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
    data() {
        return {};
    },
    mounted() {},
    methods: {
        enviaEdit(id) {
            this.$router.push({
                name: "devolucions.edit",
                params: {
                    id: id,
                },
            });
        },
    },
};
</script>
<style></style>
