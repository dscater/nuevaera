<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura</title>
    <style type="text/css">
        * {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        @page {
            margin-left: 0.5cm;
            margin-top: 0.5cm;
            margin-right: 0.5cm;
            margin-bottom: 0.5cm;
            size: 11cm 27.94cm;
        }

        body {
            position: relative;
        }

        table {
            page-break-before: avoid;
        }

        .titulo {
            margin-right: auto;
            margin-left: auto;
            margin-bottom: auto;
            width: 300px;
        }

        .titulo p.emp {
            text-align: center;
            font-size: 0.95em;
            padding: 0;
            margin-bottom: -10px;
        }

        .titulo p.dir {
            text-align: center;
            font-size: 0.60em;
            padding: 0;
            margin-bottom: -10px;
        }

        .titulo p.activi {
            text-align: center;
            font-size: 0.60em;
            padding: 0;
        }

        .titulo_derecha {
            position: absolute;
            top: -70px;
            right: -55px;
            width: 180px;
        }

        .titulo_derecha h2 {
            text-align: center;
            font-size: 0.85em;
            color: #28a745;
            font-family: Calibri, sans-serif;
            border: solid 1px #28a745;
            background: #dcf8e2;
            margin-bottom: 2px;
            width: 185px;
        }

        .titulo_derecha .contenedor_info {
            padding-left: 5px;
            width: 100%;
            border: solid 1px #28a745;
        }

        .titulo_derecha .contenedor_info p.info {
            font-size: 0.55em;
        }

        .logo {
            width: 130;
            height: 120px;
            position: absolute;
            top: -65px;
            left: -35px;
        }

        .datos_factura {
            font-size: 0.75em;
            width: 100%;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .datos_factura .c1 {
            width: 10%;
        }

        .datos_factura .c2 {
            width: 5%;
        }

        .factura {
            border-collapse: collapse;
            position: relative;
            width: 100%;
            font-size: 0.7em;
        }

        .factura thead tr {
            background: #28a745;
            color: white;
        }

        .factura thead tr th {
            text-align: center;
        }

        .factura tbody tr td {
            text-align: center;
        }

        .factura tbody tr.total td:first-child {
            text-align: right;
            padding-right: 15px;
        }

        .factura tbody tr.total_final td:nth-child(4n),
        tr.total_final td:nth-child(5n) {
            background: #28a745;
            color: white;
            font-weight: bold;
        }

        .factura tbody tr.total_literal td:nth-child(3n) {
            text-align: right;
            padding-right: 15px;
        }

        .factura tbody tr.total_literal td:nth-child(4n) {
            text-align: left;
            padding-left: 15px;
        }

        .codigos {
            margin-top: 35px;
            width: 70%;
        }

        .codigos tbody tr td {
            font-size: 0.7em;
        }

        .codigos tbody tr td.c1 {
            width: 15%;
        }

        .codigos tbody tr td.c2 {
            width: 65%;
        }

        .codigos tbody tr td.qr {
            width: 30%;
        }

        .qr {
            width: 120px;
            height: 120px;
        }

        .qr img {
            width: 100%;
            height: 100%;
        }

        .info1 {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 0.6em;
        }

        .info2 {
            text-align: center;
            font-weight: bold;
            font-size: 0.5em;
        }
    </style>
</head>

<body>
    <h1>ORDEN DE VENTA</h1>
</body>

</html>
