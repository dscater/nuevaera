<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\ImportancionAperturaController;
use App\Http\Controllers\IngresoProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdenVentaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SalidaProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TipoIngresoController;
use App\Http\Controllers\TipoSalidaController;
use App\Http\Controllers\TransferenciaProductoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// LOGIN
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// CONFIGURACIÓN
Route::get('/configuracion/getConfiguracion', [ConfiguracionController::class, 'getConfiguracion']);

Route::middleware(['auth'])->group(function () {
    Route::post('/configuracion/update', [ConfiguracionController::class, 'update']);

    Route::prefix('admin')->group(function () {
        // Usuarios
        Route::get('usuarios/getUsuario/{usuario}', [UserController::class, 'getUsuario']);
        Route::get('usuarios/userActual', [UserController::class, 'userActual']);
        Route::get('usuarios/getInfoBox', [UserController::class, 'getInfoBox']);
        Route::get('usuarios/getPermisos/{usuario}', [UserController::class, 'getPermisos']);
        Route::get('usuarios/getEncargadosRepresentantes', [UserController::class, 'getEncargadosRepresentantes']);
        Route::post('usuarios/actualizaContrasenia/{usuario}', [UserController::class, 'actualizaContrasenia']);
        Route::post('usuarios/actualizaFoto/{usuario}', [UserController::class, 'actualizaFoto']);
        Route::resource('usuarios', UserController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Sucursales
        Route::get('sucursals/getCajas', [SucursalController::class, 'getCajas']);
        Route::get('sucursals/sin_importacion', [SucursalController::class, 'sin_importacion']);
        Route::resource('sucursals', SucursalController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Cajas
        Route::get("cajas/cajas_sucursal", [CajaController::class, 'cajas_sucursal']);
        Route::resource('cajas', CajaController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Proveedores
        Route::resource('proveedors', ProveedorController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Grupos
        Route::resource('grupos', GrupoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Productos
        Route::get("productos/paginado", [ProductoController::class, 'paginado']);
        Route::get("productos/verifica_ventas", [ProductoController::class, 'verifica_ventas']);
        Route::get("productos/valida_stock", [ProductoController::class, 'valida_stock']);
        Route::get("productos/productos_sucursal", [ProductoController::class, 'productos_sucursal']);
        Route::get("productos/getStock", [ProductoController::class, 'getStock']);
        Route::get("productos/buscar_producto", [ProductoController::class, 'buscar_producto']);
        Route::resource('productos', ProductoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Tipo Ingresos
        Route::resource('tipo_ingresos', TipoIngresoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Ingreso productos
        Route::resource('ingreso_productos', IngresoProductoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Tipo Salidas
        Route::resource('tipo_salidas', TipoSalidaController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Salida productos
        Route::resource('salida_productos', SalidaProductoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Transferencia Productos
        Route::resource('transferencia_productos', TransferenciaProductoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Clientes
        Route::resource('clientes', ClienteController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Orden Ventas
        Route::post("orden_ventas/pdf/{orden_venta}", [OrdenVentaController::class, 'pdf']);
        Route::get("orden_ventas/info/getLiteral", [OrdenVentaController::class, 'getLiteral']);
        Route::get("orden_ventas/info/devolucions", [OrdenVentaController::class, 'getDevolucions']);
        Route::resource('orden_ventas', OrdenVentaController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // CREDITOS
        Route::resource('creditos', CreditoController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Devoluciones
        Route::resource('devolucions', DevolucionController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // Importacion apertura
        Route::get("importacion_aperturas/verifica_importacion", [ImportancionAperturaController::class, 'verifica_importacion']);
        Route::post("importacion_aperturas/importar_archivo", [ImportancionAperturaController::class, 'importar_archivo']);
        Route::post("importacion_aperturas/actualiza_stock", [ImportancionAperturaController::class, 'actualiza_stock']);
        Route::resource('importacion_aperturas', ImportancionAperturaController::class)->only([
            'index', 'store', 'update', 'destroy', 'show'
        ]);

        // REPORTES
        Route::post('reportes/usuarios', [ReporteController::class, 'usuarios']);
        Route::post('reportes/kardex', [ReporteController::class, 'kardex']);
        Route::post('reportes/orden_ventas', [ReporteController::class, 'orden_ventas']);
        Route::post('reportes/stock_productos', [ReporteController::class, 'stock_productos']);
    });
});


// ---------------------------------------
Route::get('/{optional?}', function () {
    return view('app');
})->name('base_path')->where('optional', '.*');
