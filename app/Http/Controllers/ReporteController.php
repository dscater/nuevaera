<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Configuracion;
use App\Models\DetalleOrden;
use App\Models\HistorialAccion;
use App\Models\KardexProducto;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\Reporte;
use App\Models\SucursalStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use TCPDF;

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

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

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
            $productos = SucursalStock::all();
            if ($filtro != 'todos') {
                if ($filtro == 'Producto') {
                    $productos = SucursalStock::where("producto_id", $producto_id)
                        ->get();
                }
            }
        }

        $array_kardex = [];
        $array_saldo_anterior = [];
        $sw_lugar = $lugar_id;
        foreach ($productos as $registro) {
            $kardex = KardexProducto::where("lugar", $sw_lugar)
                ->where('producto_id', $registro->producto_id)->get();
            $array_saldo_anterior[$registro->producto_id] = [
                'sw' => false,
                'saldo_anterior' => []
            ];
            if ($filtro == 'Rango de fechas') {
                $kardex = KardexProducto::where("lugar", $sw_lugar)
                    ->where('producto_id', $registro->producto_id)
                    ->whereBetween('fecha', [$fecha_ini, $fecha_fin])->get();
                // buscar saldo anterior si existe
                $saldo_anterior = KardexProducto::where("lugar", $sw_lugar)
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

        $lugar = $lugar_id;

        $array_dias = ['0' => 'Domingo', '1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles', '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado'];
        $array_meses = ['01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];


        // pdf
        $pdf = new Reporte();
        $pdf->SetTitle('Kardex');
        $pdf->AddPage();
        $pdf->setPrintHeader(false);
        $pdf->setY(18);
        $pdf->Cell(0, 15, "KARDEX DE PRODUCTOS", 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->Cell(0, 15, $array_dias[date('w')] . ', ' .  date('d') . ' de ' .
            $array_meses[date('m')] . ' de ' .  date('Y'), 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 15, '(Expresado en bolivianos)', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 10);
        foreach ($productos as $registro) {
            $y = $pdf->GetY();
            if ($y >= 240) {
                $pdf->AddPage();
            }
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(190, 10, $registro->producto->nombre, 1, 1, 'C');
            $pdf->MultiCell(15, 20, 'FECHA', 1, 'C', false, 0, $x = '',  $y = '',  $reseth = true,  $stretch = 0,  $ishtml = false,  $autopadding = true,  $maxh = 0,  $valign = 'M',  $fitcell = false);
            $pdf->MultiCell(35, 20, 'DETALLE', 1, 'C', false, 0);
            $pdf->MultiCell(60, 10, 'CANTIDADES', 1, 'C', false, 0);
            $pdf->MultiCell(20, 20, 'P/U', 1, 'C', false, 0);
            $pdf->MultiCell(60, 10, 'BOLIVIANOS', 1, 'C', false, 0);
            $pdf->Ln();
            $pdf->SetX(60);
            $pdf->MultiCell(20, 10, 'ENTRADA', 1, 'C', false, 0);
            $pdf->MultiCell(20, 10, 'SALIDA', 1, 'C', false, 0);
            $pdf->MultiCell(20, 10, 'SALDO', 1, 'C', false, 0);
            $pdf->SetX(140);
            $pdf->MultiCell(20, 10, 'ENTRADA', 1, 'C', false, 0);
            $pdf->MultiCell(20, 10, 'SALIDA', 1, 'C', false, 0);
            $pdf->MultiCell(20, 10, 'SALDO', 1, 'C', false, 0);
            $pdf->Ln();
            $pdf->SetFont('helvetica', '', 8);
            if (count($array_kardex[$registro->producto_id]) > 0 || $array_saldo_anterior[$registro->producto_id]["sw"]) {
                if ($array_saldo_anterior[$registro->producto_id]["sw"]) {
                    if ($y >= 260) {
                        $pdf->AddPage();
                    }
                    $pdf->MultiCell(15, 15, $y, 1, 'C', false, 0);
                    $pdf->MultiCell(35, 15, "SALDO ANTERIOR", 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, "", 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, "", 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $array_saldo_anterior[$registro->producto_id]["saldo_anterior"]["cantidad_saldo"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $registro->producto->precio, 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, "", 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, "", 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, number_format($array_saldo_anterior[$registro->producto_id]["saldo_anterior"]["monto_saldo"], 2, '.', ','), 1, 'C', false, 0);
                    $pdf->Ln();
                }
                foreach ($array_kardex[$registro->producto_id] as $value) {
                    $y = $pdf->GetY();
                    if ($y >= 260) {
                        $pdf->AddPage();
                    }
                    $pdf->MultiCell(15, 15, date("d/m/Y", strtotime($value["fecha"])) . ' ' . $y, 1, 'C', false, 0);
                    $pdf->MultiCell(35, 15, $value["detalle"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $value["cantidad_ingreso"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $value["cantidad_salida"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $value["cantidad_saldo"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, number_format($value["cu"], 2, '.', ','), 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $value["monto_ingreso"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, $value["monto_salida"], 1, 'C', false, 0);
                    $pdf->MultiCell(20, 15, number_format($value["monto_saldo"], 2, '.', ','), 1, 'C', false, 0);
                    $pdf->Ln();
                }
                $pdf->Ln();
            } else {
                $y = $pdf->GetY();
                if ($y >= 260) {
                    $pdf->AddPage();
                }
                $pdf->Cell(190, 10, "NO SE ENCONTRARON REGISTROS" . ' ' . $y, 1, 1, 'C');
                $pdf->Ln();
            }
        }
        // Output the PDF to the browser or save it to a file
        $pdf->Output('Kardex.pdf');
        exit;
    }

    public function orden_ventas(Request $request)
    {
        $filtro = $request->filtro;
        $producto_id = $request->producto_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

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

        $orden_ventas = OrdenVenta::all();
        if ($filtro != 'todos') {
            if ($filtro == 'Producto') {
                $orden_ventas = OrdenVenta::select("orden_ventas.*")
                    ->join("detalle_ordens", "detalle_ordens.orden_id", "=", "orden_ventas.id")
                    ->where("detalle_ordens.producto_id", $producto_id)
                    ->get();
            }
            if ($filtro == 'Rango de fechas') {
                $orden_ventas = OrdenVenta::whereBetween("fecha_registro", [$fecha_ini, $fecha_fin])->get();
            }
        }
        $pdf = PDF::loadView('reportes.orden_ventas', compact('orden_ventas'))->setPaper('legal', 'portrait');


        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->download('orden_ventas.pdf');
    }

    public function stock_productos(Request $request)
    {
        $request->validate(['lugar_id' => 'required']);
        $lugar_id =  $request->lugar_id;
        $filtro =  $request->filtro;
        $lugar = "ALMACEN";
        if ($lugar_id == 'ALMACEN') {
            if ($filtro != 'Todos') {
                $registros = Almacen::select("almacens.*")
                    ->join("productos", "productos.id", "=", "almacens.producto_id")
                    ->where("almacens.stock_actual", "<=", "productos.stock_min")
                    ->orderBy("productos.nombre")
                    ->get();
            } else {
                $registros = Almacen::select("almacens.*")
                    ->join("productos", "productos.id", "=", "almacens.producto_id")
                    ->orderBy("productos.nombre")
                    ->get();
            }
        } else {
            if ($filtro != 'Todos') {
                $registros = SucursalStock::select("sucursal_stocks.*")
                    ->join("productos", "productos.id", "=", "sucursal_stocks.producto_id")
                    ->where("sucursal_stocks.stock_actual", "<=", "productos.stock_min")
                    ->orderBy("productos.nombre")
                    ->get();
            } else {
                $registros = SucursalStock::select("sucursal_stocks.*")
                    ->join("productos", "productos.id", "=", "sucursal_stocks.producto_id")
                    ->orderBy("productos.nombre")
                    ->get();
            }
        }

        // pdf
        $pdf = new Reporte();
        $pdf->SetTitle('Kardex');
        $pdf->AddPage();
        $pdf->setPrintHeader(false);
        $pdf->setY(18);
        $pdf->Cell(0, 15, "STOCK DE PRODUCTOS", 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->Cell(0, 15, $lugar, 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->Cell(0, 15, "Expedido: " . date("d-m-Y"), 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->Ln(1);
        $pdf->setX(30);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(15, 10, 'N°', 1, 0, 'C', false);
        $pdf->Cell(100, 10, 'PRODUCTO',  1, 0, 'C', false);
        $pdf->Cell(40, 10, 'STOCK ACTUAL',  1, 0, 'C', false);
        $pdf->Ln();
        $cont = 1;
        foreach ($registros as $registro) {
            $pdf->SetFont('helvetica', '', 8);
            $y = $pdf->GetY();
            if ($y >= 260) {
                $pdf->AddPage();
            }
            $pdf->setX(30);
            $pdf->Cell(15, 10, $cont++, 1, 0, 'C', false);
            $pdf->Cell(100, 10, $registro->producto->nombre,  1, 0, 'C', false);
            $pdf->Cell(40, 10, $registro->stock_actual,  1, 0, 'C', false);
            $pdf->Ln();
        }
        // Output the PDF to the browser or save it to a file
        $pdf->Output('StockProductos.pdf');
        exit;
    }

    public function historial_accion(Request $request)
    {
        $historial_accions = HistorialAccion::orderBy("created_at", "desc")->get();

        if (isset($request->fecha_ini) && isset($request->fecha_fin)) {
            $historial_accions = HistorialAccion::with("user")->whereBetween("fecha", [$request->fecha_ini, $request->fecha_fin])->orderBy("created_at", "desc")->get();
        }

        $pdf = PDF::loadView('reportes.historial_accion', compact('historial_accions'))->setPaper('legal', 'portrait');


        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));
        return $pdf->download('historial_accions.pdf');
    }


    public function grafico_ingresos(Request $request)
    {
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $filtro =  $request->filtro;
        $producto_id =  $request->producto_id;

        if ($filtro == 'Producto') {
            $productos = Producto::select("productos.*")
                ->where("id", $producto_id)
                ->get();
        } else {
            $productos = Producto::select("productos.*")
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('detalle_ordens')
                        ->whereRaw('productos.id = detalle_ordens.producto_id');
                })
                ->get();
        }
        $data = [];
        foreach ($productos as $producto) {
            $cantidad = 0;
            if ($filtro == 'Rango de fechas') {
                $cantidad = DetalleOrden::select("detalle_ordens")
                    ->join("orden_ventas", "orden_ventas.id", "=", "detalle_ordens.orden_id")
                    ->where("orden_ventas.estado", "CANCELADO")
                    ->where("detalle_ordens.producto_id", $producto->id)
                    ->whereBetween("fecha_registro", [$fecha_ini, $fecha_fin])
                    ->sum("detalle_ordens.subtotal");
            } else {
                $cantidad = DetalleOrden::where("producto_id", $producto->id)
                    ->join("orden_ventas", "orden_ventas.id", "=", "detalle_ordens.orden_id")
                    ->where("orden_ventas.estado", "CANCELADO")
                    ->sum("subtotal");
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

    public function grafico_orden(Request $request)
    {
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $filtro =  $request->filtro;
        $producto_id =  $request->producto_id;

        if ($filtro == 'Producto') {
            $productos = Producto::select("productos.*")
                ->where("id", $producto_id)
                ->get();
        } else {
            $productos = Producto::select("productos.*")
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('detalle_ordens')
                        ->whereRaw('productos.id = detalle_ordens.producto_id');
                })
                ->get();
        }
        $data = [];
        foreach ($productos as $producto) {
            $cantidad = 0;
            if ($filtro == 'Rango de fechas') {
                $cantidad = count(DetalleOrden::select("detalle_ordens")
                    ->join("orden_ventas", "orden_ventas.id", "=", "detalle_ordens.orden_id")
                    ->where("detalle_ordens.producto_id", $producto->id)
                    ->whereBetween("fecha_registro", [$fecha_ini, $fecha_fin])
                    ->get());
            } else {
                $cantidad = count(DetalleOrden::where("producto_id", $producto->id)->get());
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
}
