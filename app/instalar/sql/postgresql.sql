--
-- PostgreSQL database dump
--

-- Started on 2011-05-15 23:58:55 CLT

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 379 (class 2612 OID 17623)
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- TOC entry 19 (class 1255 OID 17624)
-- Dependencies: 6 379
-- Name: f_edad(date); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION f_edad(v_fecha date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
BEGIN
	RETURN FLOOR(((DATE_PART('YEAR',CURRENT_DATE)-DATE_PART('YEAR',v_fecha))* 372 + (DATE_PART('MONTH',CURRENT_DATE) - DATE_PART('MONTH',v_fecha))*31 + (DATE_PART('DAY',CURRENT_DATE)-DATE_PART('DAY',v_fecha)))/372);
END;
$$;


--
-- TOC entry 20 (class 1255 OID 17625)
-- Dependencies: 379 6
-- Name: f_usuarioautorizado(integer, character varying); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION f_usuarioautorizado(v_usuario_id integer, v_recurso character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
	v_autorizado integer := 0;
	-- variables utilizadas para leer el cursor con los grupos del usuario
	v_grupo_id integer := 0;
	c_grupos CURSOR FOR SELECT grupo_id FROM usuario_grupo WHERE usuario_id = v_usuario_id;
BEGIN
	-- verificar si el usuario es root (id de grupo igual a 1)
	SELECT COUNT(*) INTO v_autorizado FROM usuario_grupo WHERE grupo_id = 1 AND usuario_id = v_usuario_id;
	IF v_autorizado=1 THEN
		RETURN 1;
	END IF;
	-- verificar si el recurso no requiere permisos
	SELECT COUNT(*) INTO v_autorizado FROM permiso_login WHERE recurso = v_recurso;
	IF v_autorizado=1 THEN
		RETURN 1;
	END IF;
	-- si el usuario no es root y el recurso no es libre se debe buscar permiso
	-- obtener listado de grupos del usuario
	OPEN c_grupos;
	LOOP
		FETCH c_grupos INTO v_grupo_id;
		EXIT WHEN NOT FOUND;
		SELECT COUNT(*) INTO v_autorizado FROM permiso WHERE grupo_id = v_grupo_id AND recurso = v_recurso;
		IF v_autorizado=1 THEN
			RETURN 1;
		END IF;
	END LOOP;
	CLOSE c_grupos;
	-- retornar valor por defecto, sin permiso
	RETURN 0;
END;
$$;


--
-- TOC entry 21 (class 1255 OID 17626)
-- Dependencies: 379 6
-- Name: f_usuariosetclave(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION f_usuariosetclave(v_usuario character varying, v_claveantigua character varying, v_clavenueva character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
	v_claveguardada character(32);
BEGIN
	-- verificar que el usuario exista dentro de la base de datos y este activo
	SELECT clave INTO v_claveguardada FROM usuario WHERE usuario = v_usuario AND activo = 1;
	IF v_claveguardada IS NULL THEN
		RETURN 1;
	END IF;
	-- verificar que la clave antigua corresponda con la guardada en la base de datos
	IF v_claveguardada != MD5(v_claveantigua) THEN
		RETURN 2;
	END IF;
	-- guardar clave nueva
	UPDATE usuario SET clave = MD5(v_clavenueva), hash = '' WHERE usuario = v_usuario;
	RETURN 0;
END;
$$;


--
-- TOC entry 22 (class 1255 OID 17627)
-- Dependencies: 6 379
-- Name: sp_cumpleanios(refcursor); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION sp_cumpleanios(c_result refcursor) RETURNS SETOF refcursor
    LANGUAGE plpgsql
    AS $$
DECLARE
	-- variables
	mostrar integer;
	faltan integer;
	encontrados integer;
BEGIN
	-- obtener la cantidad de cumpleaños que se deberan mostrar desde la tabla de parametros
	SELECT valor INTO mostrar FROM parametro WHERE parametro = 'BIRTHDAY_LIMIT';
	-- contar cumpleaños que existen desde la fecha actual hasta fin de año
	SELECT COUNT(*) INTO encontrados FROM usuario WHERE activo = 1 AND to_char(fechanacimiento, 'MMDD') >= to_char(CURRENT_DATE, 'MMDD');
	-- dependiendo de cuantos se hayan encontrado es si se busca solo cumpleaños en el año actual o en el actual y el siguiente
	IF encontrados >= mostrar THEN
		-- se muestran cumpleaños para el año presente
		OPEN c_result FOR
			SELECT nombre || ' ' || apellido AS nombre, to_char(fechanacimiento, 'MM-DD') AS fecha
			FROM usuario
			WHERE activo = 1 AND to_char(fechanacimiento, 'MMDD') >= to_char(CURRENT_DATE, 'MMDD')
			ORDER BY to_char(fechanacimiento, 'MMDD') ASC
			LIMIT mostrar
		;
		RETURN NEXT c_result;
	ELSE
		-- se muestran cumpleaños del año presente mas los que falten para completar "mostrar" con los del siguiente año
		faltan = mostrar - encontrados;
		OPEN c_result FOR SELECT * FROM (
			(
				SELECT nombre || ' ' || apellido AS nombre, to_char(fechanacimiento, 'MM-DD') AS fecha
				FROM usuario
				WHERE activo = 1 AND to_char(fechanacimiento, 'MMDD') >= to_char(CURRENT_DATE, 'MMDD')
				ORDER BY to_char(fechanacimiento, 'MMDD') ASC
				LIMIT mostrar
			) UNION ALL (
				SELECT nombre || ' ' || apellido AS nombre, to_char(fechanacimiento, 'MM-DD') AS fecha
				FROM usuario
				WHERE activo = 1 AND to_char(fechanacimiento, 'MMDD') < to_char(CURRENT_DATE, 'MMDD')
				ORDER BY to_char(fechanacimiento, 'MMDD') ASC
				LIMIT faltan
			)
		) AS t;
		RETURN NEXT c_result;
	END IF;
END;
$$;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1566 (class 1259 OID 17628)
-- Dependencies: 1882 1883 1884 6
-- Name: actividad_economica; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE actividad_economica (
    id integer NOT NULL,
    glosa character varying(100) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2130 (class 0 OID 0)
-- Dependencies: 1566
-- Name: TABLE actividad_economica; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE actividad_economica IS 'Códigos de actividad económica';


--
-- TOC entry 2131 (class 0 OID 0)
-- Dependencies: 1566
-- Name: COLUMN actividad_economica.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN actividad_economica.id IS 'Código de la actividad';


--
-- TOC entry 2132 (class 0 OID 0)
-- Dependencies: 1566
-- Name: COLUMN actividad_economica.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN actividad_economica.glosa IS 'Glosa de la actividad';


--
-- TOC entry 2133 (class 0 OID 0)
-- Dependencies: 1566
-- Name: COLUMN actividad_economica.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN actividad_economica.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2134 (class 0 OID 0)
-- Dependencies: 1566
-- Name: COLUMN actividad_economica.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN actividad_economica.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2135 (class 0 OID 0)
-- Dependencies: 1566
-- Name: COLUMN actividad_economica.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN actividad_economica.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1567 (class 1259 OID 17631)
-- Dependencies: 1885 1886 1887 6
-- Name: afp; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE afp (
    id integer NOT NULL,
    nombre character varying(20) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2136 (class 0 OID 0)
-- Dependencies: 1567
-- Name: TABLE afp; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE afp IS 'Administradoras de fondos de pensiones';


--
-- TOC entry 2137 (class 0 OID 0)
-- Dependencies: 1567
-- Name: COLUMN afp.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN afp.id IS 'ID de la administradora de pensiones (RUT sin puntos ni dv)';


--
-- TOC entry 2138 (class 0 OID 0)
-- Dependencies: 1567
-- Name: COLUMN afp.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN afp.nombre IS 'Nombre de la administradora de pensiones';


--
-- TOC entry 2139 (class 0 OID 0)
-- Dependencies: 1567
-- Name: COLUMN afp.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN afp.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2140 (class 0 OID 0)
-- Dependencies: 1567
-- Name: COLUMN afp.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN afp.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2141 (class 0 OID 0)
-- Dependencies: 1567
-- Name: COLUMN afp.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN afp.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1568 (class 1259 OID 17634)
-- Dependencies: 6
-- Name: area_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE area_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1569 (class 1259 OID 17636)
-- Dependencies: 1888 1889 1890 1891 6
-- Name: area; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE area (
    id integer DEFAULT nextval('area_id_seq'::regclass) NOT NULL,
    glosa character varying(45) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2142 (class 0 OID 0)
-- Dependencies: 1569
-- Name: TABLE area; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE area IS 'Áreas de la empresa';


--
-- TOC entry 2143 (class 0 OID 0)
-- Dependencies: 1569
-- Name: COLUMN area.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN area.id IS 'ID del Área';


--
-- TOC entry 2144 (class 0 OID 0)
-- Dependencies: 1569
-- Name: COLUMN area.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN area.glosa IS 'Nombre del Área';


--
-- TOC entry 2145 (class 0 OID 0)
-- Dependencies: 1569
-- Name: COLUMN area.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN area.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2146 (class 0 OID 0)
-- Dependencies: 1569
-- Name: COLUMN area.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN area.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2147 (class 0 OID 0)
-- Dependencies: 1569
-- Name: COLUMN area.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN area.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1570 (class 1259 OID 17640)
-- Dependencies: 1892 1893 1894 6
-- Name: bodega; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE bodega (
    id character varying(20) NOT NULL,
    glosa character varying(50) NOT NULL,
    sucursal_id character varying(5) NOT NULL,
    usuario_id integer,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2148 (class 0 OID 0)
-- Dependencies: 1570
-- Name: TABLE bodega; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE bodega IS 'Bodegas de la empresa';


--
-- TOC entry 2149 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.id IS 'ID de la bodega';


--
-- TOC entry 2150 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.glosa IS 'Descripción';


--
-- TOC entry 2151 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.sucursal_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.sucursal_id IS 'Sucursal donde la bodega esta ubicada';


--
-- TOC entry 2152 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.usuario_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.usuario_id IS 'Usuario a cargo de la bodega (solo si existe uno)';


--
-- TOC entry 2153 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2154 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2155 (class 0 OID 0)
-- Dependencies: 1570
-- Name: COLUMN bodega.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN bodega.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1571 (class 1259 OID 17643)
-- Dependencies: 6
-- Name: cargo_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE cargo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1572 (class 1259 OID 17645)
-- Dependencies: 1895 1896 1897 1898 1899 6
-- Name: cargo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE cargo (
    id integer DEFAULT nextval('cargo_id_seq'::regclass) NOT NULL,
    glosa character varying(45) NOT NULL,
    area_id integer NOT NULL,
    cardinalidad smallint DEFAULT (0)::smallint NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2156 (class 0 OID 0)
-- Dependencies: 1572
-- Name: TABLE cargo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE cargo IS 'Cargos del personal de la empresa';


--
-- TOC entry 2157 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.id IS 'ID del cargo';


--
-- TOC entry 2158 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.glosa IS 'Nombre/descripción del cargo';


--
-- TOC entry 2159 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.area_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.area_id IS 'Área a la que pertenece el cargo';


--
-- TOC entry 2160 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.cardinalidad; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.cardinalidad IS 'Indica cuantos pueden tener el cargo, =0 infinitos';


--
-- TOC entry 2161 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2162 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2163 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN cargo.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cargo.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1573 (class 1259 OID 17650)
-- Dependencies: 1900 1901 1902 1903 1904 6
-- Name: cliente; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE cliente (
    id integer NOT NULL,
    razonsocial character varying(50) NOT NULL,
    nombrefantasia character varying(30) NOT NULL,
    nacional smallint DEFAULT 1 NOT NULL,
    actividad_economica_id integer NOT NULL,
    direccion character varying(70) NOT NULL,
    comuna_id integer NOT NULL,
    web character varying(30),
    telefono1 character varying(20) NOT NULL,
    telefono2 character varying(20),
    contacto character varying(30),
    email character varying(60),
    replegal character varying(30),
    reprut integer,
    activo smallint DEFAULT 1 NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2164 (class 0 OID 0)
-- Dependencies: 1573
-- Name: TABLE cliente; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE cliente IS 'Clientes de la empresa';


--
-- TOC entry 2165 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.id IS 'ID del cliente (RUT sin puntos ni dv)';


--
-- TOC entry 2166 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.razonsocial; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.razonsocial IS 'Razón social del cliente';


--
-- TOC entry 2167 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.nombrefantasia; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.nombrefantasia IS 'Nombre de fantasía del cliente';


--
-- TOC entry 2168 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.nacional; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.nacional IS 'Indica si es un cliente nacional (1) o extranjero (0)';


--
-- TOC entry 2169 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.actividad_economica_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.actividad_economica_id IS 'Código de actividad económica';


--
-- TOC entry 2170 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.direccion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.direccion IS 'Dirección principal utilizada';


--
-- TOC entry 2171 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.comuna_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.comuna_id IS 'Comuna';


--
-- TOC entry 2172 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.web; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.web IS 'Sitio web (incluyendo http://)';


--
-- TOC entry 2173 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.telefono1; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.telefono1 IS 'Teléfono principal';


--
-- TOC entry 2174 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.telefono2; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.telefono2 IS 'Teléfono secundario';


--
-- TOC entry 2175 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.contacto; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.contacto IS 'Nombre del contacto dentro de la empresa';


--
-- TOC entry 2176 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.email IS 'Correo del contacto';


--
-- TOC entry 2177 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.replegal; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.replegal IS 'Representante legal';


--
-- TOC entry 2178 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.reprut; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.reprut IS 'Rut del representante legal (sin puntos ni dv)';


--
-- TOC entry 2179 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.activo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.activo IS 'Indica si el cliente está activo (1) o no (0)';


--
-- TOC entry 2180 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2181 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2182 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN cliente.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN cliente.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1574 (class 1259 OID 17655)
-- Dependencies: 1905 1906 1907 6
-- Name: comuna; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE comuna (
    id integer NOT NULL,
    nombre character varying(45) NOT NULL,
    region_id smallint NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2183 (class 0 OID 0)
-- Dependencies: 1574
-- Name: TABLE comuna; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE comuna IS 'Comunas del paí­s';


--
-- TOC entry 2184 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.id IS 'ID de la comuna';


--
-- TOC entry 2185 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.nombre IS 'Nombre de la comuna';


--
-- TOC entry 2186 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.region_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.region_id IS 'Región de la comuna';


--
-- TOC entry 2187 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2188 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2189 (class 0 OID 0)
-- Dependencies: 1574
-- Name: COLUMN comuna.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN comuna.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1575 (class 1259 OID 17658)
-- Dependencies: 1908 1909 1910 6
-- Name: enlace; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE enlace (
    url character varying(200) NOT NULL,
    nombre character varying(60) NOT NULL,
    enlace_categoria_id integer NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2190 (class 0 OID 0)
-- Dependencies: 1575
-- Name: TABLE enlace; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE enlace IS 'Enlaces generales de la aplicación';


--
-- TOC entry 2191 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.url; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.url IS 'Dirección url completa';


--
-- TOC entry 2192 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.nombre IS 'Nombre o descripción del enlace';


--
-- TOC entry 2193 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.enlace_categoria_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.enlace_categoria_id IS 'Categorí­a del enlace';


--
-- TOC entry 2194 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2195 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2196 (class 0 OID 0)
-- Dependencies: 1575
-- Name: COLUMN enlace.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1576 (class 1259 OID 17661)
-- Dependencies: 6
-- Name: enlace_categoria_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE enlace_categoria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1577 (class 1259 OID 17663)
-- Dependencies: 1911 1912 1913 1914 1915 6
-- Name: enlace_categoria; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE enlace_categoria (
    id integer DEFAULT nextval('enlace_categoria_id_seq'::regclass) NOT NULL,
    nombre character varying(40) NOT NULL,
    orden smallint DEFAULT (99)::smallint NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2197 (class 0 OID 0)
-- Dependencies: 1577
-- Name: TABLE enlace_categoria; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE enlace_categoria IS 'Categorí­as de los enlaces generales de la aplicación';


--
-- TOC entry 2198 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.id IS 'ID de la categorí­a';


--
-- TOC entry 2199 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.nombre IS 'Nombre de la categorí­a';


--
-- TOC entry 2200 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.orden; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.orden IS 'Order en que serán mostradas las categorías';


--
-- TOC entry 2201 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2202 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2203 (class 0 OID 0)
-- Dependencies: 1577
-- Name: COLUMN enlace_categoria.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_categoria.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1578 (class 1259 OID 17668)
-- Dependencies: 1916 1917 1918 6
-- Name: enlace_usuario; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE enlace_usuario (
    usuario_id integer NOT NULL,
    url character varying(200) NOT NULL,
    nombre character varying(60) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2204 (class 0 OID 0)
-- Dependencies: 1578
-- Name: TABLE enlace_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE enlace_usuario IS 'Enlaces personales de cada usuario';


--
-- TOC entry 2205 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.usuario_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.usuario_id IS 'ID del usuario que creó el enlace';


--
-- TOC entry 2206 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.url; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.url IS 'Dirección url completa';


--
-- TOC entry 2207 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.nombre IS 'Nombre o descripción del enlace';


--
-- TOC entry 2208 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2209 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2210 (class 0 OID 0)
-- Dependencies: 1578
-- Name: COLUMN enlace_usuario.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN enlace_usuario.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1604 (class 1259 OID 18586)
-- Dependencies: 2010 2011 2012 6
-- Name: feriado; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE feriado (
    anio smallint NOT NULL,
    mes smallint NOT NULL,
    dia smallint NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2211 (class 0 OID 0)
-- Dependencies: 1604
-- Name: TABLE feriado; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE feriado IS 'Días feriados';


--
-- TOC entry 2212 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.anio; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.anio IS 'Año en caso de feriados que varien con los años, =0 en caso de otros (como 1 de ene o 25 de dic)';


--
-- TOC entry 2213 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.mes; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.mes IS 'Mes del feriado';


--
-- TOC entry 2214 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.dia; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.dia IS 'Día feriado';


--
-- TOC entry 2215 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2216 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2217 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN feriado.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN feriado.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1602 (class 1259 OID 18523)
-- Dependencies: 2000 2001 2002 2003 2004 2005 6
-- Name: geoposicionamiento; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE geoposicionamiento (
    id character(32) NOT NULL,
    longitud numeric(10,8) DEFAULT 0 NOT NULL,
    latitud numeric(10,8) DEFAULT 0 NOT NULL,
    fechahora timestamp without time zone DEFAULT now() NOT NULL,
    glosa character varying(100) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2218 (class 0 OID 0)
-- Dependencies: 1602
-- Name: TABLE geoposicionamiento; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE geoposicionamiento IS 'Ubicación actual geográfica para diferentes fines';


--
-- TOC entry 2219 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.id IS 'Identificador, recomendado hash MD5';


--
-- TOC entry 2220 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.longitud; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.longitud IS 'Longitud geográfica';


--
-- TOC entry 2221 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.latitud; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.latitud IS 'Latitud geográfica';


--
-- TOC entry 2222 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.fechahora IS 'Fecha y hora de la última actualización';


--
-- TOC entry 2223 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.glosa IS 'Descripción de a quién/que se le hace el geoposicionamiento';


--
-- TOC entry 2224 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2225 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2226 (class 0 OID 0)
-- Dependencies: 1602
-- Name: COLUMN geoposicionamiento.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN geoposicionamiento.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1579 (class 1259 OID 17671)
-- Dependencies: 6
-- Name: grupo_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE grupo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1580 (class 1259 OID 17673)
-- Dependencies: 1919 1920 1921 1922 6
-- Name: grupo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE grupo (
    id integer DEFAULT nextval('grupo_id_seq'::regclass) NOT NULL,
    glosa character varying(45) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2227 (class 0 OID 0)
-- Dependencies: 1580
-- Name: TABLE grupo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE grupo IS 'Perfiles del sistema para agrupar a los usuarios';


--
-- TOC entry 2228 (class 0 OID 0)
-- Dependencies: 1580
-- Name: COLUMN grupo.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN grupo.id IS 'ID del grupo';


--
-- TOC entry 2229 (class 0 OID 0)
-- Dependencies: 1580
-- Name: COLUMN grupo.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN grupo.glosa IS 'Nombre del grupo';


--
-- TOC entry 2230 (class 0 OID 0)
-- Dependencies: 1580
-- Name: COLUMN grupo.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN grupo.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2231 (class 0 OID 0)
-- Dependencies: 1580
-- Name: COLUMN grupo.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN grupo.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2232 (class 0 OID 0)
-- Dependencies: 1580
-- Name: COLUMN grupo.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN grupo.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1581 (class 1259 OID 17677)
-- Dependencies: 1923 1924 1925 6
-- Name: modulo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modulo (
    nombre character varying(15) NOT NULL,
    glosa character varying(70) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2233 (class 0 OID 0)
-- Dependencies: 1581
-- Name: TABLE modulo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE modulo IS 'Módulos del sistema';


--
-- TOC entry 2234 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN modulo.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN modulo.nombre IS 'Nombre del módulo';


--
-- TOC entry 2235 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN modulo.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN modulo.glosa IS 'Descripción del módulo';


--
-- TOC entry 2236 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN modulo.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN modulo.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2237 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN modulo.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN modulo.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2238 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN modulo.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN modulo.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1582 (class 1259 OID 17680)
-- Dependencies: 6
-- Name: noticia_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE noticia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1583 (class 1259 OID 17682)
-- Dependencies: 1926 1927 1928 1929 6
-- Name: noticia; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE noticia (
    id integer DEFAULT nextval('noticia_id_seq'::regclass) NOT NULL,
    titulo character varying(30) NOT NULL,
    cuerpo text NOT NULL,
    fechahora timestamp without time zone NOT NULL,
    expiracion date NOT NULL,
    usuario_id integer NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL,
    resumen character varying(80) NOT NULL
);


--
-- TOC entry 2239 (class 0 OID 0)
-- Dependencies: 1583
-- Name: TABLE noticia; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE noticia IS 'Noticias para ser publicada en la portada de la app y en rss';


--
-- TOC entry 2240 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.id IS 'ID de la noticia';


--
-- TOC entry 2241 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.titulo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.titulo IS 'Tí­tulo de la noticia';


--
-- TOC entry 2242 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.cuerpo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.cuerpo IS 'Texto de la noticia';


--
-- TOC entry 2243 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.fechahora IS 'Fecha y hora en la que se creo la noticia';


--
-- TOC entry 2244 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.expiracion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.expiracion IS 'Fecha hasta cuando la noticia es válida';


--
-- TOC entry 2245 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.usuario_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.usuario_id IS 'ID del usuario que publica la noticia';


--
-- TOC entry 2246 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2247 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2248 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 2249 (class 0 OID 0)
-- Dependencies: 1583
-- Name: COLUMN noticia.resumen; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN noticia.resumen IS 'Resumen de la noticia para ser mostrado en los links hacia esta';


--
-- TOC entry 1584 (class 1259 OID 17689)
-- Dependencies: 1930 1931 1932 6
-- Name: parametro; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE parametro (
    parametro character varying(30) NOT NULL,
    valor character varying(60) NOT NULL,
    descripcion character varying(100) NOT NULL,
    modulo_nombre character varying(15) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2250 (class 0 OID 0)
-- Dependencies: 1584
-- Name: TABLE parametro; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE parametro IS 'Parámetros de la aplicación';


--
-- TOC entry 2251 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.parametro; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.parametro IS 'Nombre del parámetro en mayúsculas, sin espacios ni caracteres especiales';


--
-- TOC entry 2252 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.valor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.valor IS 'Valor del parámetro (int, string, etc)';


--
-- TOC entry 2253 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.descripcion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.descripcion IS 'Descripción del parámetro';


--
-- TOC entry 2254 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.modulo_nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.modulo_nombre IS 'Módulo del sistema al que pertenece el parámetro';


--
-- TOC entry 2255 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2256 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2257 (class 0 OID 0)
-- Dependencies: 1584
-- Name: COLUMN parametro.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN parametro.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1585 (class 1259 OID 17692)
-- Dependencies: 1933 1934 1935 6
-- Name: permiso; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE permiso (
    grupo_id smallint NOT NULL,
    recurso character varying(100) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2258 (class 0 OID 0)
-- Dependencies: 1585
-- Name: TABLE permiso; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE permiso IS 'Relación entre grupos y recursos';


--
-- TOC entry 2259 (class 0 OID 0)
-- Dependencies: 1585
-- Name: COLUMN permiso.grupo_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso.grupo_id IS 'ID del grupo que tendrá acceso al recurso';


--
-- TOC entry 2260 (class 0 OID 0)
-- Dependencies: 1585
-- Name: COLUMN permiso.recurso; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso.recurso IS 'Generalmente una url, pero puede ser otro tipo de recurso como smb://servidor para utilizar con SAMBA y PAM';


--
-- TOC entry 2261 (class 0 OID 0)
-- Dependencies: 1585
-- Name: COLUMN permiso.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2262 (class 0 OID 0)
-- Dependencies: 1585
-- Name: COLUMN permiso.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2263 (class 0 OID 0)
-- Dependencies: 1585
-- Name: COLUMN permiso.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1586 (class 1259 OID 17695)
-- Dependencies: 1936 1937 1938 6
-- Name: permiso_login; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE permiso_login (
    recurso character varying(100) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2264 (class 0 OID 0)
-- Dependencies: 1586
-- Name: TABLE permiso_login; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE permiso_login IS 'Recursos que solo requieren al usuario logueado';


--
-- TOC entry 2265 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN permiso_login.recurso; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso_login.recurso IS 'Generalmente una url, pero puede ser otro tipo de recurso como smb://servidor para utilizar con SAMBA y PAM';


--
-- TOC entry 2266 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN permiso_login.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso_login.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2267 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN permiso_login.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso_login.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2268 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN permiso_login.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN permiso_login.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1587 (class 1259 OID 17698)
-- Dependencies: 1939 1940 1941 1942 6
-- Name: producto; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE producto (
    id character varying(20) NOT NULL,
    nombre character varying(30) NOT NULL,
    producto_categoria_id integer NOT NULL,
    unidad_id integer NOT NULL,
    valor integer DEFAULT 0 NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2269 (class 0 OID 0)
-- Dependencies: 1587
-- Name: TABLE producto; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE producto IS 'Productos, ya sean materias primas, productos finales o insumos de la empresa';


--
-- TOC entry 2270 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.id IS 'Identificador';


--
-- TOC entry 2271 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.nombre IS 'Nombre';


--
-- TOC entry 2272 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.producto_categoria_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.producto_categoria_id IS 'Categoría final del producto';


--
-- TOC entry 2273 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.unidad_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.unidad_id IS 'Tipo de unidad a utilizar por el producto';


--
-- TOC entry 2274 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.valor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.valor IS 'Valor de venta por unidad del producto';


--
-- TOC entry 2275 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2276 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2277 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN producto.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1588 (class 1259 OID 17702)
-- Dependencies: 1944 1945 1946 6
-- Name: producto_categoria; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE producto_categoria (
    id integer NOT NULL,
    glosa character varying(40) NOT NULL,
    producto_categoria_id integer,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2278 (class 0 OID 0)
-- Dependencies: 1588
-- Name: TABLE producto_categoria; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE producto_categoria IS 'Categorías y sub categorías para clasificar productos';


--
-- TOC entry 2279 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.id IS 'Identificador único';


--
-- TOC entry 2280 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.glosa IS 'Glosa';


--
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.producto_categoria_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.producto_categoria_id IS 'Categoría de producto padre (si es el nivel más alto no especificar)';


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 1588
-- Name: COLUMN producto_categoria.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_categoria.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1589 (class 1259 OID 17705)
-- Dependencies: 6 1588
-- Name: producto_categoria_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE producto_categoria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2285 (class 0 OID 0)
-- Dependencies: 1589
-- Name: producto_categoria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE producto_categoria_id_seq OWNED BY producto_categoria.id;


--
-- TOC entry 1590 (class 1259 OID 17707)
-- Dependencies: 1947 1948 1949 6
-- Name: producto_proveedor; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE producto_proveedor (
    producto_id character varying(20) NOT NULL,
    proveedor_id integer NOT NULL,
    valor integer NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2286 (class 0 OID 0)
-- Dependencies: 1590
-- Name: TABLE producto_proveedor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE producto_proveedor IS 'Relación entre productos y los proveedores que los ofrecen';


--
-- TOC entry 2287 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.producto_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.producto_id IS 'Código del producto';


--
-- TOC entry 2288 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.proveedor_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.proveedor_id IS 'Rut del proveedor';


--
-- TOC entry 2289 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.valor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.valor IS 'Valor de compra por unidad definida en el producto';


--
-- TOC entry 2290 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2291 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2292 (class 0 OID 0)
-- Dependencies: 1590
-- Name: COLUMN producto_proveedor.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN producto_proveedor.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1591 (class 1259 OID 17710)
-- Dependencies: 1950 1951 1952 1953 1954 6
-- Name: proveedor; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE proveedor (
    id integer NOT NULL,
    razonsocial character varying(50) NOT NULL,
    nombrefantasia character varying(30) NOT NULL,
    nacional smallint DEFAULT 1 NOT NULL,
    actividad_economica_id integer NOT NULL,
    direccion character varying(70) NOT NULL,
    comuna_id integer NOT NULL,
    web character varying(30),
    telefono1 character varying(20) NOT NULL,
    telefono2 character varying(20),
    contacto character varying(30),
    email character varying(60),
    replegal character varying(30),
    reprut integer,
    activo smallint DEFAULT 1 NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2293 (class 0 OID 0)
-- Dependencies: 1591
-- Name: TABLE proveedor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE proveedor IS 'Proveedores de la empresa';


--
-- TOC entry 2294 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.id IS 'ID del proveedor (RUT sin puntos ni dv)';


--
-- TOC entry 2295 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.razonsocial; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.razonsocial IS 'Razón social del proveedor';


--
-- TOC entry 2296 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.nombrefantasia; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.nombrefantasia IS 'Nombre de fantasía del proveedor';


--
-- TOC entry 2297 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.nacional; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.nacional IS 'Indica si es un proveedor nacional (1) o extranjero (0)';


--
-- TOC entry 2298 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.actividad_economica_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.actividad_economica_id IS 'Código de actividad económica';


--
-- TOC entry 2299 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.direccion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.direccion IS 'Dirección principal utilizada';


--
-- TOC entry 2300 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.comuna_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.comuna_id IS 'Comuna';


--
-- TOC entry 2301 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.web; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.web IS 'Sitio web (incluyendo http://)';


--
-- TOC entry 2302 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.telefono1; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.telefono1 IS 'Teléfono principal';


--
-- TOC entry 2303 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.telefono2; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.telefono2 IS 'Teléfono secundario';


--
-- TOC entry 2304 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.contacto; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.contacto IS 'Nombre del contacto dentro de la empresa';


--
-- TOC entry 2305 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.email IS 'Correo del contacto';


--
-- TOC entry 2306 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.replegal; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.replegal IS 'Representante legal';


--
-- TOC entry 2307 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.reprut; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.reprut IS 'Rut del representante legal (sin puntos ni dv)';


--
-- TOC entry 2308 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.activo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.activo IS 'Indica si el proveedor está activo (1) o no (0)';


--
-- TOC entry 2309 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2310 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2311 (class 0 OID 0)
-- Dependencies: 1591
-- Name: COLUMN proveedor.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN proveedor.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1592 (class 1259 OID 17715)
-- Dependencies: 1955 1956 1957 6
-- Name: region; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE region (
    id smallint NOT NULL,
    nombre character varying(70) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2312 (class 0 OID 0)
-- Dependencies: 1592
-- Name: TABLE region; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE region IS 'Regiones del paí­s';


--
-- TOC entry 2313 (class 0 OID 0)
-- Dependencies: 1592
-- Name: COLUMN region.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN region.id IS 'ID de la región';


--
-- TOC entry 2314 (class 0 OID 0)
-- Dependencies: 1592
-- Name: COLUMN region.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN region.nombre IS 'Nombre de la región';


--
-- TOC entry 2315 (class 0 OID 0)
-- Dependencies: 1592
-- Name: COLUMN region.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN region.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2316 (class 0 OID 0)
-- Dependencies: 1592
-- Name: COLUMN region.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN region.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2317 (class 0 OID 0)
-- Dependencies: 1592
-- Name: COLUMN region.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN region.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1593 (class 1259 OID 17718)
-- Dependencies: 1958 1959 1960 6
-- Name: salud; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE salud (
    id integer NOT NULL,
    nombre character varying(30) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2318 (class 0 OID 0)
-- Dependencies: 1593
-- Name: TABLE salud; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE salud IS 'Instituciones de salud: FONASA o Isapres';


--
-- TOC entry 2319 (class 0 OID 0)
-- Dependencies: 1593
-- Name: COLUMN salud.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN salud.id IS 'ID de la institución de salud (RUT sin puntos ni dv)';


--
-- TOC entry 2320 (class 0 OID 0)
-- Dependencies: 1593
-- Name: COLUMN salud.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN salud.nombre IS 'Nombre de la institución de salud';


--
-- TOC entry 2321 (class 0 OID 0)
-- Dependencies: 1593
-- Name: COLUMN salud.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN salud.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2322 (class 0 OID 0)
-- Dependencies: 1593
-- Name: COLUMN salud.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN salud.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2323 (class 0 OID 0)
-- Dependencies: 1593
-- Name: COLUMN salud.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN salud.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1594 (class 1259 OID 17721)
-- Dependencies: 1961 1962 1963 6
-- Name: stock; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE stock (
    producto_id character varying(20) NOT NULL,
    bodega_id character varying(20) NOT NULL,
    area_id integer NOT NULL,
    nivel integer NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2324 (class 0 OID 0)
-- Dependencies: 1594
-- Name: TABLE stock; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE stock IS 'Niveles de stock actuales';


--
-- TOC entry 2325 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.producto_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.producto_id IS 'Producto';


--
-- TOC entry 2326 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.bodega_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.bodega_id IS 'Bodega';


--
-- TOC entry 2327 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.area_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.area_id IS 'Área';


--
-- TOC entry 2328 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.nivel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.nivel IS 'Nivel de stock por unidad de producto';


--
-- TOC entry 2329 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2330 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2331 (class 0 OID 0)
-- Dependencies: 1594
-- Name: COLUMN stock.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1595 (class 1259 OID 17724)
-- Dependencies: 1964 1965 1966 6
-- Name: stock_nivel; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE stock_nivel (
    producto_id character varying(20) NOT NULL,
    bodega_id character varying(20) NOT NULL,
    area_id integer NOT NULL,
    critico integer,
    bajo integer NOT NULL,
    medio integer,
    normal integer,
    alto integer,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2332 (class 0 OID 0)
-- Dependencies: 1595
-- Name: TABLE stock_nivel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE stock_nivel IS 'Niveles de stock requeridos';


--
-- TOC entry 2333 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.producto_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.producto_id IS 'Producto';


--
-- TOC entry 2334 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.bodega_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.bodega_id IS 'Bodega';


--
-- TOC entry 2335 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.area_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.area_id IS 'Área';


--
-- TOC entry 2336 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.critico; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.critico IS 'Nivel por unidad de producto para estado crítico';


--
-- TOC entry 2337 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.bajo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.bajo IS 'Nivel por unidad de producto para estado bajo';


--
-- TOC entry 2338 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.medio; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.medio IS 'Nivel por unidad de producto para estado medio';


--
-- TOC entry 2339 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.normal; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.normal IS 'Nivel por unidad de producto para estado normal';


--
-- TOC entry 2340 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.alto; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.alto IS 'Nivel por unidad de producto para estado alto';


--
-- TOC entry 2341 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2342 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2343 (class 0 OID 0)
-- Dependencies: 1595
-- Name: COLUMN stock_nivel.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN stock_nivel.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1596 (class 1259 OID 17727)
-- Dependencies: 1967 1968 1969 1970 1971 6
-- Name: sucursal; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sucursal (
    id character varying(5) NOT NULL,
    glosa character varying(45) NOT NULL,
    matriz smallint DEFAULT (0)::smallint NOT NULL,
    direccion character varying(100) NOT NULL,
    comuna_id integer NOT NULL,
    email character varying(50) NOT NULL,
    telefono character varying(25) NOT NULL,
    fax character varying(25) DEFAULT NULL::character varying,
    usuario_id integer,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2344 (class 0 OID 0)
-- Dependencies: 1596
-- Name: TABLE sucursal; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE sucursal IS 'Sucursales y casa matriz de la empresa';


--
-- TOC entry 2345 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.id IS 'ID de la sucursal';


--
-- TOC entry 2346 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.glosa IS 'Nombre de la sucursal';


--
-- TOC entry 2347 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.matriz; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.matriz IS 'Indica si la sucursal es la casa matriz, 1 lo es, con 0 no';


--
-- TOC entry 2348 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.direccion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.direccion IS 'Dirección de la sucursal';


--
-- TOC entry 2349 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.comuna_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.comuna_id IS 'Comuna de la sucursal';


--
-- TOC entry 2350 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.email IS 'Correo electrónico de la sucursal';


--
-- TOC entry 2351 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.telefono; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.telefono IS 'Teléfono de la sucursal';


--
-- TOC entry 2352 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.fax; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.fax IS 'Fax de la sucursal';


--
-- TOC entry 2353 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.usuario_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.usuario_id IS 'Usuario a cargo de la sucursal';


--
-- TOC entry 2354 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2355 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2356 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN sucursal.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN sucursal.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1603 (class 1259 OID 18565)
-- Dependencies: 2006 2007 2008 2009 6
-- Name: transportista; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE transportista (
    id integer NOT NULL,
    razonsocial character varying(50) NOT NULL,
    interno smallint DEFAULT 1 NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2357 (class 0 OID 0)
-- Dependencies: 1603
-- Name: TABLE transportista; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE transportista IS 'Transportistas externos o internos que se utilizan para el movimiento de productos';


--
-- TOC entry 2358 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.id IS 'ID del transportista (RUT sin puntos ni dv)';


--
-- TOC entry 2359 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.razonsocial; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.razonsocial IS 'Razón social del transportista';


--
-- TOC entry 2360 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.interno; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.interno IS 'Indica si es un transportista de la empresa (=1) o externo (=0)';


--
-- TOC entry 2361 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2362 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2363 (class 0 OID 0)
-- Dependencies: 1603
-- Name: COLUMN transportista.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN transportista.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1597 (class 1259 OID 17732)
-- Dependencies: 1972 1973 1974 6
-- Name: uf; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE uf (
    fecha date NOT NULL,
    valor double precision NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2364 (class 0 OID 0)
-- Dependencies: 1597
-- Name: TABLE uf; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE uf IS 'Valores diarios de la UF';


--
-- TOC entry 2365 (class 0 OID 0)
-- Dependencies: 1597
-- Name: COLUMN uf.fecha; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN uf.fecha IS 'Día del valor';


--
-- TOC entry 2366 (class 0 OID 0)
-- Dependencies: 1597
-- Name: COLUMN uf.valor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN uf.valor IS 'Valor de la UF';


--
-- TOC entry 2367 (class 0 OID 0)
-- Dependencies: 1597
-- Name: COLUMN uf.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN uf.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2368 (class 0 OID 0)
-- Dependencies: 1597
-- Name: COLUMN uf.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN uf.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2369 (class 0 OID 0)
-- Dependencies: 1597
-- Name: COLUMN uf.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN uf.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1598 (class 1259 OID 17735)
-- Dependencies: 1976 1977 1978 6
-- Name: unidad; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE unidad (
    id integer NOT NULL,
    unidad character varying(10) NOT NULL,
    glosa character varying(30) NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2370 (class 0 OID 0)
-- Dependencies: 1598
-- Name: TABLE unidad; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE unidad IS 'Unidades de medida para productos';


--
-- TOC entry 2371 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.id IS 'Identificador';


--
-- TOC entry 2372 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.unidad; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.unidad IS 'Unidad (ej: unidad, kg, m, etc)';


--
-- TOC entry 2373 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.glosa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.glosa IS 'Glosa (ej: unidad, kilogramo, metro, etc)';


--
-- TOC entry 2374 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2375 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2376 (class 0 OID 0)
-- Dependencies: 1598
-- Name: COLUMN unidad.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN unidad.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1599 (class 1259 OID 17738)
-- Dependencies: 6 1598
-- Name: unidad_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE unidad_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2377 (class 0 OID 0)
-- Dependencies: 1599
-- Name: unidad_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE unidad_id_seq OWNED BY unidad.id;


--
-- TOC entry 1600 (class 1259 OID 17740)
-- Dependencies: 1979 1980 1981 1982 1983 1984 1985 1986 1987 1988 1989 1990 1991 1992 1993 1994 1995 1996 6
-- Name: usuario; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE usuario (
    id integer NOT NULL,
    clave character(32) DEFAULT NULL::bpchar,
    hash character(32) DEFAULT NULL::bpchar,
    ultimoacceso timestamp without time zone,
    ultimapagina character varying(250) DEFAULT NULL::character varying,
    nombre character varying(20) NOT NULL,
    apellido character varying(30) NOT NULL,
    fechanacimiento date NOT NULL,
    lang character(2) DEFAULT 'es'::bpchar NOT NULL,
    usuario character varying(20) DEFAULT NULL::character varying,
    activo smallint DEFAULT (0)::smallint NOT NULL,
    avatardata bytea,
    avatarname character varying(50) DEFAULT NULL::character varying,
    avatartype character varying(10) DEFAULT NULL::character varying,
    avatarsize integer,
    sucursal_id character varying(5) NOT NULL,
    cargo_id integer NOT NULL,
    ingreso date NOT NULL,
    contratoinicio date,
    contratofin date,
    cvdata bytea,
    cvname character varying(50) DEFAULT NULL::character varying,
    cvtype character varying(20) DEFAULT NULL::character varying,
    cvsize integer,
    email character varying(60) DEFAULT NULL::character varying,
    telefono1 character varying(20) DEFAULT NULL::character varying,
    telefono2 character varying(20) DEFAULT NULL::character varying,
    filasporpagina integer DEFAULT 20,
    remuneracion integer DEFAULT 0 NOT NULL,
    salud_id integer,
    afp_id integer,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2378 (class 0 OID 0)
-- Dependencies: 1600
-- Name: TABLE usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE usuario IS 'Usuarios del sistema y personal de la empresa';


--
-- TOC entry 2379 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.id IS 'ID del usuario, utilizar RUN sin DV ni puntos ni guión';


--
-- TOC entry 2380 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.clave; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.clave IS 'Clave encriptada usando MD5';


--
-- TOC entry 2381 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.hash; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.hash IS 'Hash generado usando MD5';


--
-- TOC entry 2382 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.ultimoacceso; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.ultimoacceso IS 'Fecha y hora del último recurso utilizado';


--
-- TOC entry 2383 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.ultimapagina; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.ultimapagina IS 'íšltimo recurso utilizado';


--
-- TOC entry 2384 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.nombre; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.nombre IS 'Nombres';


--
-- TOC entry 2385 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.apellido; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.apellido IS 'Apellidos del usuario (paterno y materno)';


--
-- TOC entry 2386 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.fechanacimiento; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.fechanacimiento IS 'Fecha de nacimiento';


--
-- TOC entry 2387 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.lang; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.lang IS 'Lenguaje en que deberá ser mostrado el sistema';


--
-- TOC entry 2388 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.usuario IS 'Nombre de usuario';


--
-- TOC entry 2389 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.activo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.activo IS 'Indica si el usuario se encuentra activo en el sistema';


--
-- TOC entry 2390 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.avatardata; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.avatardata IS 'Datos para el avatar/fotografí­a';


--
-- TOC entry 2391 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.avatarname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.avatarname IS 'Nombre del avatar';


--
-- TOC entry 2392 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.avatartype; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.avatartype IS 'Mimetype de la imágen';


--
-- TOC entry 2393 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.avatarsize; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.avatarsize IS 'Tamaño de la imágen';


--
-- TOC entry 2394 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.sucursal_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.sucursal_id IS 'ID de la sucursal a la que pertenece el usuario';


--
-- TOC entry 2395 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.cargo_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.cargo_id IS 'ID del cargo que posee el usuario';


--
-- TOC entry 2396 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.ingreso; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.ingreso IS 'Fecha de ingreso a la empresa';


--
-- TOC entry 2397 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.contratoinicio; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.contratoinicio IS 'Fecha en que se inicio su contrato';


--
-- TOC entry 2398 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.contratofin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.contratofin IS 'Fecha en que se puso fin a su contrato';


--
-- TOC entry 2399 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.cvdata; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.cvdata IS 'Datos para el curriculum';


--
-- TOC entry 2400 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.cvname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.cvname IS 'Nombre del curriculum';


--
-- TOC entry 2401 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.cvtype; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.cvtype IS 'Mimetype del curriculum';


--
-- TOC entry 2402 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.cvsize; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.cvsize IS 'Tamaño del curriculum';


--
-- TOC entry 2403 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.email IS 'Correo electrónico';


--
-- TOC entry 2404 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.telefono1; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.telefono1 IS 'Teléfono primario';


--
-- TOC entry 2405 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.telefono2; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.telefono2 IS 'Teléfono alternativo';


--
-- TOC entry 2406 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.filasporpagina; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.filasporpagina IS 'Filas por página que el usuario verá en las búsquedas';


--
-- TOC entry 2407 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.remuneracion; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.remuneracion IS 'Remuneración mensual bruta, 0 en caso de trabajo a honorarios';


--
-- TOC entry 2408 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.salud_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.salud_id IS 'ID de la institución de salud a la que el usuario esta afiliado';


--
-- TOC entry 2409 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.afp_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.afp_id IS 'ID de la AFP a la que el usuario esta afiliado';


--
-- TOC entry 2410 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2411 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2412 (class 0 OID 0)
-- Dependencies: 1600
-- Name: COLUMN usuario.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1601 (class 1259 OID 17761)
-- Dependencies: 1997 1998 1999 6
-- Name: usuario_grupo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE usuario_grupo (
    usuario_id integer NOT NULL,
    grupo_id smallint NOT NULL,
    audit_programa character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_usuario character varying(20) DEFAULT 'miintranet'::character varying NOT NULL,
    audit_fechahora timestamp without time zone DEFAULT now() NOT NULL
);


--
-- TOC entry 2413 (class 0 OID 0)
-- Dependencies: 1601
-- Name: TABLE usuario_grupo; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE usuario_grupo IS 'Relación entre usuarios y los grupos a los que pertenecen';


--
-- TOC entry 2414 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN usuario_grupo.usuario_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario_grupo.usuario_id IS 'ID del usuario';


--
-- TOC entry 2415 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN usuario_grupo.grupo_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario_grupo.grupo_id IS 'ID del grupo';


--
-- TOC entry 2416 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN usuario_grupo.audit_programa; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario_grupo.audit_programa IS 'Programa que realizó la última modificación a la fila';


--
-- TOC entry 2417 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN usuario_grupo.audit_usuario; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario_grupo.audit_usuario IS 'Usuario que realizó la última modificación a la fila';


--
-- TOC entry 2418 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN usuario_grupo.audit_fechahora; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN usuario_grupo.audit_fechahora IS 'Fecha y hora en que se realizó la última modificación a la fila';


--
-- TOC entry 1943 (class 2604 OID 17764)
-- Dependencies: 1589 1588
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE producto_categoria ALTER COLUMN id SET DEFAULT nextval('producto_categoria_id_seq'::regclass);


--
-- TOC entry 1975 (class 2604 OID 17765)
-- Dependencies: 1599 1598
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE unidad ALTER COLUMN id SET DEFAULT nextval('unidad_id_seq'::regclass);


--
-- TOC entry 2014 (class 2606 OID 17767)
-- Dependencies: 1566 1566
-- Name: actividad_economica_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY actividad_economica
    ADD CONSTRAINT actividad_economica_pkey PRIMARY KEY (id);


--
-- TOC entry 2016 (class 2606 OID 17769)
-- Dependencies: 1567 1567
-- Name: afp_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY afp
    ADD CONSTRAINT afp_pkey PRIMARY KEY (id);


--
-- TOC entry 2018 (class 2606 OID 17771)
-- Dependencies: 1569 1569
-- Name: area_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY area
    ADD CONSTRAINT area_pkey PRIMARY KEY (id);


--
-- TOC entry 2020 (class 2606 OID 17773)
-- Dependencies: 1570 1570
-- Name: bodega_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY bodega
    ADD CONSTRAINT bodega_pkey PRIMARY KEY (id);


--
-- TOC entry 2023 (class 2606 OID 17775)
-- Dependencies: 1572 1572
-- Name: cargo_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargo_pkey PRIMARY KEY (id);


--
-- TOC entry 2025 (class 2606 OID 17777)
-- Dependencies: 1573 1573
-- Name: cliente_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id);


--
-- TOC entry 2027 (class 2606 OID 17779)
-- Dependencies: 1574 1574
-- Name: comuna_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY comuna
    ADD CONSTRAINT comuna_pkey PRIMARY KEY (id);


--
-- TOC entry 2033 (class 2606 OID 17781)
-- Dependencies: 1577 1577
-- Name: enlace_categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY enlace_categoria
    ADD CONSTRAINT enlace_categoria_pkey PRIMARY KEY (id);


--
-- TOC entry 2031 (class 2606 OID 17783)
-- Dependencies: 1575 1575
-- Name: enlace_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY enlace
    ADD CONSTRAINT enlace_pkey PRIMARY KEY (url);


--
-- TOC entry 2035 (class 2606 OID 17785)
-- Dependencies: 1578 1578 1578
-- Name: enlace_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY enlace_usuario
    ADD CONSTRAINT enlace_usuario_pkey PRIMARY KEY (usuario_id, url);


--
-- TOC entry 2092 (class 2606 OID 18590)
-- Dependencies: 1604 1604 1604 1604
-- Name: feriado_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY feriado
    ADD CONSTRAINT feriado_pkey PRIMARY KEY (anio, mes, dia);


--
-- TOC entry 2088 (class 2606 OID 18527)
-- Dependencies: 1602 1602
-- Name: geoposicionamiento_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY geoposicionamiento
    ADD CONSTRAINT geoposicionamiento_pkey PRIMARY KEY (id);


--
-- TOC entry 2038 (class 2606 OID 17787)
-- Dependencies: 1580 1580
-- Name: grupo_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY grupo
    ADD CONSTRAINT grupo_pkey PRIMARY KEY (id);


--
-- TOC entry 2040 (class 2606 OID 17789)
-- Dependencies: 1581 1581
-- Name: modulo_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modulo
    ADD CONSTRAINT modulo_pkey PRIMARY KEY (nombre);


--
-- TOC entry 2042 (class 2606 OID 17791)
-- Dependencies: 1583 1583
-- Name: noticia_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY noticia
    ADD CONSTRAINT noticia_pkey PRIMARY KEY (id);


--
-- TOC entry 2046 (class 2606 OID 17793)
-- Dependencies: 1584 1584
-- Name: parametro_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY parametro
    ADD CONSTRAINT parametro_pkey PRIMARY KEY (parametro);


--
-- TOC entry 2051 (class 2606 OID 17795)
-- Dependencies: 1586 1586
-- Name: permiso_login_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY permiso_login
    ADD CONSTRAINT permiso_login_pkey PRIMARY KEY (recurso);


--
-- TOC entry 2049 (class 2606 OID 17797)
-- Dependencies: 1585 1585 1585
-- Name: permiso_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY permiso
    ADD CONSTRAINT permiso_pkey PRIMARY KEY (recurso, grupo_id);


--
-- TOC entry 2055 (class 2606 OID 17799)
-- Dependencies: 1588 1588
-- Name: producto_categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY producto_categoria
    ADD CONSTRAINT producto_categoria_pkey PRIMARY KEY (id);


--
-- TOC entry 2053 (class 2606 OID 17801)
-- Dependencies: 1587 1587
-- Name: producto_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id);


--
-- TOC entry 2057 (class 2606 OID 17803)
-- Dependencies: 1590 1590 1590
-- Name: producto_proveedor_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY producto_proveedor
    ADD CONSTRAINT producto_proveedor_pkey PRIMARY KEY (producto_id, proveedor_id);


--
-- TOC entry 2059 (class 2606 OID 17805)
-- Dependencies: 1591 1591
-- Name: proveedor_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id);


--
-- TOC entry 2061 (class 2606 OID 17807)
-- Dependencies: 1592 1592
-- Name: region_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY region
    ADD CONSTRAINT region_pkey PRIMARY KEY (id);


--
-- TOC entry 2063 (class 2606 OID 17809)
-- Dependencies: 1593 1593
-- Name: salud_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY salud
    ADD CONSTRAINT salud_pkey PRIMARY KEY (id);


--
-- TOC entry 2067 (class 2606 OID 17811)
-- Dependencies: 1595 1595 1595 1595
-- Name: stock_nivel_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY stock_nivel
    ADD CONSTRAINT stock_nivel_pkey PRIMARY KEY (producto_id, bodega_id, area_id);


--
-- TOC entry 2065 (class 2606 OID 17813)
-- Dependencies: 1594 1594 1594 1594
-- Name: stock_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY stock
    ADD CONSTRAINT stock_pkey PRIMARY KEY (producto_id, bodega_id, area_id);


--
-- TOC entry 2070 (class 2606 OID 17815)
-- Dependencies: 1596 1596
-- Name: sucursal_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sucursal
    ADD CONSTRAINT sucursal_pkey PRIMARY KEY (id);


--
-- TOC entry 2090 (class 2606 OID 18570)
-- Dependencies: 1603 1603
-- Name: transportista_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY transportista
    ADD CONSTRAINT transportista_pkey PRIMARY KEY (id);


--
-- TOC entry 2073 (class 2606 OID 17817)
-- Dependencies: 1597 1597
-- Name: uf_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY uf
    ADD CONSTRAINT uf_pkey PRIMARY KEY (fecha);


--
-- TOC entry 2075 (class 2606 OID 17819)
-- Dependencies: 1598 1598
-- Name: unidad_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY unidad
    ADD CONSTRAINT unidad_pkey PRIMARY KEY (id);


--
-- TOC entry 2085 (class 2606 OID 17821)
-- Dependencies: 1601 1601 1601
-- Name: usuario_grupo_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT usuario_grupo_pkey PRIMARY KEY (grupo_id, usuario_id);


--
-- TOC entry 2079 (class 2606 OID 17823)
-- Dependencies: 1600 1600
-- Name: usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id);


--
-- TOC entry 2083 (class 2606 OID 17825)
-- Dependencies: 1600 1600
-- Name: usuario_usuario_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_usuario_key UNIQUE (usuario);


--
-- TOC entry 2021 (class 1259 OID 17826)
-- Dependencies: 1572
-- Name: cargo_area_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX cargo_area_id_idx ON cargo USING btree (area_id);


--
-- TOC entry 2028 (class 1259 OID 17827)
-- Dependencies: 1574
-- Name: comuna_region_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX comuna_region_id_idx ON comuna USING btree (region_id);


--
-- TOC entry 2029 (class 1259 OID 17828)
-- Dependencies: 1575
-- Name: enlace_enlace_categoria_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX enlace_enlace_categoria_id_idx ON enlace USING btree (enlace_categoria_id);


--
-- TOC entry 2036 (class 1259 OID 17829)
-- Dependencies: 1578
-- Name: enlace_usuario_usuario_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX enlace_usuario_usuario_id_idx ON enlace_usuario USING btree (usuario_id);


--
-- TOC entry 2043 (class 1259 OID 17830)
-- Dependencies: 1583
-- Name: noticia_usuario_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX noticia_usuario_id_idx ON noticia USING btree (usuario_id);


--
-- TOC entry 2044 (class 1259 OID 17831)
-- Dependencies: 1584
-- Name: parametro_modulo_nombre_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX parametro_modulo_nombre_idx ON parametro USING btree (modulo_nombre);


--
-- TOC entry 2047 (class 1259 OID 17832)
-- Dependencies: 1585
-- Name: permiso_grupo_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX permiso_grupo_id_idx ON permiso USING btree (grupo_id);


--
-- TOC entry 2068 (class 1259 OID 17833)
-- Dependencies: 1596
-- Name: sucursal_comuna_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX sucursal_comuna_id_idx ON sucursal USING btree (comuna_id);


--
-- TOC entry 2071 (class 1259 OID 17834)
-- Dependencies: 1596
-- Name: sucursal_usuario_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX sucursal_usuario_id_idx ON sucursal USING btree (usuario_id);


--
-- TOC entry 2076 (class 1259 OID 17835)
-- Dependencies: 1600
-- Name: usuario_afp_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX usuario_afp_id_idx ON usuario USING btree (afp_id);


--
-- TOC entry 2077 (class 1259 OID 17836)
-- Dependencies: 1600
-- Name: usuario_cargo_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX usuario_cargo_id_idx ON usuario USING btree (cargo_id);


--
-- TOC entry 2086 (class 1259 OID 17837)
-- Dependencies: 1601
-- Name: usuario_grupo_usuario_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX usuario_grupo_usuario_id_idx ON usuario_grupo USING btree (usuario_id);


--
-- TOC entry 2080 (class 1259 OID 17838)
-- Dependencies: 1600
-- Name: usuario_salud_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX usuario_salud_id_idx ON usuario USING btree (salud_id);


--
-- TOC entry 2081 (class 1259 OID 17839)
-- Dependencies: 1600
-- Name: usuario_sucursal_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX usuario_sucursal_id_idx ON usuario USING btree (sucursal_id);


--
-- TOC entry 2093 (class 2606 OID 17840)
-- Dependencies: 1596 1570 2069
-- Name: bodega_sucursal_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bodega
    ADD CONSTRAINT bodega_sucursal_id_fkey FOREIGN KEY (sucursal_id) REFERENCES sucursal(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2094 (class 2606 OID 17845)
-- Dependencies: 1570 2078 1600
-- Name: bodega_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY bodega
    ADD CONSTRAINT bodega_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2095 (class 2606 OID 17850)
-- Dependencies: 1572 2017 1569
-- Name: cargo_area_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargo_area_id_fkey FOREIGN KEY (area_id) REFERENCES area(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2096 (class 2606 OID 17855)
-- Dependencies: 1573 1566 2013
-- Name: cliente_actividad_economica_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_actividad_economica_id_fkey FOREIGN KEY (actividad_economica_id) REFERENCES actividad_economica(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2097 (class 2606 OID 17860)
-- Dependencies: 1573 1574 2026
-- Name: cliente_comuna_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cliente
    ADD CONSTRAINT cliente_comuna_id_fkey FOREIGN KEY (comuna_id) REFERENCES comuna(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2098 (class 2606 OID 17865)
-- Dependencies: 1592 2060 1574
-- Name: comuna_region_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY comuna
    ADD CONSTRAINT comuna_region_id_fkey FOREIGN KEY (region_id) REFERENCES region(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2099 (class 2606 OID 17870)
-- Dependencies: 1577 1575 2032
-- Name: enlace_enlace_categoria_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY enlace
    ADD CONSTRAINT enlace_enlace_categoria_id_fkey FOREIGN KEY (enlace_categoria_id) REFERENCES enlace_categoria(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2100 (class 2606 OID 17875)
-- Dependencies: 2078 1578 1600
-- Name: enlace_usuario_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY enlace_usuario
    ADD CONSTRAINT enlace_usuario_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2101 (class 2606 OID 17880)
-- Dependencies: 1600 1583 2078
-- Name: noticia_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY noticia
    ADD CONSTRAINT noticia_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2102 (class 2606 OID 17885)
-- Dependencies: 1581 1584 2039
-- Name: parametro_modulo_nombre_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY parametro
    ADD CONSTRAINT parametro_modulo_nombre_fkey FOREIGN KEY (modulo_nombre) REFERENCES modulo(nombre) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2103 (class 2606 OID 17890)
-- Dependencies: 2037 1580 1585
-- Name: permiso_grupo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY permiso
    ADD CONSTRAINT permiso_grupo_id_fkey FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2106 (class 2606 OID 17895)
-- Dependencies: 2054 1588 1588
-- Name: producto_categoria_producto_categoria_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY producto_categoria
    ADD CONSTRAINT producto_categoria_producto_categoria_id_fkey FOREIGN KEY (producto_categoria_id) REFERENCES producto_categoria(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2104 (class 2606 OID 17900)
-- Dependencies: 1588 2054 1587
-- Name: producto_producto_categoria_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY producto
    ADD CONSTRAINT producto_producto_categoria_id_fkey FOREIGN KEY (producto_categoria_id) REFERENCES producto_categoria(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2107 (class 2606 OID 17905)
-- Dependencies: 1590 1587 2052
-- Name: producto_proveedor_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY producto_proveedor
    ADD CONSTRAINT producto_proveedor_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES producto(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2108 (class 2606 OID 17910)
-- Dependencies: 1591 2058 1590
-- Name: producto_proveedor_proveedor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY producto_proveedor
    ADD CONSTRAINT producto_proveedor_proveedor_id_fkey FOREIGN KEY (proveedor_id) REFERENCES proveedor(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2105 (class 2606 OID 17915)
-- Dependencies: 1587 2074 1598
-- Name: producto_unidad_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY producto
    ADD CONSTRAINT producto_unidad_id_fkey FOREIGN KEY (unidad_id) REFERENCES unidad(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2109 (class 2606 OID 17920)
-- Dependencies: 1566 2013 1591
-- Name: proveedor_actividad_economica_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY proveedor
    ADD CONSTRAINT proveedor_actividad_economica_id_fkey FOREIGN KEY (actividad_economica_id) REFERENCES actividad_economica(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2110 (class 2606 OID 17925)
-- Dependencies: 2026 1574 1591
-- Name: proveedor_comuna_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY proveedor
    ADD CONSTRAINT proveedor_comuna_id_fkey FOREIGN KEY (comuna_id) REFERENCES comuna(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2111 (class 2606 OID 17930)
-- Dependencies: 1594 2017 1569
-- Name: stock_area_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock
    ADD CONSTRAINT stock_area_id_fkey FOREIGN KEY (area_id) REFERENCES area(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2112 (class 2606 OID 17935)
-- Dependencies: 1594 2019 1570
-- Name: stock_bodega_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock
    ADD CONSTRAINT stock_bodega_id_fkey FOREIGN KEY (bodega_id) REFERENCES bodega(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2114 (class 2606 OID 17940)
-- Dependencies: 2017 1569 1595
-- Name: stock_nivel_area_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock_nivel
    ADD CONSTRAINT stock_nivel_area_id_fkey FOREIGN KEY (area_id) REFERENCES area(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2115 (class 2606 OID 17945)
-- Dependencies: 1595 2019 1570
-- Name: stock_nivel_bodega_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock_nivel
    ADD CONSTRAINT stock_nivel_bodega_id_fkey FOREIGN KEY (bodega_id) REFERENCES bodega(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2116 (class 2606 OID 17950)
-- Dependencies: 1587 1595 2052
-- Name: stock_nivel_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock_nivel
    ADD CONSTRAINT stock_nivel_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES producto(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2113 (class 2606 OID 17955)
-- Dependencies: 2052 1594 1587
-- Name: stock_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY stock
    ADD CONSTRAINT stock_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES producto(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2117 (class 2606 OID 17960)
-- Dependencies: 2026 1574 1596
-- Name: sucursal_comuna_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sucursal
    ADD CONSTRAINT sucursal_comuna_id_fkey FOREIGN KEY (comuna_id) REFERENCES comuna(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2118 (class 2606 OID 18606)
-- Dependencies: 2078 1596 1600
-- Name: sucursal_usuario_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sucursal
    ADD CONSTRAINT sucursal_usuario_id_fk FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2119 (class 2606 OID 17965)
-- Dependencies: 1567 1600 2015
-- Name: usuario_afp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_afp_id_fkey FOREIGN KEY (afp_id) REFERENCES afp(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2120 (class 2606 OID 17970)
-- Dependencies: 1600 1572 2022
-- Name: usuario_cargo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_cargo_id_fkey FOREIGN KEY (cargo_id) REFERENCES cargo(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2123 (class 2606 OID 17975)
-- Dependencies: 1580 2037 1601
-- Name: usuario_grupo_grupo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT usuario_grupo_grupo_id_fkey FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2124 (class 2606 OID 17980)
-- Dependencies: 1601 2078 1600
-- Name: usuario_grupo_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario_grupo
    ADD CONSTRAINT usuario_grupo_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2121 (class 2606 OID 17985)
-- Dependencies: 2062 1600 1593
-- Name: usuario_salud_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_salud_id_fkey FOREIGN KEY (salud_id) REFERENCES salud(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2122 (class 2606 OID 17990)
-- Dependencies: 1600 2069 1596
-- Name: usuario_sucursal_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT usuario_sucursal_id_fkey FOREIGN KEY (sucursal_id) REFERENCES sucursal(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2129 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2011-05-15 23:58:56 CLT

--
-- PostgreSQL database dump complete
--

