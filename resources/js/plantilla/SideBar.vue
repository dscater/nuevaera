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
                        v-if="permisos.includes('ingreso_productos.index')"
                    >
                        OPERACIONES
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('ingreso_productos.index')"
                    >
                        <router-link
                            :to="{ name: 'ingreso_productos.index' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Ingreso de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('salida_productos.index')"
                    >
                        <router-link
                            :to="{ name: 'salida_productos.index' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Salida de Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-header bg-navy"
                        v-if="
                            permisos.includes('users.index') ||
                            permisos.includes('sucursals.index') ||
                            permisos.includes('cajas.index') ||
                            permisos.includes('proveedors.index') ||
                            permisos.includes('productos.index') ||
                            permisos.includes('grupos.index') ||
                            permisos.includes('configuracion.index')
                        "
                    >
                        ADMINISTRACIÓN
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
                        v-if="permisos.includes('cajas.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'cajas.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Cajas</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('proveedors.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'proveedors.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Proveedores</p>
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
                            <i class="nav-icon fas fa-list"></i>
                            <p>Productos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('grupos.index')"
                    >
                        <router-link
                            exact
                            :to="{ name: 'grupos.index' }"
                            class="nav-link"
                            v-loading.fullscreen.lock="fullscreenLoading"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Grupos</p>
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
                        v-if="permisos.includes('reportes.usuarios')"
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
                    <li class="nav-header bg-navy">OTRAS OPCIONES</li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('tipo_ingresos.index')"
                    >
                        <router-link
                            :to="{ name: 'tipo_ingresos.index' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Tipo de Ingresos</p>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="permisos.includes('tipo_salidas.index')"
                    >
                        <router-link
                            :to="{ name: 'tipo_salidas.index' }"
                            class="nav-link"
                        >
                            <i class="nav-icon fas fa-list"></i>
                            <p>Tipo de Salidas</p>
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
