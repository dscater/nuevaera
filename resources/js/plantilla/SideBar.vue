<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <router-link
            exact
            :to="{ name: 'inicio' }"
            class="brand-link bg-primary"
        >
            <img
                :src="configuracion.path_image"
                alt="Logo"
                class="brand-image img-circle elevation-3"
                style="opacity: 0.8"
            />
            <span
                class="brand-text font-weight-light"
                v-text="configuracion.alias"
            ></span>
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img
                        :src="user_sidebar.path_image"
                        class="img-circle elevation-2"
                        alt="User Image"
                    />
                </div>
                <div class="info">
                    <router-link
                        exact
                        :to="{
                            name: 'usuarios.perfil',
                            params: { id: user_sidebar.id },
                        }"
                        class="d-block"
                        v-text="user_sidebar.full_name"
                    ></router-link>
                </div>
            </div>

            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input
                        class="form-control form-control-sidebar bg-white"
                        type="search"
                        placeholder="Buscar Modulo"
                        aria-label="Search"
                    />
                    <div class="input-group-append">
                        <button class="btn btn-sidebar bg-white">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column text-xs nav-flat"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false"
                >
                    <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <router-link
                            exact
                            :to="{ name: 'inicio' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-home"></i>
                            <p>Inicio</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-header bg-navy"
                        v-if="
                            permisos.includes('cobros.index')
                        "
                    >
                        OPERACIONES
                    </li>
                    <li
                        class="nav-header bg-navy"
                        v-if="
                            permisos.includes('sucursals.index') ||
                            permisos.includes('users.index') ||
                            permisos.includes('empleados.index') ||
                            permisos.includes('clientes.index') ||
                            permisos.includes('productos.index') ||
                            permisos.includes('accesos.index') ||
                            permisos.includes('maquinas.index') ||
                            permisos.includes('plans.index') ||
                            permisos.includes('configuracion.index')
                        "
                    >
                        ADMINISTRACIÓN
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('clientes.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'clientes.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-users"></i>
                            <p>Clientes</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('productos.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'productos.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('accesos.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'accesos.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Acceso al Gimnasio</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('maquinas.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'maquinas.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Maquinas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('mantenimiento_maquinas.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'mantenimiento_maquinas.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Mantenimiento de Maquinas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('empleados.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'empleados.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-users"></i>
                            <p>Empleados</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('sucursals.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'sucursals.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Sucursales</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('plans.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'plans.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-money-check"></i>
                            <p>Planes</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('usuarios.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'usuarios.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('configuracion.index')"
                    >
                        <router-link
                            :to="{ name: 'configuracion' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configuración</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-header bg-navy"
                        v-if="
                            permisos.includes('reportes.usuarios') ||
                            permisos.includes('reportes.clientes') ||
                            permisos.includes('reportes.empleados') ||
                            permisos.includes('reportes.maquinas') ||
                            permisos.includes(
                                'reportes.mantenimiento_maquinas'
                            ) ||
                            permisos.includes('reportes.inscripcions') ||
                            permisos.includes('reportes.accesos') ||
                            permisos.includes('reportes.cobros') ||
                            permisos.includes('reportes.productos') ||
                            permisos.includes('reportes.ingreso_productos') ||
                            permisos.includes('reportes.stock_productos') ||
                            permisos.includes('reportes.venta_productos') ||
                            permisos.includes('reportes.grafico_ventas') ||
                            permisos.includes('reportes.grafico_cobros')
                        "
                    >
                        REPORTES
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.usuarios')"
                    >
                        <router-link
                            :to="{ name: 'reportes.usuarios' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Usuarios</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.clientes')"
                    >
                        <router-link
                            :to="{ name: 'reportes.clientes' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Clientes</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.empleados')"
                    >
                        <router-link
                            :to="{ name: 'reportes.empleados' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Empleados</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.maquinas')"
                    >
                        <router-link
                            :to="{ name: 'reportes.maquinas' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Máquinas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.mantenimiento_maquinas')"
                    >
                        <router-link
                            :to="{ name: 'reportes.mantenimiento_maquinas' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Mantenimiento de Máquinas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.inscripcions')"
                    >
                        <router-link
                            :to="{ name: 'reportes.inscripcions' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Inscripciones</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.accesos')"
                    >
                        <router-link
                            :to="{ name: 'reportes.accesos' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Control de Acceso</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.cobros')"
                    >
                        <router-link
                            :to="{ name: 'reportes.cobros' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Cobros</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.productos')"
                    >
                        <router-link
                            :to="{ name: 'reportes.productos' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.ingreso_productos')"
                    >
                        <router-link
                            :to="{ name: 'reportes.ingreso_productos' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Ingresos de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.stock_productos')"
                    >
                        <router-link
                            :to="{ name: 'reportes.stock_productos' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Stock de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.venta_productos')"
                    >
                        <router-link
                            :to="{ name: 'reportes.venta_productos' }"
                            class="nav-link"
                        >
                            <i class="fas fa-file-pdf nav-icon"></i>
                            <p>Lista de Ventas de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.grafico_ventas')"
                    >
                        <router-link
                            :to="{ name: 'reportes.grafico_ventas' }"
                            class="nav-link"
                        >
                            <i class="fas fa-chart-bar nav-icon"></i>
                            <p>Gráfico Ventas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('reportes.grafico_cobros')"
                    >
                        <router-link
                            :to="{ name: 'reportes.grafico_cobros' }"
                            class="nav-link"
                        >
                            <i class="fas fa-chart-bar nav-icon"></i>
                            <p>Gráfico Cobros</p>
                        </router-link>
                    </li>
                    <li class="nav-header bg-navy">OTRAS OPCIONES</li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('categorias.index')"
                    >
                        <router-link
                            exact
                            :to="{
                                name: 'categorias.index',
                                params: { id: user_sidebar.id },
                            }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>Categorías</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link
                            exact
                            :to="{
                                name: 'usuarios.perfil',
                                params: { id: user_sidebar.id },
                            }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-user"></i>
                            <p>Perfil</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <a
                            href="#"
                            class="nav-link"
                            @click.prevent="logout()"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="fas fa-power-off nav-icon"></i>
                            <p>Salir</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
export default {
    props: ["user_sidebar", "configuracion"],
    data() {
        return {
            user: JSON.parse(localStorage.getItem("user")),
            fullscreenLoading: false,
            permisos: localStorage.getItem("permisos"),
        };
    },
    methods: {
        logout() {
            this.fullscreenLoading = true;
            axios.post("/logout").then((res) => {
                setTimeout(function () {
                    localStorage.clear();
                    location.reload();
                    this.$router.push({ name: "login" });
                }, 500);
            });
        },
    },
};
</script>

<style>
.user-panel .info {
    display: flex;
    height: 100%;
    align-items: center;
}
.user-panel .info a {
    font-size: 0.8em;
}
</style>
