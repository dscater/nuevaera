<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCPDF;

class Reporte extends TCPDF
{
    public function Header()
    {
        // if ($this->tocpage) or
        if ($this->page == 1) {
            $configuracion = Configuracion::first();
            $path_foto = public_path() . "/" . $configuracion->logo;
            $extension = pathinfo($path_foto, PATHINFO_EXTENSION);
            $this->Image($configuracion->path_image, 10, 10, 25, '', $extension, '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 15, $configuracion->razon_social, 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        } else {
            // *** replace the following parent::Header() with your code for other pages
            //parent::Header();
            // following will add your own logo ant text to other pages
            // $this->Image('http://localhost/other_pages_logo.png', 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            // $this->SetFont('helvetica', 'B', 14);
            // $this->Cell(0, 15, 'Other pages header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'B', 10);
        // Obtener el número de página actual
        $pageNumber = $this->getAliasNumPage();
        // Obtener el total de páginas
        $totalPages = $this->getAliasNbPages();
        // $pageNumber = str_replace(".", "", $pageNumber);
        // $pageNumber = str_replace(".", "", $totalPages);
        $this->Cell(0, 10, "Página $pageNumber de $totalPages", 0, 0, 'R');
    }
}
