-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema tecnogamerbd
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema tecnogamerbd
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `tecnogamerbd` DEFAULT CHARACTER SET utf8mb4 ;
USE `tecnogamerbd` ;

-- -----------------------------------------------------
-- Table `tecnogamerbd`.`categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`categoria` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`categoria` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `estado` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`etiqueta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`etiqueta` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`etiqueta` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `id_categoria` INT(11) NOT NULL,
  `estado` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_etiqueta_categoria` (`id_categoria` ASC),
  CONSTRAINT `fk_etiqueta_categoria`
    FOREIGN KEY (`id_categoria`)
    REFERENCES `tecnogamerbd`.`categoria` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`rol`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`rol` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`rol` (
  `id` INT NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`usuario` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(30) NOT NULL,
  `apellido` VARCHAR(30) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `usuario` VARCHAR(10) NOT NULL,
  `password` VARCHAR(60) NOT NULL,
  `sexo` CHAR(1) NOT NULL,
  `id_rol` INT(1) NOT NULL,
  `estado` INT(1) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_usuario_rol_idx` (`id_rol` ASC),
  CONSTRAINT `fk_usuario_rol`
    FOREIGN KEY (`id_rol`)
    REFERENCES `tecnogamerbd`.`rol` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`publicacion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`publicacion` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`publicacion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `estado` INT(11) NOT NULL,
  `fecha` DATE NOT NULL,
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publicacion_usuario` (`id_usuario` ASC),
  CONSTRAINT `fk_publicacion_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`imagen`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`imagen` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`imagen` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT(11) NOT NULL,
  `ruta_imagen` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_imagen_publicacion` (`id_publicacion` ASC),
  CONSTRAINT `fk_imagen_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`publicacion_etiqueta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`publicacion_etiqueta` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`publicacion_etiqueta` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT(11) NOT NULL,
  `id_etiqueta` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publicacion_etiqueta_etiqueta` (`id_etiqueta` ASC),
  CONSTRAINT `fk_publicacion_etiqueta_etiqueta`
    FOREIGN KEY (`id_etiqueta`)
    REFERENCES `tecnogamerbd`.`etiqueta` (`id`),
  CONSTRAINT `fk_publicacion_etiqueta_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`suscripcion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`suscripcion` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`suscripcion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_autor` INT(11) NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_suscripcion_autor` (`id_autor` ASC),
  CONSTRAINT `fk_suscripcion_autor`
    FOREIGN KEY (`id_autor`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`),
  CONSTRAINT `fk_suscripcion_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`permiso`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`permiso` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`permiso` (
  `id` INT NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` VARCHAR(45) NOT NULL,
  `url` VARCHAR(45) NULL,
  `menu_id` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`rol_permiso`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`rol_permiso` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`rol_permiso` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_rol` INT NOT NULL,
  `id_permiso` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rol_permiso_rol_idx` (`id_rol` ASC),
  INDEX `fk_rol_permiso_permiso_idx` (`id_permiso` ASC),
  CONSTRAINT `fk_rol_permiso_rol`
    FOREIGN KEY (`id_rol`)
    REFERENCES `tecnogamerbd`.`rol` (`id`)
    ON DELETE NO ACTION
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_rol_permiso_permiso`
    FOREIGN KEY (`id_permiso`)
    REFERENCES `tecnogamerbd`.`permiso` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`megusta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`megusta` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`megusta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `fecha` DATE NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_megusta_usuario_idx` (`id_usuario` ASC),
  INDEX `fk_megusta_publicacion_idx` (`id_publicacion` ASC),
  CONSTRAINT `fk_megusta_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_megusta_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`avatar`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`avatar` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`avatar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `ruta_imagen` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_avatar_usuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_avatar_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`comentario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`comentario` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`comentario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(200) NOT NULL,
  `id_publicacion` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `fecha` DATE NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comentario_publicacion_idx` (`id_publicacion` ASC),
  INDEX `fk_comentario_usuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_comentario_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentario_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`calificacion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`calificacion` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`calificacion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `id_publicacion` INT NOT NULL,
  `estrellas` SMALLINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_calificacion_publicacion_idx` (`id_publicacion` ASC),
  INDEX `fk_calificacion_usuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_calificacion_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_calificacion_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`reporte`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`reporte` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`reporte` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_publicacion` INT NOT NULL,
  `id_reportador` INT NOT NULL,
  `comentario` VARCHAR(200) NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` INT(1) NOT NULL,
  `id_admin` INT NULL,
  `fecha_atendido` DATE NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_reporte_publicacion_idx` (`id_publicacion` ASC),
  INDEX `fk_reporte_reportador_idx` (`id_reportador` ASC),
  INDEX `fk_reporte_admin_idx` (`id_admin` ASC),
  CONSTRAINT `fk_reporte_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reporte_reportador`
    FOREIGN KEY (`id_reportador`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reporte_admin`
    FOREIGN KEY (`id_admin`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`solicitud_categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`solicitud_categoria` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`solicitud_categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comentario` VARCHAR(45) NOT NULL,
  `categoria` VARCHAR(45) NOT NULL,
  `id_usuario` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_solicitud_categoria_usuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_solicitud_categoria_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`solicitud_categoria_etiqueta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`solicitud_categoria_etiqueta` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`solicitud_categoria_etiqueta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_solicitud_categoria` INT NOT NULL,
  `etiqueta` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_solicitud_categoria_etiqueta_categoria_idx` (`id_solicitud_categoria` ASC),
  CONSTRAINT `fk_solicitud_categoria_etiqueta_categoria`
    FOREIGN KEY (`id_solicitud_categoria`)
    REFERENCES `tecnogamerbd`.`solicitud_categoria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`solicitud_creador_contenido`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`solicitud_creador_contenido` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`solicitud_creador_contenido` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `tiene_experiencia` INT NOT NULL,
  `motivo` VARCHAR(200) NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_solicitud_cc_usuario_idx` (`id_usuario` ASC),
  CONSTRAINT `fk_solicitud_cc_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `tecnogamerbd`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tecnogamerbd`.`notificacion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tecnogamerbd`.`notificacion` ;

CREATE TABLE IF NOT EXISTS `tecnogamerbd`.`notificacion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_suscripcion` INT NOT NULL,
  `id_publicacion` INT NOT NULL,
  `visto` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_notificacion_suscripcion_idx` (`id_suscripcion` ASC),
  INDEX `fk_notificacion_publicacion_idx` (`id_publicacion` ASC),
  CONSTRAINT `fk_notificacion_suscripcion`
    FOREIGN KEY (`id_suscripcion`)
    REFERENCES `tecnogamerbd`.`suscripcion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_notificacion_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `tecnogamerbd`.`publicacion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`categoria`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`categoria` (`id`, `nombre`, `estado`) VALUES (1, 'Consolas', DEFAULT);
INSERT INTO `tecnogamerbd`.`categoria` (`id`, `nombre`, `estado`) VALUES (2, 'Generos', DEFAULT);
INSERT INTO `tecnogamerbd`.`categoria` (`id`, `nombre`, `estado`) VALUES (3, 'Hardware', DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`etiqueta`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (1, 'Playstation', 1, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (2, 'XBox', 1, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (3, 'Nintendo', 1, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (4, 'Accion', 2, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (5, 'Aventura', 2, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (6, 'Terror', 2, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (7, 'Disparos', 2, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (8, 'Placa de Video', 3, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (9, 'Microprocesador', 3, 1);
INSERT INTO `tecnogamerbd`.`etiqueta` (`id`, `nombre`, `id_categoria`, `estado`) VALUES (10, 'Motherboard', 3, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`rol`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`rol` (`id`, `nombre`) VALUES (1, 'Usuario');
INSERT INTO `tecnogamerbd`.`rol` (`id`, `nombre`) VALUES (2, 'Creador de Contenido');
INSERT INTO `tecnogamerbd`.`rol` (`id`, `nombre`) VALUES (3, 'Administrador');

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`usuario` (`id`, `nombre`, `apellido`, `email`, `usuario`, `password`, `sexo`, `id_rol`, `estado`) VALUES (1, 'Tecno', 'Gamer', 'admin@tecnogamer.com', 'admin', '$2y$10$.sB4RH0EetxFqPWlV920euA8Ll4CEWPxu1RPmQvy08269wMW3AZni', 'F', 3, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`permiso`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (1, 'MI_PERFIL', 'Mi Perfil', 'perfil.html', '#miperfil');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (2, 'ABM_PUBLICACION', 'ABM Publicacion', 'alta_publicacion.html', '#crear-publicacion');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (3, 'ABM_ETIQUETAS', 'ABM Etiquetas', 'abm_etiquetas.html', '#abm-etiquetas');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (4, 'LISTA_SUSCRIPCIONES', 'Lista Suscriciones', 'lista_suscriciones.html', '#suscriciones');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (5, 'ABM_CATEGORIAS', 'ABM Categorias', 'abm_categorias.html', '#abm-categorias');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (6, 'SOLICITUD_CATEGORIA', 'Solicitud Categoria', 'solicitud_categoria.html', '#solicitud-categoria');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (7, 'ABM_USUARIOS', 'ABM Usuarios', 'abm_usuarios.html', '#abm-usuarios');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (8, 'VER_SOLICITUDES_CATEGORIAS', 'Ver Solicitudes Categoria', 'ver_solicitudes_categoria.html', '#ver-solicitudes-categoria');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (9, 'VER_REPORTES', 'Ver Reportes', 'ver_reportes.html', '#ver-reportes');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (10, 'CALIFICAR_PUBLICACION', 'Calificar Publicacion', NULL, NULL);
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (11, 'REPORTAR_PUBLICACION', 'Reportar Publicacion', NULL, NULL);
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (12, 'SUSCRIBIRSE', 'Suscribirse', NULL, NULL);
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (13, 'SOLICITUD_CC', 'Solicitud Creador Contenido', 'solicitud_cc.html', '#solicitud-cc');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (14, 'EDITAR_PERFIL', 'Editar perfil', 'editar_perfil.html', '#editar-perfil-menu-link');
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (15, 'RECIBIR_NOTIFICACION', 'Recibir notificacion', NULL, NULL);
INSERT INTO `tecnogamerbd`.`permiso` (`id`, `nombre`, `descripcion`, `url`, `menu_id`) VALUES (16, 'VER_SOLICITUDES_CC', 'Ver Solicitudes CC', 'ver_solicitudes_cc.html', '#ver-solicitudes-cc');

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`rol_permiso`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (1, 1, 1);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (2, 1, 4);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (3, 1, 10);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (4, 1, 11);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (5, 1, 12);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (6, 1, 13);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (7, 1, 14);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (8, 1, 15);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (9, 2, 1);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (10, 2, 2);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (11, 2, 4);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (12, 2, 6);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (13, 2, 10);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (14, 2, 11);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (15, 2, 12);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (16, 2, 14);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (17, 2, 15);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (18, 3, 1);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (19, 3, 2);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (20, 3, 3);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (21, 3, 4);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (22, 3, 5);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (23, 3, 7);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (24, 3, 8);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (25, 3, 9);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (26, 3, 10);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (27, 3, 12);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (28, 3, 14);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (29, 3, 15);
INSERT INTO `tecnogamerbd`.`rol_permiso` (`id`, `id_rol`, `id_permiso`) VALUES (30, 3, 16);

COMMIT;


-- -----------------------------------------------------
-- Data for table `tecnogamerbd`.`avatar`
-- -----------------------------------------------------
START TRANSACTION;
USE `tecnogamerbd`;
INSERT INTO `tecnogamerbd`.`avatar` (`id`, `id_usuario`, `ruta_imagen`) VALUES (1, 1, 'imagenes/admin/avatar_admin.png');

COMMIT;

