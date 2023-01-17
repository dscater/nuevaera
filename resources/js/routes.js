import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        // INICIO
        {
            path: '/',
            name: 'inicio',
            component: require('./components/Inicio.vue').default
        },

        // LOGIN
        {
            path: '/login',
            name: 'login',
            component: require('./Auth.vue').default
        },

        // Usuarios
        {
            path: '/usuarios/perfil/:id',
            name: 'usuarios.perfil',
            component: require('./components/modulos/usuarios/perfil.vue').default,
            props: true
        },
        {
            path: '/usuarios',
            name: 'usuarios.index',
            component: require('./components/modulos/usuarios/index.vue').default
        },

        // sucursales
        {
            path: '/sucursals',
            name: 'sucursals.index',
            component: require('./components/modulos/sucursals/index.vue').default,
        },

        // cajas
        {
            path: '/cajas',
            name: 'cajas.index',
            component: require('./components/modulos/cajas/index.vue').default,
        },

        // proveedores
        {
            path: '/proveedors',
            name: 'proveedors.index',
            component: require('./components/modulos/proveedors/index.vue').default,
        },

        // productos
        {
            path: '/productos',
            name: 'productos.index',
            component: require('./components/modulos/productos/index.vue').default,
        },

        // grupos
        {
            path: '/grupos',
            name: 'grupos.index',
            component: require('./components/modulos/grupos/index.vue').default,
        },

        // tipo ingresos
        {
            path: '/tipo_ingresos',
            name: 'tipo_ingresos.index',
            component: require('./components/modulos/tipo_ingresos/index.vue').default,
        },

        // ingreso productos
        {
            path: '/ingreso_productos',
            name: 'ingreso_productos.index',
            component: require('./components/modulos/ingreso_productos/index.vue').default,
        },

        // tipo salidas
        {
            path: '/tipo_salidas',
            name: 'tipo_salidas.index',
            component: require('./components/modulos/tipo_salidas/index.vue').default,
        },

        // salida productos
        {
            path: '/salida_productos',
            name: 'salida_productos.index',
            component: require('./components/modulos/salida_productos/index.vue').default,
        },

        // Configuración
        {
            path: '/configuracion',
            name: 'configuracion',
            component: require('./components/modulos/configuracion/index.vue').default,
            props: true
        },

        // Reportes
        {
            path: '/reportes/usuarios',
            name: 'reportes.usuarios',
            component: require('./components/modulos/reportes/usuarios.vue').default,
            props: true
        },

        // PÁGINA NO ENCONTRADA
        {
            path: '*',
            component: require('./components/modulos/errors/404.vue').default
        },
    ],
    mode: 'history',
    linkActiveClass: 'active'
});