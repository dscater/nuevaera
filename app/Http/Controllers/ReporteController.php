<?php

namespace App\Http\Controllers;

use App\Models\Acceso;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Cobro;
use App\Models\DetalleVenta;
use App\Models\Empleado;
use App\Models\IngresoProducto;
use App\Models\Inscripcion;
use App\Models\KardexProducto;
use App\Models\MantenimientoMaquina;
use App\Models\Maquina;
use App\Models\OrdenVenta;
use App\Models\Plan;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\SucursalStock;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController extends Controller
{
    public function usuarios(Request $request)
    {
        $filtro =  $request->filtro;
        $usuarios = User::where('id', '!=', 1)->orderBy("paterno", "ASC")->get();

        if ($filtro == 'Tipo de usuario') {
            $request->validate([
                'tipo' => 'required',
            ]);
            $usuarios = User::where('id', '!=', 1)->where('tipo', $request->tipo)->orderBy("paterno", "ASC")->get();
        }

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setOption("public_path", public_path())->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->download('Usuarios.pdf');
    }

    public function kardex(Request $request)
    {
        $filtro = $request->filtro;
        $lugar_id = $request->lugar_id;
        $producto_id = $request->producto_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        $request->validate([
            'lugar_id' => 'required',
        ]);

        if ($request->filtro == 'Producto') {
            $request->validate([
                'producto_id' => 'required',
            ]);
        }

        if ($request->filtro == 'Rango de fechas') {
            $request->validate([
                'fecha_ini' => 'required|date',
                'fecha_fin' => 'required|date',
            ]);
        }

        if ($lugar_id == 'ALMACEN') {
            $productos = Almacen::all();
            if ($filtro != 'todos') {
                if ($filtro == 'Producto') {
                    $productos = Almacen::where("producto_id", $producto_id)->get();
                }
            }
        } else {
            $productos = SucursalStock::where("sucursal_id", $lugar_id)->get();
            if ($filtro != 'todos') {
                if ($filtro == 'Producto') {
                    $productos = SucursalStock::where("sucursal_id", $lugar_id)
                        ->where("producto_id", $producto_id)
                        ->get();
                }
            }
        }

        $array_kardex = [];
        $array_saldo_anterior = [];
        $sw_lugar = "ALMACEN";
        if ($lugar_id == 'ALMACEN') {
            $lugar_id = 0;
        } else {
            $sw_lugar = 'SUCURSAL';
        }
        foreach ($productos as $registro) {
            $kardex = KardexProducto::where("lugar", $sw_lugar)
                ->where("lugar_id", $lugar_id)
                ->where('producto_id', $registro->producto_id)->get();
            $array_saldo_anterior[$registro->producto_id] = [
                'sw' => false,
                'saldo_anterior' => []
            ];
            if ($filtro == 'Rango de fechas') {
                $kardex = KardexProducto::where("lugar", $sw_lugar)
                    ->where("lugar_id", $lugar_id)
                    ->where('producto_id', $registro->producto_id)
                    ->whereBetween('fecha', [$fecha_ini, $fecha_fin])->get();
                // buscar saldo anterior si existe
                $saldo_anterior = KardexProducto::where("lugar", $sw_lugar)
                    ->where("lugar_id", $lugar_id)
                    ->where('producto_id', $registro->producto_id)
                    ->where('fecha', '<', $fecha_ini)
                    ->orderBy('created_at', 'asc')->get()->last();
                if ($saldo_anterior) {
                    $cantidad_saldo = $saldo_anterior->cantidad_saldo;
                    $monto_saldo = $saldo_anterior->monto_saldo;
                    $array_saldo_anterior[$registro->producto_id] = [
                        'sw' => true,
                        'saldo_anterior' => [
                            'cantidad_saldo' => $cantidad_saldo,
                            'monto_saldo' => $monto_saldo,
                        ]
                    ];
                }
            }
            $array_kardex[$registro->producto_id] = $kardex;
        }

        $lugar = "ALMACEN";
        if ($lugar_id != "ALMACEN") {
            $lugar = Sucursal::find($lugar_id)->nombre;
        }

        $pdf = PDF::loadView('reportes.kardex', compact('productos', 'array_kardex', 'array_saldo_anterior', 'lugar'))->setPaper('letter', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->stream('kardex.pdf');
    }

    public function orden_ventas(Request $request)
    {
        $filtro = $request->filtro;
        $lugar_id = $request->lugar_id;
        $producto_id = $request->producto_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $request->validate([
            'lugar_id' => 'required',
        ]);

        if ($filtro == 'Producto') {
            $request->validate([
                'producto_id' => 'required',
            ]);
        }
        if ($filtro == 'Rango de fechas') {
            $request->validate([
                'fecha_ini' => 'required|date',
                'fecha_fin' => 'required|date',
            ]);
        }

        $orden_ventas = OrdenVenta::where("sucursal_id", $lugar_id)->get();
        if ($filtro != 'todos') {
            if ($filtro == 'Producto') {
                $orden_ventas = OrdenVenta::select("orden_ventas.*")
                    ->join("detalle_ordens", "detalle_ordens.orden_id", "=", "orden_ventas.id")
                    ->where("detalle_ordens.producto_id", $producto_id)
                    ->where("orden_ventas.sucursal_id", $lugar_id)
                    ->get();
            }
            if ($filtro == 'Rango de fechas') {
                $orden_ventas = OrdenVenta::where("sucursal_id", $lugar_id)
                    ->whereBetween("fecha_registro", [$fecha_ini, $fecha_fin])->get();
            }
        }
        $pdf = PDF::loadView('reportes.orden_ventas', compact('orden_ventas'))->setOption("public_path", public_path())->setPaper('legal', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->download('orden_ventas.pdf');
    }

    public function stock_productos(Request $request)
    {
        $request->validate(['lugar_id' => 'required']);
        $lugar_id =  $request->lugar_id;
        $lugar = "ALMACEN";
        if ($lugar_id == 'ALMACEN') {
            $registros = Almacen::select("almacens.*")->join("productos", "productos.id", "=", "almacens.producto_id")->orderBy("productos.nombre")->get();
        } else {
            $registros = SucursalStock::select("sucursal_stocks.*")
                ->join("productos", "productos.id", "=", "sucursal_stocks.producto_id")
                ->where("sucursal_id", $lugar_id)->orderBy("productos.nombre")->get();
            $lugar = Sucursal::find($lugar_id)->nombre;
        }

        $pdf = PDF::loadView('reportes.stock_productos', compact('registros', 'lugar'))->setPaper('legal', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));
        return $pdf->download('stock_productos.pdf');
    }

    public function ingreso_productos(Request $request)
    {
        $request->validate(['sucursal_id' => 'required']);
        $sucursal_id =  $request->sucursal_id;
        $producto_id =  $request->producto_id;
        $categoria_id =  $request->categoria_id;
        $filtro =  $request->filtro;
        $ingreso_productos = IngresoProducto::where("sucursal_id", $sucursal_id)->get();

        if ($filtro == 'Categoría' && $categoria_id != 'todos') {
            $request->validate(['categoria_id' => 'required']);
            $ingreso_productos = IngresoProducto::select("ingreso_productos.*")
                ->join("productos", "productos.id", "=", "ingreso_productos.producto_id")
                ->where("ingreso_productos.sucursal_id", $sucursal_id)
                ->where('productos.categoria_id', $categoria_id)
                ->get();
        }

        if ($filtro == 'Producto' && $producto_id != 'todos') {
            $request->validate(['producto_id' => 'required']);
            $ingreso_productos = IngresoProducto::where("sucursal_id", $sucursal_id)
                ->where('producto_id', $producto_id)
                ->get();
        }

        $pdf = PDF::loadView('reportes.ingreso_productos', compact('ingreso_productos'))->setPaper('legal', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));
        return $pdf->download('ingreso_productos.pdf');
    }

    public function venta_productos(Request $request)
    {
        $request->validate(['sucursal_id' => 'required']);
        $sucursal_id =  $request->sucursal_id;
        $producto_id =  $request->producto_id;
        $categoria_id =  $request->categoria_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $filtro =  $request->filtro;

        $detalle_ventas = DetalleVenta::select("detalle_ventas.*")
            ->join("ventas", "ventas.id", "=", "detalle_ventas.venta_id")
            ->where("ventas.sucursal_id", $sucursal_id)->get();


        if ($filtro == 'Categoría' && $categoria_id != 'todos') {
            $detalle_ventas = DetalleVenta::select("detalle_ventas.*")
                ->join("ventas", "ventas.id", "=", "detalle_ventas.venta_id")
                ->join("productos", "productos.id", "=", "detalle_ventas.producto_id")
                ->where("productos.categoria_id", $categoria_id)
                ->where("ventas.sucursal_id", $sucursal_id)
                ->get();
        }

        if ($filtro == 'Producto' && $producto_id != 'todos') {
            $detalle_ventas = DetalleVenta::select("detalle_ventas.*")
                ->join("ventas", "ventas.id", "=", "detalle_ventas.venta_id")
                ->where("detalle_ventas.producto_id", $producto_id)
                ->where("ventas.sucursal_id", $sucursal_id)
                ->get();
        }

        if ($filtro == 'Rango de fechas') {
            $detalle_ventas = DetalleVenta::select("detalle_ventas.*")
                ->join("ventas", "ventas.id", "=", "detalle_ventas.venta_id")
                ->whereBetween("ventas.fecha", [$fecha_ini, $fecha_fin])
                ->get();
        }

        $pdf = PDF::loadView('reportes.venta_productos', compact('detalle_ventas'))->setPaper('legal', 'portrait');
        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));
        return $pdf->download('venta_productos.pdf');
    }
    public function grafico_ventas(Request $request)
    {
        $request->validate(['sucursal_id' => 'required']);
        $sucursal_id =  $request->sucursal_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $filtro =  $request->filtro;

        $productos = Producto::where("sucursal_id", $sucursal_id)->get();
        $data = [];
        foreach ($productos as $producto) {
            $cantidad = 0;
            if ($filtro == 'Rango de fechas') {
                $cantidad = DetalleVenta::select("detalle_ventas")
                    ->join("ventas", "ventas.id", "=", "detalle_ventas.venta_id")
                    ->where("producto_id", $producto->id)
                    ->whereBetween("fecha", [$fecha_ini, $fecha_fin])
                    ->sum("detalle_ventas.subtotal");
            } else {
                $cantidad = DetalleVenta::where("producto_id", $producto->id)->sum("subtotal");
            }
            $data[] = [$producto->nombre, $cantidad ? (float)$cantidad : 0];
        }

        $fecha = date("d/m/Y");
        return response()->JSON([
            "sw" => true,
            "datos" => $data,
            "fecha" => $fecha
        ]);
    }
    public function grafico_cobros(Request $request)
    {
        $request->validate(['sucursal_id' => 'required']);
        $sucursal_id =  $request->sucursal_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $filtro =  $request->filtro;

        $plans = Plan::where("sucursal_id", $sucursal_id)->get();
        $data = [];
        foreach ($plans as $plan) {
            $cantidad = 0;
            if ($filtro == 'Rango de fechas') {
                $cantidad = Cobro::select("cobros")
                    ->join("inscripcions", "inscripcions.id", "=", "cobros.inscripcion_id")
                    ->join("plans", "plans.id", "=", "inscripcions.plan_id")
                    ->where("inscripcions.plan_id", $plan->id)
                    ->whereBetween("fecha_cobro", [$fecha_ini, $fecha_fin])
                    ->sum("plans.costo");
            } else {
                $cantidad = Cobro::select("cobros")
                    ->join("inscripcions", "inscripcions.id", "=", "cobros.inscripcion_id")
                    ->join("plans", "plans.id", "=", "inscripcions.plan_id")
                    ->where("inscripcions.plan_id", $plan->id)
                    ->sum("plans.costo");
            }
            $data[] = [$plan->nombre, $cantidad ? (float)$cantidad : 0];
        }

        $fecha = date("d/m/Y");
        return response()->JSON([
            "sw" => true,
            "datos" => $data,
            "fecha" => $fecha
        ]);
    }
}
