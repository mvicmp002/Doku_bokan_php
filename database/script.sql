
CREATE DATABASE IF NOT EXISTS doku_bokan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE doku_bokan;

DROP TABLE IF EXISTS partidas;
DROP TABLE IF EXISTS jugadores;

CREATE TABLE jugadores (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre           VARCHAR(60)  NOT NULL,
  apellido         VARCHAR(60)  NOT NULL,
  email            VARCHAR(120) NOT NULL,
  password         VARCHAR(255) NOT NULL,          -- siempre hasheada, nunca texto plano
  mision_actual    TINYINT UNSIGNED NOT NULL DEFAULT 0,  -- 0 = sin partida en curso, 1-4 = misión donde quedó
  partidas_jugadas INT UNSIGNED NOT NULL DEFAULT 0,
  mejor_tiempo     INT UNSIGNED NULL DEFAULT NULL, -- en segundos; NULL = todavía no terminó el juego
  creado_en        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_jugadores_email (email)
) ENGINE=InnoDB;


CREATE TABLE partidas (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  jugador_id       INT UNSIGNED NOT NULL,
  slot             TINYINT UNSIGNED NOT NULL,              -- espacio de guardado: 1, 2 o 3
  mision_alcanzada TINYINT UNSIGNED NOT NULL DEFAULT 1,    -- 1 a 4 (4 = misión final)
  estado           ENUM('en_curso','completada') NOT NULL DEFAULT 'en_curso',
  tiempo_segundos  INT UNSIGNED NULL DEFAULT NULL,         -- solo se llena al completar
  creado_en        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_partida_slot (jugador_id, slot),           -- un jugador no puede repetir espacio
  CONSTRAINT chk_partida_slot CHECK (slot BETWEEN 1 AND 3),
  CONSTRAINT fk_partidas_jugador
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== DATOS DE PRUEBA =====
-- Los dos usuarios tienen la contraseña: Prueba123
INSERT INTO jugadores (nombre, apellido, email, password, mision_actual, partidas_jugadas, mejor_tiempo) VALUES
('Victoria', 'Parec', 'victoria@prueba.com', '$2b$12$a92ZylqNxu6BLIrPoi57aOS.1CW064FrITvl2XV/tzxgOlNRLxXTq', 0, 2, 540),
('Soma',     'Prueba', 'soma@prueba.com',     '$2b$12$a92ZylqNxu6BLIrPoi57aOS.1CW064FrITvl2XV/tzxgOlNRLxXTq', 3, 1, NULL);

INSERT INTO partidas (jugador_id, slot, mision_alcanzada, estado, tiempo_segundos) VALUES
(1, 1, 4, 'completada', 620),
(1, 2, 4, 'completada', 540),
(2, 1, 3, 'en_curso',   NULL);