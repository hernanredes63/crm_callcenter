-- CREATE DATABASE IF NOT EXISTS crm_callcenter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE crm_callcenter;
-- =====================================================================
-- Schema MySQL 8 — CRM Leads (migración desde Postgres/Supabase)
-- Charset: utf8mb4 / utf8mb4_unicode_ci
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ================= USERS (reemplaza auth.users de Supabase) ==========
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id            CHAR(36)    NOT NULL PRIMARY KEY,
    email         VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================= CATÁLOGOS =========================================
DROP TABLE IF EXISTS paises;
CREATE TABLE paises (
    id         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre     VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS ciudad;
CREATE TABLE ciudad (
    id       INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre   VARCHAR(100) NOT NULL,
    pais_id  INT NOT NULL,
    CONSTRAINT fk_ciudad_pais FOREIGN KEY (pais_id) REFERENCES paises(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS empresas;
CREATE TABLE empresas (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nombre     VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS proyectos;
CREATE TABLE proyectos (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nombre     VARCHAR(190) NOT NULL,
    empresa_id CHAR(36) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_proy_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS campanas;
CREATE TABLE campanas (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nombre     VARCHAR(190) NOT NULL,
    empresa_id CHAR(36) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_camp_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS canales;
CREATE TABLE canales (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nombre     VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE IF EXISTS mql_status;
CREATE TABLE mql_status (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nivel      VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================= PROFILES (datos de negocio del user) ==============
DROP TABLE IF EXISTS profiles;
CREATE TABLE profiles (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    user_id    CHAR(36) NOT NULL UNIQUE,
    nombre     VARCHAR(190) NOT NULL,
    email      VARCHAR(190),
    activo     TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================= USER_ROLES ========================================
DROP TABLE IF EXISTS user_roles;
CREATE TABLE user_roles (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    user_id    CHAR(36) NOT NULL,
    role       ENUM('admin','supervisor','operador','vendedor','marketing') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_role (user_id, role),
    CONSTRAINT fk_role_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================= AGENTES / VENDEDOR ================================
DROP TABLE IF EXISTS agentes;
CREATE TABLE agentes (
    id         CHAR(36) NOT NULL PRIMARY KEY,
    nombre     VARCHAR(190) NOT NULL,
    profile_id CHAR(36) NULL,
    activo     TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_agente_profile FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS vendedor;
CREATE TABLE vendedor (
    id         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre     VARCHAR(150) NOT NULL,
    profile_id CHAR(36) NULL,
    CONSTRAINT fk_vend_profile FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ================= LEADS =============================================
DROP TABLE IF EXISTS leads;
CREATE TABLE leads (
    id                    CHAR(36) NOT NULL PRIMARY KEY,
    nombre                VARCHAR(190) NOT NULL,
    telefono              VARCHAR(50),
    email                 VARCHAR(190),
    pais                  VARCHAR(150),
    ciudad                VARCHAR(150),
    pais_id               INT NULL,
    empresa_id            CHAR(36) NULL,
    proyecto_id           CHAR(36) NULL,
    campana_id            CHAR(36) NULL,
    canal_id              CHAR(36) NULL,
    mql_id                CHAR(36) NULL,
    temperatura           ENUM('frio','tibio','caliente') NOT NULL DEFAULT 'frio',
    tipo_pago             VARCHAR(50),
    comentario            TEXT,
    operador_id           CHAR(36) NULL,
    vendedor_id           CHAR(36) NULL,
    vendedor_tabla_id     INT NULL,
    agente_id             CHAR(36) NULL,
    derivado_ventas       TINYINT(1) NOT NULL DEFAULT 0,
    fecha_derivado_ventas DATETIME NULL,
    ejecutivo_ventas      VARCHAR(190),
    fecha_lead            DATE NOT NULL DEFAULT (CURRENT_DATE),
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_leads_telefono (telefono),
    KEY idx_leads_operador (operador_id),
    KEY idx_leads_fecha_lead (fecha_lead),
    KEY idx_leads_derivado (derivado_ventas, fecha_derivado_ventas),
    CONSTRAINT fk_lead_empresa   FOREIGN KEY (empresa_id)   REFERENCES empresas(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lead_proyecto  FOREIGN KEY (proyecto_id)  REFERENCES proyectos(id)  ON DELETE SET NULL,
    CONSTRAINT fk_lead_campana   FOREIGN KEY (campana_id)   REFERENCES campanas(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lead_canal     FOREIGN KEY (canal_id)     REFERENCES canales(id)    ON DELETE SET NULL,
    CONSTRAINT fk_lead_mql       FOREIGN KEY (mql_id)       REFERENCES mql_status(id) ON DELETE SET NULL,
    CONSTRAINT fk_lead_operador  FOREIGN KEY (operador_id)  REFERENCES profiles(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lead_vendedor  FOREIGN KEY (vendedor_id)  REFERENCES profiles(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lead_vendtabla FOREIGN KEY (vendedor_tabla_id) REFERENCES vendedor(id) ON DELETE SET NULL,
    CONSTRAINT fk_lead_agente    FOREIGN KEY (agente_id)    REFERENCES agentes(id)    ON DELETE SET NULL,
    CONSTRAINT fk_lead_pais      FOREIGN KEY (pais_id)      REFERENCES paises(id)     ON DELETE SET NULL
) ENGINE=InnoDB;

-- ================= LEADS_REDES ======================================
DROP TABLE IF EXISTS leads_redes;
CREATE TABLE leads_redes (
    id           CHAR(36) NOT NULL PRIMARY KEY,
    nombre       VARCHAR(190) NOT NULL,
    telefono     VARCHAR(50),
    email        VARCHAR(190),
    pais_id      INT NULL,
    ciudad       VARCHAR(150),
    empresa_id   CHAR(36) NULL,
    proyecto_id  CHAR(36) NULL,
    campana_id   CHAR(36) NULL,
    canal_id     CHAR(36) NULL,
    mql_id       CHAR(36) NULL,
    temperatura  ENUM('frio','tibio','caliente') NOT NULL DEFAULT 'frio',
    tipo_pago    VARCHAR(50),
    comentario   TEXT,
    operador_id  CHAR(36) NULL,
    agente_id    CHAR(36) NULL,
    fecha_lead   DATE NOT NULL DEFAULT (CURRENT_DATE),
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_lr_fecha (fecha_lead),
    KEY idx_lr_operador (operador_id),
    CONSTRAINT fk_lr_empresa  FOREIGN KEY (empresa_id)  REFERENCES empresas(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lr_proyecto FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)  ON DELETE SET NULL,
    CONSTRAINT fk_lr_campana  FOREIGN KEY (campana_id)  REFERENCES campanas(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lr_canal    FOREIGN KEY (canal_id)    REFERENCES canales(id)    ON DELETE SET NULL,
    CONSTRAINT fk_lr_mql      FOREIGN KEY (mql_id)      REFERENCES mql_status(id) ON DELETE SET NULL,
    CONSTRAINT fk_lr_operador FOREIGN KEY (operador_id) REFERENCES profiles(id)   ON DELETE SET NULL,
    CONSTRAINT fk_lr_agente   FOREIGN KEY (agente_id)   REFERENCES agentes(id)    ON DELETE SET NULL,
    CONSTRAINT fk_lr_pais     FOREIGN KEY (pais_id)     REFERENCES paises(id)     ON DELETE SET NULL
) ENGINE=InnoDB;

-- ================= SEGUIMIENTOS =====================================
DROP TABLE IF EXISTS seguimientos;
CREATE TABLE seguimientos (
    id                     CHAR(36) NOT NULL PRIMARY KEY,
    lead_id                CHAR(36) NOT NULL,
    iteracion              TINYINT  NOT NULL,
    estado                 ENUM('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') NOT NULL,
    comentario             TEXT,
    fecha_proximo_contacto DATETIME NULL,
    google_event_id        VARCHAR(120) NULL,
    created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_lead_iter (lead_id, iteracion),
    CONSTRAINT chk_iter CHECK (iteracion BETWEEN 1 AND 7),
    CONSTRAINT fk_seg_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS seguimientos_redes;
CREATE TABLE seguimientos_redes (
    id                     CHAR(36) NOT NULL PRIMARY KEY,
    lead_id                CHAR(36) NOT NULL,
    iteracion              TINYINT  NOT NULL,
    estado                 ENUM('contesto','apagado','colgo','ocupado','no_contesta','no_le_interesa','se_envio_mensaje') NOT NULL,
    comentario             TEXT,
    fecha_proximo_contacto DATETIME NULL,
    created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_lr_iter (lead_id, iteracion),
    CONSTRAINT chk_iter_r CHECK (iteracion BETWEEN 1 AND 7),
    CONSTRAINT fk_segr_lead FOREIGN KEY (lead_id) REFERENCES leads_redes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS seguimiento_ventas;
CREATE TABLE seguimiento_ventas (
    id                CHAR(36) NOT NULL PRIMARY KEY,
    lead_id           CHAR(36) NOT NULL,
    vendedor_tabla_id INT NOT NULL,
    estado            ENUM('pendiente','visita','venta','perdido') NOT NULL DEFAULT 'pendiente',
    comentario        TEXT,
    fecha_visita      DATE NULL,
    fecha_venta       DATE NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sv_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    CONSTRAINT fk_sv_vend FOREIGN KEY (vendedor_tabla_id) REFERENCES vendedor(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- TRIGGER: impedir doble derivación de un mismo lead
-- Equivalente a prevent_duplicate_derivation() de Postgres
-- =====================================================================
DELIMITER //
DROP TRIGGER IF EXISTS trg_leads_prevent_dup_derivation //
CREATE TRIGGER trg_leads_prevent_dup_derivation
BEFORE UPDATE ON leads
FOR EACH ROW
BEGIN
    IF NEW.derivado_ventas = 1
       AND OLD.derivado_ventas = 1
       AND OLD.vendedor_tabla_id IS NOT NULL
       AND NEW.vendedor_tabla_id <> OLD.vendedor_tabla_id THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Este lead ya fue derivado a ventas. No se puede derivar nuevamente.';
    END IF;
END//
DELIMITER ;
