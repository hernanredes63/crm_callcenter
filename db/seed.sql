-- Datos iniciales
-- Admin: admin@crm.local / Admin1234!
-- password_hash generado con password_hash('Admin1234!', PASSWORD_BCRYPT)

INSERT INTO users (id, email, password_hash) VALUES
('00000000-0000-0000-0000-000000000001',
 'admin@crm.local',
 '$2y$10$HSMqzaJRitX0GCrxjRYCp.2gZwPlLVE3a1vP7I8nmudPfPPB8cdHW');

INSERT INTO profiles (id, user_id, nombre, email, activo) VALUES
('00000000-0000-0000-0000-000000000A01',
 '00000000-0000-0000-0000-000000000001',
 'Administrador', 'admin@crm.local', 1);

INSERT INTO user_roles (id, user_id, role) VALUES
('00000000-0000-0000-0000-000000000B01',
 '00000000-0000-0000-0000-000000000001', 'admin');

-- Catálogos base
INSERT INTO paises (nombre) VALUES ('Bolivia'),('Argentina'),('Chile'),('Perú'),('Colombia'),('México'),('Ecuador'),('Uruguay'),('Paraguay'),('España');

INSERT INTO ciudad (nombre, pais_id) VALUES
 ('La Paz', 1),('Cochabamba', 1),('Santa Cruz', 1),('Sucre', 1),('Oruro', 1),('Potosí', 1),('Tarija', 1),('Beni', 1),('Pando', 1);

INSERT INTO canales (id, nombre) VALUES
 (UUID(), 'Facebook'), (UUID(), 'Instagram'), (UUID(), 'WhatsApp'),
 (UUID(), 'TikTok'), (UUID(), 'Referido'), (UUID(), 'Web'), (UUID(), 'Google Ads');

INSERT INTO mql_status (id, nivel) VALUES
 (UUID(), 'MQL1'), (UUID(), 'MQL2'), (UUID(), 'MQL3'), (UUID(), 'MQL4');

-- Recordá cambiar la contraseña del admin al primer login.
