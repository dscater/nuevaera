<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Importación de Apertura - <span>Nuevo</span></h1>
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
                                <FormImportacionApertura
                                ></FormImportacionApertura>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import FormImportacionApertura from "./FormImportacionApertura.vue";
export default {
    components: {
        FormImportacionApertura,
    },
    data() {
        return {};
    },
    mounted() {},
    methods: {
    },
};
</script>
<style></style>
