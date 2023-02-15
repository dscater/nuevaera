<template>
    <tr>
        <td data-col="Grupo: ">
            {{ producto.grupo.nombre }}
        </td>
        <td data-col="Medida: ">
            {{ producto.medida }}
        </td>
        <td data-col="Nombre: ">
            {{ producto.nombre }}
            <template
                v-if="existe_ventas && importacion_apertura.cambio_stock == 1"
            >
                <br />
                <span class="text-red text-xs"
                    >Stock no modificable debido a que existe mas de una
                    operación realizada con el producto</span
                >
            </template>
        </td>
        <td>
            <input
                v-if="importacion_apertura?.cambio_stock == 1 && !existe_ventas"
                type="number"
                class="form-control input_stock_importacion"
                v-model="cantidad"
                @change="enviaCantidad('cantidad')"
                @keyup="enviaCantidad('cantidad')"
            />
            <input
                v-else
                type="number"
                class="form-control"
                v-model="cantidad"
                readonly
            />
        </td>
        <td>
            <input
                type="number"
                class="form-control input_stock_importacion"
                v-model="precio"
                @change="enviaCantidad('precio')"
                @keyup="enviaCantidad('precio')"
            />
        </td>
        <td>
            <input
                type="number"
                class="form-control input_stock_importacion"
                v-model="stock_min"
                @change="enviaCantidad('stock_min')"
                @keyup="enviaCantidad('stock_min')"
            />
        </td>
    </tr>
</template>
<script>
export default {
    props: ["o_importacion_apertura", "producto"],
    data() {
        return {
            cantidad: 0,
            precio: 0,
            stock_min: 0,
            importacion_apertura: this.o_importacion_apertura,
            existe_ventas: false,
            setTimeEnvio: null,
        };
    },
    watch: {
        o_importacion_apertura(newVal, oldVal) {
            this.importacion_apertura = newVal;
        },
    },
    mounted() {
        this.getStockActual();
        this.verificaCambio();
    },
    methods: {
        getStockActual() {
            axios
                .get("/admin/productos/getStock/", {
                    params: {
                        producto_id: this.producto.id,
                        lugar: this.importacion_apertura.lugar,
                    },
                })
                .then((response) => {
                    this.cantidad = response.data.stock_actual;
                    this.precio = response.data.producto.precio;
                    this.stock_min = response.data.producto.stock_min;
                    console.log("EEE");
                });
        },
        enviaCantidad(col) {
            clearTimeout(this.setTimeEnvio);
            let self = this;
            this.setTimeEnvio = setTimeout(function () {
                self.actualizaStock(col);
            }, 700);
        },
        // ENVIAR REGISTRO
        actualizaStock(col) {
            // this.enviando = true;
            if (this.cantidad != "") {
                try {
                    let url = "/admin/importacion_aperturas/actualiza_stock";
                    axios
                        .post(url, {
                            id: this.producto.id,
                            lugar: this.importacion_apertura.lugar,
                            cantidad: this.cantidad,
                            precio: this.precio,
                            stock_min: this.stock_min,
                            col: col,
                        })
                        .then((res) => {
                            // this.enviando = false;
                            if (res.data.sw) {
                                // Swal.fire({
                                //     icon: "success",
                                //     title: res.data.msj,
                                //     showConfirmButton: false,
                                //     timer: 2000,
                                // });
                                this.errors = [];
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
                            // this.enviando = false;
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
                    // this.enviando = false;
                    console.log(e);
                }
            }
        },
        verificaCambio() {
            axios
                .get("/admin/productos/verifica_ventas", {
                    params: {
                        id: this.producto.id,
                        lugar: this.importacion_apertura.lugar,
                    },
                })
                .then((response) => {
                    this.existe_ventas = response.data;
                });
        },
    },
};
</script>
<style></style>
