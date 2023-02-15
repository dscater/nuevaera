<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Orden de Ventas - <span>Ticket</span></h1>
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
                                <div id="principal">
                                    <div
                                        class="contenedor_factura ml-auto mr-auto"
                                        id="contenedor_imprimir"
                                    >
                                        <div class="elemento logo">
                                            <!-- <img
                                                src="{{ asset('imgs/' . $empresa->logo) }}"
                                                alt="Logo"
                                            /> -->
                                        </div>
                                        <div class="elemento nom_empresa">
                                            {{ oConfiguracion.razon_social }}
                                        </div>
                                        <div class="elemento txt_fo text-bold">
                                            ORDEN DE VENTA
                                        </div>
                                        <div class="elemento direccion">
                                            Dirección:
                                            {{ oConfiguracion.dir }}
                                        </div>
                                        <div class="elemento direccion">
                                            Teléfonos:
                                            {{ oConfiguracion.fono }}
                                        </div>
                                        <div class="elemento detalle izquierda">
                                            Nro. Orden:
                                            {{ oOrdenVenta.nro_orden }}
                                            <br />
                                            Fecha:
                                            {{ oOrdenVenta.fecha_formateado }}
                                            &nbsp;&nbsp; Hora:
                                            {{ oOrdenVenta.hora }}
                                            <br />
                                            Cliente:
                                            {{ oOrdenVenta.cliente?.nombre }}
                                            <br />
                                            CI/NIT: {{ oOrdenVenta.nit }}
                                            <br />
                                            Tipo:
                                            {{ oOrdenVenta.tipo_venta }} - <span v-if="oOrdenVenta.tipo_venta=='A CRÉDITO'">{{oOrdenVenta.estado}}</span>
                                            <br />
                                            Caja:
                                            {{ oOrdenVenta.user?.usuario }}
                                            <br />
                                        </div>
                                        <div class="elemento">DETALLE</div>
                                        <div class="cobro">
                                            <table>
                                                <tr class="punteado">
                                                    <td class="centreado">
                                                        CANTIDAD
                                                    </td>
                                                    <td class="centreado">
                                                        PRODUCTO
                                                    </td>
                                                    <td class="centreado">
                                                        P/U
                                                    </td>
                                                    <td class="centreado">
                                                        SUBTOTAL
                                                    </td>
                                                </tr>
                                                <tr
                                                    v-for="item in oOrdenVenta.detalle_ordens"
                                                >
                                                    <td class="centreado">
                                                        {{ item.cantidad }}
                                                    </td>
                                                    <td class="izquierda">
                                                        {{
                                                            item.producto.nombre
                                                        }}
                                                    </td>
                                                    <td class="centreado">
                                                        {{ item.precio }}
                                                    </td>
                                                    <td class="centreado">
                                                        {{ item.subtotal }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        colspan="3"
                                                        class="bold elemento"
                                                        style="text-align:right;padding-right:4px;"
                                                    >
                                                        TOTAL
                                                    </td>
                                                    <td class="centreado bold">
                                                        {{ oOrdenVenta.total }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        colspan="3"
                                                        class="bold elemento"
                                                        style="text-align:right;padding-right:4px;"
                                                    >
                                                        DESCUENTO %
                                                    </td>
                                                    <td class="centreado bold">
                                                        {{ oOrdenVenta.descuento }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        colspan="3"
                                                        class="bold elemento"
                                                        style="text-align:right;padding-right:4px;"
                                                    >
                                                        TOTAL FINAL
                                                    </td>
                                                    <td class="centreado bold">
                                                        {{ oOrdenVenta.total_final }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div
                                            class="elemento"
                                            style="
                                                border-top: dashed 1px;
                                                padding-top: 7px;
                                                marigin-top: 5px;
                                                font-weight: bold;
                                            "
                                            v-if="devolucion"
                                        >
                                            DEVOLUCIONES
                                        </div>
                                        <div class="cobro" v-if="devolucion">
                                            <table>
                                                <tr class="punteado">
                                                    <td class="centreado">
                                                        CANTIDAD
                                                    </td>
                                                    <td class="centreado">
                                                        PRODUCTO
                                                    </td>
                                                </tr>
                                                <tr
                                                    v-for="item in devolucion.devolucion_detalles"
                                                    v-if="item.cantidad > 0"
                                                >
                                                    <td class="centreado">
                                                        {{ item.cantidad }}
                                                    </td>
                                                    <td class="izquierda">
                                                        {{
                                                            item.producto.nombre
                                                        }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div
                                            class="elemento detalle bold"
                                            v-if="devolucion"
                                        >
                                            TOTAL FINAL: {{ total_final }}
                                        </div>
                                        <div class="izquierda literal">
                                            Son: {{ literal }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button
                                            class="btn btn-warning btn-block btn-flat"
                                            id="btnImprimir"
                                            @click="imrpimirContenedor"
                                        >
                                            <i class="fa fa-print"></i> Imprimir
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <router-link
                                            class="btn btn-outline-warning mt-2 btn-flat mb-1 btn-block"
                                            :to="{
                                                name: 'orden_ventas.edit',
                                                params: {
                                                    id: oOrdenVenta.id,
                                                },
                                            }"
                                        >
                                            Editar
                                        </router-link>
                                    </div>
                                </div>
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
    props: ["id", "imprime"],
    data() {
        return {
            fullscreenLoading: true,
            loadingWindow: Loading.service({
                fullscreen: this.fullscreenLoading,
            }),
            oConfiguracion: {
                id: 0,
                nombre_sistema: "",
                alias: "",
                razon_social: "",
                nit: "",
                ciudad: "",
                dir: "",
                fono: "",
                web: "",
                actividad: "",
                correo: "",
                logo: "",
            },
            oOrdenVenta: {
                id: 0,
                sucursal_id: "",
                cliente_id: "",
                nit: "",
                venta_mayor: "NO",
                total: "0.00",
                detalle_ordens: [],
            },
            total_final: 0,
            literal: "",
            devolucion: null,
            total_cantidad_devolucion: 0,
        };
    },
    watch: {
        oOrdenVenta(newVal) {
            this.oOrdenVenta = newVal;
            if (this.imprime) {
                let self = this;
                setTimeout(function () {
                    self.imrpimirContenedor();
                }, 300);
            }
        },
    },
    mounted() {
        this.getOrdenVenta();
        this.getConfiguracion();
        this.loadingWindow.close();
    },
    methods: {
        getConfiguracion() {
            this.loading = true;
            let url = "/configuracion/getConfiguracion";
            axios.get(url).then((res) => {
                this.oConfiguracion = res.data.configuracion;
            });
        },
        getOrdenVenta() {
            axios.get("/admin/orden_ventas/" + this.id).then((response) => {
                this.oOrdenVenta = response.data;
                this.getDevolucion();
            });
        },
        getLiteral() {
            axios
                .get("/admin/orden_ventas/info/getLiteral", {
                    params: {
                        total: this.total_final,
                    },
                })
                .then((response) => {
                    this.literal = response.data;
                });
        },
        getDevolucion() {
            axios
                .get("/admin/orden_ventas/info/devolucions", {
                    params: {
                        id: this.oOrdenVenta.id,
                    },
                })
                .then((response) => {
                    this.devolucion = response.data.devolucion;
                    this.total_cantidad_devolucion =
                        response.data.total_cantidad_devolucion;
                    if (this.devolucion && this.total_cantidad_devolucion > 0) {
                        this.total_final = response.data.total_final;
                    } else {
                        this.total_final = this.oOrdenVenta.total_final;
                    }
                    this.getLiteral();
                });
        },
        imrpimirContenedor() {
            var divContents = document.getElementById("principal").innerHTML;
            var a = window.open("", "");
            a.document.write("<html>");
            a.document.write("<head>");
            a.document.write(
                `
                <style>
                    @page { margin: 0;}
                    body { width: 7cm !important;}

                    #principal{
                        width: 7cm !important;
                    }

                    #contenedor_imprimir {
                        font-size: 0.95em;
                        width: 7cm !important;
                        padding-top: 15px;
                        padding-bottom: 15px;
                        font-family: 'Times New Roman', Times, serif;
                    }

                    .elemento {
                        text-align: center;
                    }

                    .elemento.logo img {
                        width: 60%;
                    }

                    .separador {
                        padding: 0px;
                        margin: 0px;
                    }

                    .fono,
                    .lp {
                        font-size: 0.75em;
                    }

                    .txt_fo {
                        margin-top: 3px;
                        border-top: solid 1px black;
                    }

                    .detalle {
                        border-top: solid 1px black;
                        border-bottom: solid 1px black;
                    }

                    .act_eco {
                        font-size: 0.73em;
                    }

                    .info1 {
                        text-align: center;
                        font-weight: bold;
                        font-size: 0.75em;
                    }

                    .info2 {
                        text-align: center;
                        font-weight: bold;
                        font-size: 0.7em;
                    }

                    .izquierda {
                        text-align: left;
                        padding-left: 5px;
                    }

                    .derecha {
                        text-align: right;
                        padding-right: 5px;
                    }

                    .informacion {
                        padding: 5px;
                        width: 100%;
                    }

                    .bold {
                        font-weight: bold;
                    }

                    .cobro {
                        width: 100%;
                        padding: 5px;
                    }

                    .cobro table {
                        width: 100%;
                    }

                    .centreado {
                        text-align: center;
                    }

                    .cobro table tr td {
                        font-size: 0.9em;
                    }

                    .literal {
                        font-size: 0.7em;
                    }

                    .cod_control,
                    .fecha_emision {
                        font-size: 0.9em;
                    }

                    .cobro table {
                        border-collapse: collapse;
                    }

                    .cobro table tr.punteado td {
                        border-top: solid 1px black;
                        border-bottom: solid 1px black;
                    }

                    .qr img {
                        width: 160px;
                        height: 160px;
                    }

                    .total {
                        font-size: 0.9em !important;
                    }
                </style>
                `
            );
            a.document.write("</head>");
            a.document.write("<body >");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();
            a.print();
        },
    },
};
</script>
<style>
/* FACURA */
.contenedor_factura {
    font-size: 0.95em;
    width: 7cm !important;
    padding-top: 15px;
    padding-bottom: 15px;
    font-family: "Times New Roman", Times, serif;
}

.elemento {
    text-align: center;
}

.elemento.logo img {
    width: 60%;
}

.separador {
    padding: 0px;
    margin: 0px;
}

.fono,
.lp {
    font-size: 0.75em;
}

.txt_fo {
    margin-top: 3px;
    border-top: solid 1px black;
}

.detalle {
    border-top: solid 1px black;
    border-bottom: solid 1px black;
}

.act_eco {
    font-size: 0.73em;
}

.info1 {
    text-align: center;
    font-weight: bold;
    font-size: 0.75em;
}

.info2 {
    text-align: center;
    font-weight: bold;
    font-size: 0.7em;
}

.izquierda {
    text-align: left;
    padding-left: 5px;
}

.derecha {
    text-align: right;
    padding-right: 5px;
}

.informacion {
    padding: 5px;
    width: 100%;
}

.bold {
    font-weight: bold;
}

.cobro {
    width: 100%;
    padding: 5px;
}

.cobro table {
    width: 100%;
}

.centreado {
    text-align: center;
}

.cobro table tr td {
    font-size: 0.9em;
}

.literal {
    font-size: 0.7em;
}

.cod_control,
.fecha_emision {
    font-size: 0.9em;
}

.cobro table {
    border-collapse: collapse;
}

.cobro table tr.punteado td {
    border-top: solid 1px black;
    border-bottom: solid 1px black;
}

.qr img {
    width: 160px;
    height: 160px;
}

.total {
    font-size: 0.9em !important;
}
</style>
