ALTER TABLE `detalle_ordens` CHANGE `cantidad` `cantidad` DOUBLE NOT NULL;

ALTER TABLE `sucursal_stocks` CHANGE `stock_actual` `stock_actual` DOUBLE NOT NULL;

ALTER TABLE `almacens` CHANGE `stock_actual` `stock_actual` DOUBLE NOT NULL;

ALTER TABLE `devolucion_detalles` CHANGE `cantidad` `cantidad` DOUBLE NOT NULL;

ALTER TABLE `ingreso_productos` CHANGE `cantidad` `cantidad` DOUBLE NOT NULL;

ALTER TABLE `productos` CHANGE `stock_min` `stock_min` DOUBLE NOT NULL;

ALTER TABLE `salida_productos` CHANGE `cantidad` `cantidad` DOUBLE NOT NULL;

ALTER TABLE `transferencia_productos` CHANGE `cantidad` `cantidad` DOUBLE NOT NULL;

ALTER TABLE `clientes` CHANGE `ci` `ci` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `ci_exp` `ci_exp` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, CHANGE `fono` `fono` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;

ALTER TABLE `ingreso_productos` CHANGE `precio_compra` `precio_compra` DECIMAL(8,2) NOT NULL DEFAULT '0';