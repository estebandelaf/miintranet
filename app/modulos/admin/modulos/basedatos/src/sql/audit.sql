-- Columnas para auditar tabla {table}
-- @author {author}
-- @date {date}
ALTER TABLE {table} ADD COLUMN audit_programa character varying (20) NOT NULL DEFAULT 'miintranet';
ALTER TABLE {table} ADD COLUMN audit_usuario character varying (20) NOT NULL DEFAULT 'miintranet';
ALTER TABLE {table} ADD COLUMN audit_fechahora timestamp without time zone NOT NULL DEFAULT NOW();
COMMENT ON COLUMN {table}.audit_programa IS 'Programa que realizó la última modificación a la fila';
COMMENT ON COLUMN {table}.audit_usuario IS 'Usuario que realizó la última modificación a la fila';
COMMENT ON COLUMN {table}.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';

